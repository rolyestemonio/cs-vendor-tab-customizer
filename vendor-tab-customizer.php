<?php
/**
 * Plugin Name: Vendor Tab Customizer
 * Plugin URI: https://github.com/rolyestemonio/cs-vendor-tab-customizer
 * Description: Dynamic tab customization system for vendor channels with admin dashboard
 * Version: 1.0.0
 * Author: Roly Estemonio
 * Author URI: https://rolyestemonio.website
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vendor-tab-customizer
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VTC_VERSION', '1.0.0');
define('VTC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VTC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VTC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Vendor_Tab_Customizer {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Cache group name
     */
    const CACHE_GROUP = 'vendor_tab_customizer';
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once VTC_PLUGIN_DIR . 'includes/class-vtc-admin.php';
        require_once VTC_PLUGIN_DIR . 'includes/class-vtc-frontend.php';
        require_once VTC_PLUGIN_DIR . 'includes/class-vtc-cache.php';
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        
        // Activation & Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'vendor-tab-customizer',
            false,
            dirname(VTC_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize admin
        if (is_admin()) {
            VTC_Admin::get_instance();
        }
        
        // Initialize frontend
        VTC_Frontend::get_instance();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        if (!get_option('vtc_version')) {
            add_option('vtc_version', VTC_VERSION);
        }
        
        // Clear any existing cache
        VTC_Cache::clear_all_cache();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear all cache
        VTC_Cache::clear_all_cache();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function vtc_init() {
    return Vendor_Tab_Customizer::get_instance();
}

// Start the plugin
vtc_init();