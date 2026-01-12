<?php
/**
 * Frontend functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class VTC_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Frontend hooks can be added here if needed
    }
    
    /**
     * Get tab customization config with caching
     */
    public static function get_dynamic_tab_config($vendor_id) {
        $cache_key = 'vendor_config_' . $vendor_id;
        $config = VTC_Cache::get($cache_key);
        
        if (false !== $config) {
            return $config;
        }
        
        $enabled = get_user_meta($vendor_id, 'enable_tab_customization', true);
        
        if (!$enabled) {
            VTC_Cache::set($cache_key, array(), HOUR_IN_SECONDS);
            return array();
        }
        
        $customizations_raw = get_user_meta($vendor_id, 'tab_customizations', true);
        
        $config = array(
            'customizations' => array(),
            'hidden_tabs' => array(),
            'custom_css' => get_user_meta($vendor_id, 'custom_tab_css', true),
            'custom_js' => get_user_meta($vendor_id, 'custom_tab_js', true),
        );
        
        if (is_array($customizations_raw)) {
            foreach ($customizations_raw as $custom) {
                if (!isset($custom['tab_select'])) {
                    continue;
                }
                
                $tab_key = $custom['tab_select'];
                $action = $custom['tab_action'] ?? '';
                
                if ($action === 'hide') {
                    $config['hidden_tabs'][] = $tab_key;
                } elseif ($action === 'rename' && !empty($custom['tab_new_name'])) {
                    $config['customizations'][$tab_key]['rename'] = $custom['tab_new_name'];
                } elseif ($action === 'custom_content' && !empty($custom['tab_custom_content'])) {
                    $config['customizations'][$tab_key]['custom_content'] = $custom['tab_custom_content'];
                }
            }
        }
        
        VTC_Cache::set($cache_key, $config, HOUR_IN_SECONDS);
        
        return $config;
    }
    
    /**
     * Render tab menu with dynamic customizations
     */
    public static function render_dynamic_tab_menu($vendor_id, $has_services) {
        $config = self::get_dynamic_tab_config($vendor_id);
        $hidden_tabs = $config['hidden_tabs'] ?? array();
        
        $tabs = array(
            'services' => array('label' => __('Services', 'vendor-tab-customizer'), 'show_if' => $has_services),
            'products' => array('label' => __('Products', 'vendor-tab-customizer'), 'show_if' => !$has_services),
            'procedures' => array('label' => __('Procedures', 'vendor-tab-customizer'), 'show_if' => true),
            'events' => array('label' => __('Events', 'vendor-tab-customizer'), 'show_if' => true),
            'news' => array('label' => __('News', 'vendor-tab-customizer'), 'show_if' => true),
            'webinars' => array('label' => __('Webinars', 'vendor-tab-customizer'), 'show_if' => true),
            'publications' => array('label' => __('Publications', 'vendor-tab-customizer'), 'show_if' => true)
        );
        
        $first_visible = true;
        foreach ($tabs as $key => $tab) {
            if (in_array($key, $hidden_tabs) || !$tab['show_if']) {
                continue;
            }
            
            $label = $tab['label'];
            if (isset($config['customizations'][$key]['rename'])) {
                $label = $config['customizations'][$key]['rename'];
            }
            
            $active_class = $first_visible ? 'active' : '';
            echo '<li class="tab-link ' . esc_attr($active_class) . '" data-tab="tab-' . esc_attr($key) . '">' . esc_html($label) . '</li>';
            $first_visible = false;
        }
    }
    
    /**
     * Render tab content with custom content support
     */
    public static function render_dynamic_tab_content($tab_key, $vendor_id, $default_content_callback) {
        $config = self::get_dynamic_tab_config($vendor_id);
        
        if (isset($config['customizations'][$tab_key]['custom_content'])) {
            $custom_content = $config['customizations'][$tab_key]['custom_content'];
            if (!empty($custom_content)) {
                echo wp_kses_post($custom_content);
                return true;
            }
        }
        
        if (is_callable($default_content_callback)) {
            call_user_func($default_content_callback);
            return false;
        }
        
        return false;
    }
    
    /**
     * Output custom CSS and JS
     */
    public static function output_channel_custom_styles($vendor_id) {
        $config = self::get_dynamic_tab_config($vendor_id);
        
        if (!empty($config['custom_css'])) {
            echo '<style>' . esc_html($config['custom_css']) . '</style>';
        }
        
        if (!empty($config['custom_js'])) {
            echo '<script>jQuery(document).ready(function($) {' . "\n" . $config['custom_js'] . "\n" . '});</script>';
        }
    }
    
    /**
     * Check if tab should be hidden
     */
    public static function is_tab_hidden($tab_key, $vendor_id) {
        $config = self::get_dynamic_tab_config($vendor_id);
        $hidden_tabs = $config['hidden_tabs'] ?? array();
        return in_array($tab_key, $hidden_tabs);
    }
}

// Global helper functions for backward compatibility
if (!function_exists('render_dynamic_tab_menu')) {
    function render_dynamic_tab_menu($vendor_id, $has_services) {
        VTC_Frontend::render_dynamic_tab_menu($vendor_id, $has_services);
    }
}

if (!function_exists('render_dynamic_tab_content')) {
    function render_dynamic_tab_content($tab_key, $vendor_id, $default_content_callback) {
        return VTC_Frontend::render_dynamic_tab_content($tab_key, $vendor_id, $default_content_callback);
    }
}

if (!function_exists('output_channel_custom_styles')) {
    function output_channel_custom_styles($vendor_id) {
        VTC_Frontend::output_channel_custom_styles($vendor_id);
    }
}

if (!function_exists('is_tab_hidden')) {
    function is_tab_hidden($tab_key, $vendor_id) {
        return VTC_Frontend::is_tab_hidden($tab_key, $vendor_id);
    }
}

if (!function_exists('get_dynamic_tab_config')) {
    function get_dynamic_tab_config($vendor_id) {
        return VTC_Frontend::get_dynamic_tab_config($vendor_id);
    }
}