<?php
/**
 * Admin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class VTC_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Vendor Tab Management', 'vendor-tab-customizer'),
            __('Vendor Tabs', 'vendor-tab-customizer'),
            'manage_options',
            'vendor-tab-management',
            array($this, 'render_admin_page'),
            'dashicons-admin-customizer',
            30
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_vendor-tab-management' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'vtc-admin-style',
            VTC_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            VTC_VERSION
        );
        
        wp_enqueue_script(
            'vtc-admin-script',
            VTC_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            VTC_VERSION,
            true
        );
    }
    
    /**
     * Get all vendors with caching
     */
    public function get_all_vendors() {
        $cache_key = 'all_vendors_list';
        $vendors = VTC_Cache::get($cache_key);
        
        if (false !== $vendors) {
            return $vendors;
        }
        
        global $wpdb;
        
        $vendor_ids = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT um.user_id 
                FROM {$wpdb->usermeta} um
                WHERE um.meta_key = %s
                AND um.user_id IN (
                    SELECT user_id FROM {$wpdb->usermeta} 
                    WHERE meta_key = %s
                    AND meta_value LIKE %s
                )",
                'mepr_business_slug',
                $wpdb->prefix . 'capabilities',
                '%vendor%'
            )
        );
        
        $vendors = array();
        foreach ($vendor_ids as $vendor) {
            $user_id = $vendor->user_id;
            $user_data = get_userdata($user_id);
            
            if ($user_data) {
                $brand_name = get_user_meta($user_id, 'mepr_brand_name', true);
                $business_slug = get_user_meta($user_id, 'mepr_business_slug', true);
                
                if (empty($brand_name)) {
                    $brand_name = $user_data->display_name ?: $user_data->user_login;
                }
                
                if (empty($business_slug)) {
                    continue;
                }
                
                $vendors[] = array(
                    'id' => $user_id,
                    'name' => $brand_name,
                    'slug' => $business_slug,
                    'email' => $user_data->user_email,
                );
            }
        }
        
        usort($vendors, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        VTC_Cache::set($cache_key, $vendors, HOUR_IN_SECONDS);
        
        return $vendors;
    }
    
    /**
     * Handle form submission
     */
    private function handle_form_submission() {
        if (!isset($_POST['vendor_tab_submit']) || !check_admin_referer('vendor_tab_customization')) {
            return false;
        }
        
        $vendor_id = intval($_POST['vendor_id']);
        
        if ($vendor_id <= 0) {
            return false;
        }
        
        // Save customization enabled
        $enabled = isset($_POST['enable_customization']) ? 1 : 0;
        update_user_meta($vendor_id, 'enable_tab_customization', $enabled);
        
        // Save tab customizations
        $customizations = array();
        if (isset($_POST['tab_customizations']) && is_array($_POST['tab_customizations'])) {
            foreach ($_POST['tab_customizations'] as $custom) {
                if (!empty($custom['tab_select'])) {
                    $customizations[] = array(
                        'tab_select' => sanitize_text_field($custom['tab_select']),
                        'tab_action' => sanitize_text_field($custom['tab_action']),
                        'tab_new_name' => isset($custom['tab_new_name']) ? sanitize_text_field($custom['tab_new_name']) : '',
                        'tab_custom_content' => isset($custom['tab_custom_content']) ? wp_kses_post($custom['tab_custom_content']) : '',
                    );
                }
            }
        }
        
        delete_user_meta($vendor_id, 'tab_customizations');
        if (!empty($customizations)) {
            update_user_meta($vendor_id, 'tab_customizations', $customizations);
        }
        
        // Save custom CSS
        $custom_css = isset($_POST['custom_css']) ? sanitize_textarea_field($_POST['custom_css']) : '';
        update_user_meta($vendor_id, 'custom_tab_css', $custom_css);
        
        // Save custom JS (preserve HTML in strings)
        $custom_js = isset($_POST['custom_js']) ? wp_unslash($_POST['custom_js']) : '';
        update_user_meta($vendor_id, 'custom_tab_js', $custom_js);
        
        // Clear cache for this vendor
        VTC_Cache::delete('vendor_config_' . $vendor_id);
        VTC_Cache::delete('all_vendors_list');
        
        return true;
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $saved = $this->handle_form_submission();
        $vendors = $this->get_all_vendors();
        $selected_vendor = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : null;
        
        include VTC_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Render vendor customization form
     */
    public function render_vendor_form($vendor_id) {
        $vendor_name = get_user_meta($vendor_id, 'mepr_brand_name', true);
        $vendor_slug = get_user_meta($vendor_id, 'mepr_business_slug', true);
        $enabled = get_user_meta($vendor_id, 'enable_tab_customization', true);
        $customizations = get_user_meta($vendor_id, 'tab_customizations', true);
        $custom_css = get_user_meta($vendor_id, 'custom_tab_css', true);
        $custom_js = get_user_meta($vendor_id, 'custom_tab_js', true);
        
        if (!is_array($customizations)) {
            $customizations = array();
        }
        
        include VTC_PLUGIN_DIR . 'templates/vendor-form.php';
    }
}