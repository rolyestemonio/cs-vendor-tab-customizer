<?php
/**
 * Cache management class
 */

if (!defined('ABSPATH')) {
    exit;
}

class VTC_Cache {
    
    const CACHE_GROUP = 'vendor_tab_customizer';
    
    /**
     * Get cached data
     */
    public static function get($key) {
        return wp_cache_get($key, self::CACHE_GROUP);
    }
    
    /**
     * Set cached data
     */
    public static function set($key, $data, $expiration = 3600) {
        return wp_cache_set($key, $data, self::CACHE_GROUP, $expiration);
    }
    
    /**
     * Delete cached data
     */
    public static function delete($key) {
        return wp_cache_delete($key, self::CACHE_GROUP);
    }
    
    /**
     * Clear all plugin cache
     */
    public static function clear_all_cache() {
        global $wpdb;
        
        // Get all vendors to clear their individual caches
        $vendor_ids = $wpdb->get_col(
            "SELECT DISTINCT user_id 
            FROM {$wpdb->usermeta} 
            WHERE meta_key = 'mepr_business_slug'"
        );
        
        foreach ($vendor_ids as $vendor_id) {
            self::delete('vendor_config_' . $vendor_id);
        }
        
        self::delete('all_vendors_list');
        
        return true;
    }
    
    /**
     * Clear vendor-specific cache
     */
    public static function clear_vendor_cache($vendor_id) {
        self::delete('vendor_config_' . $vendor_id);
        self::delete('all_vendors_list');
    }
}