<?php
/**
 * Vendor customization form template
 */

if (!defined('ABSPATH')) {
    exit;
}

$tab_options = array(
    'services' => __('Services', 'vendor-tab-customizer'),
    'products' => __('Products', 'vendor-tab-customizer'),
    'procedures' => __('Procedures', 'vendor-tab-customizer'),
    'events' => __('Events', 'vendor-tab-customizer'),
    'news' => __('News', 'vendor-tab-customizer'),
    'webinars' => __('Webinars', 'vendor-tab-customizer'),
    'publications' => __('Publications', 'vendor-tab-customizer')
);
?>

<h2><?php printf(__('Customize Tabs for: %s', 'vendor-tab-customizer'), esc_html($vendor_name)); ?></h2>
<p>
    <strong><?php _e('Slug:', 'vendor-tab-customizer'); ?></strong> <code><?php echo esc_html($vendor_slug); ?></code> | 
    <a href="<?php echo esc_url(site_url('/channel/' . $vendor_slug . '/')); ?>" target="_blank" class="button button-small">
        <span class="dashicons dashicons-external" style="vertical-align: middle;"></span> <?php _e('View Channel', 'vendor-tab-customizer'); ?>
    </a>
</p>

<form method="post" action="">
    <?php wp_nonce_field('vendor_tab_customization'); ?>
    <input type="hidden" name="vendor_id" value="<?php echo esc_attr($vendor_id); ?>">
    
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Enable Tab Customization', 'vendor-tab-customizer'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="enable_customization" value="1" <?php checked($enabled, 1); ?>>
                    <?php _e('Enable custom tabs for this vendor', 'vendor-tab-customizer'); ?>
                </label>
                <p class="description"><?php _e('When enabled, this vendor\'s tab customizations will be applied to their channel page.', 'vendor-tab-customizer'); ?></p>
            </td>
        </tr>
    </table>
    
    <h3><?php _e('Tab Customizations', 'vendor-tab-customizer'); ?></h3>
    <button type="button" id="add-tab-customization" class="add-tab-btn">
        <?php _e('+ Add Tab Customization', 'vendor-tab-customizer'); ?>
    </button>
    
    <div id="tab-customizations-container">
        <?php if (empty($customizations)): ?>
            <p style="color: #666; font-style: italic;">
                <?php _e('No customizations yet. Click "Add Tab Customization" to get started.', 'vendor-tab-customizer'); ?>
            </p>
        <?php endif; ?>
        
        <?php foreach ($customizations as $index => $custom): ?>
            <div class="tab-customization-row">
                <div class="form-row">
                    <label><?php _e('Select Tab:', 'vendor-tab-customizer'); ?></label>
                    <select name="tab_customizations[<?php echo $index; ?>][tab_select]" required>
                        <option value=""><?php _e('-- Select Tab --', 'vendor-tab-customizer'); ?></option>
                        <?php foreach ($tab_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($custom['tab_select'], $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label><?php _e('Action:', 'vendor-tab-customizer'); ?></label>
                    <select name="tab_customizations[<?php echo $index; ?>][tab_action]" class="tab-action-select" required>
                        <option value="rename" <?php selected($custom['tab_action'], 'rename'); ?>><?php _e('Rename Tab', 'vendor-tab-customizer'); ?></option>
                        <option value="custom_content" <?php selected($custom['tab_action'], 'custom_content'); ?>><?php _e('Custom Content', 'vendor-tab-customizer'); ?></option>
                        <option value="hide" <?php selected($custom['tab_action'], 'hide'); ?>><?php _e('Hide Tab', 'vendor-tab-customizer'); ?></option>
                    </select>
                </div>
                <div class="form-row rename-field" style="display: <?php echo $custom['tab_action'] === 'rename' ? 'block' : 'none'; ?>;">
                    <label><?php _e('New Tab Name:', 'vendor-tab-customizer'); ?></label>
                    <input type="text" name="tab_customizations[<?php echo $index; ?>][tab_new_name]" 
                           value="<?php echo esc_attr($custom['tab_new_name'] ?? ''); ?>" 
                           placeholder="<?php esc_attr_e('Enter new tab name', 'vendor-tab-customizer'); ?>" />
                </div>
                <div class="form-row custom-content-field" style="display: <?php echo $custom['tab_action'] === 'custom_content' ? 'block' : 'none'; ?>;">
                    <label><?php _e('Custom HTML Content:', 'vendor-tab-customizer'); ?></label>
                    <textarea name="tab_customizations[<?php echo $index; ?>][tab_custom_content]" 
                              placeholder="<?php esc_attr_e('Enter your custom HTML content here...', 'vendor-tab-customizer'); ?>"><?php echo esc_textarea($custom['tab_custom_content'] ?? ''); ?></textarea>
                    <p class="description"><?php _e('You can use HTML tags here. This content will replace the default tab content.', 'vendor-tab-customizer'); ?></p>
                </div>
                <button type="button" class="remove-tab-btn"><?php _e('Remove', 'vendor-tab-customizer'); ?></button>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="custom-code-section">
        <h3><?php _e('Custom CSS', 'vendor-tab-customizer'); ?></h3>
        <p class="description"><?php _e('Add custom CSS for this vendor (without &lt;style&gt; tags)', 'vendor-tab-customizer'); ?></p>
        <textarea name="custom_css" placeholder="/* <?php esc_attr_e('Add your custom CSS here', 'vendor-tab-customizer'); ?> */"><?php echo esc_textarea($custom_css); ?></textarea>
        
        <h3><?php _e('Custom JavaScript', 'vendor-tab-customizer'); ?></h3>
        <p class="description"><?php _e('Add custom JavaScript for this vendor (without &lt;script&gt; tags, jQuery available as $)', 'vendor-tab-customizer'); ?></p>
        <textarea name="custom_js" placeholder="// <?php esc_attr_e('Add your custom JavaScript here', 'vendor-tab-customizer'); ?>
// <?php esc_attr_e('jQuery is available as $', 'vendor-tab-customizer'); ?>"><?php echo esc_textarea($custom_js); ?></textarea>
    </div>
    
    <p class="submit">
        <input type="submit" name="vendor_tab_submit" class="button button-primary button-large" 
               value="<?php esc_attr_e('💾 Save Customizations', 'vendor-tab-customizer'); ?>">
    </p>
</form>

<script type="text/html" id="tab-customization-template">
    <div class="tab-customization-row">
        <div class="form-row">
            <label><?php _e('Select Tab:', 'vendor-tab-customizer'); ?></label>
            <select name="tab_customizations[INDEX][tab_select]" required>
                <option value=""><?php _e('-- Select Tab --', 'vendor-tab-customizer'); ?></option>
                <?php foreach ($tab_options as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <label><?php _e('Action:', 'vendor-tab-customizer'); ?></label>
            <select name="tab_customizations[INDEX][tab_action]" class="tab-action-select" required>
                <option value="rename"><?php _e('Rename Tab', 'vendor-tab-customizer'); ?></option>
                <option value="custom_content"><?php _e('Custom Content', 'vendor-tab-customizer'); ?></option>
                <option value="hide"><?php _e('Hide Tab', 'vendor-tab-customizer'); ?></option>
            </select>
        </div>
        <div class="form-row rename-field">
            <label><?php _e('New Tab Name:', 'vendor-tab-customizer'); ?></label>
            <input type="text" name="tab_customizations[INDEX][tab_new_name]" 
                   placeholder="<?php esc_attr_e('Enter new tab name', 'vendor-tab-customizer'); ?>" />
        </div>
        <div class="form-row custom-content-field" style="display:none;">
            <label><?php _e('Custom HTML Content:', 'vendor-tab-customizer'); ?></label>
            <textarea name="tab_customizations[INDEX][tab_custom_content]" 
                      placeholder="<?php esc_attr_e('Enter your custom HTML content here...', 'vendor-tab-customizer'); ?>"></textarea>
        </div>
        <button type="button" class="remove-tab-btn"><?php _e('Remove', 'vendor-tab-customizer'); ?></button>
    </div>
</script>