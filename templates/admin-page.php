<?php
/**
 * Admin page template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vtc-admin-wrap">
    <h1>
        <span class="dashicons dashicons-admin-customizer"></span> 
        <?php _e('Vendor Tab Management', 'vendor-tab-customizer'); ?>
    </h1>
    <p><?php _e('Customize tabs, content, and styling for vendor channels.', 'vendor-tab-customizer'); ?></p>
    
    <?php if ($saved): ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php _e('Success!', 'vendor-tab-customizer'); ?></strong> <?php _e('Vendor tab customization saved successfully!', 'vendor-tab-customizer'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="vendor-management-container">
        <div class="vendor-list-panel">
            <h2><?php printf(__('Vendors (%d)', 'vendor-tab-customizer'), count($vendors)); ?></h2>
            <div class="vendor-search">
                <input type="text" id="vendor-search" placeholder="<?php esc_attr_e('🔍 Search vendors...', 'vendor-tab-customizer'); ?>" />
            </div>
            <ul class="vendor-list">
                <?php if (empty($vendors)): ?>
                    <li class="no-vendors"><?php _e('No vendors found', 'vendor-tab-customizer'); ?></li>
                <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <li class="vendor-item <?php echo $selected_vendor == $vendor['id'] ? 'active' : ''; ?>">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=vendor-tab-management&vendor_id=' . $vendor['id'])); ?>">
                                <strong><?php echo esc_html($vendor['name']); ?></strong>
                                <span class="vendor-slug"><?php echo esc_html($vendor['slug']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="vendor-customization-panel">
            <?php if ($selected_vendor): ?>
                <?php $this->render_vendor_form($selected_vendor); ?>
            <?php else: ?>
                <div class="no-vendor-selected">
                    <span class="dashicons dashicons-arrow-left-alt2" style="font-size: 48px; color: #ccc;"></span>
                    <h2><?php _e('Select a vendor from the list to customize their tabs', 'vendor-tab-customizer'); ?></h2>
                    <p><?php _e('You can rename tabs, add custom content, hide tabs, and add custom CSS/JS.', 'vendor-tab-customizer'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>