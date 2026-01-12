# Vendor Tab Customizer

A WordPress plugin for dynamic tab customization system with admin dashboard for vendor channels.

## Author
**Roly Estemonio**  
[GitHub Repository](https://github.com/linkhub)

## Description

The Vendor Tab Customizer plugin allows administrators to customize vendor channel tabs with ease. Features include:

- **Rename Tabs**: Change tab labels for specific vendors
- **Custom Content**: Replace default tab content with custom HTML
- **Hide Tabs**: Selectively hide tabs from vendor channels
- **Custom CSS/JS**: Add vendor-specific styling and scripts
- **Performance Optimized**: Built-in caching system to prevent server overload
- **User-Friendly Interface**: Intuitive admin dashboard with search functionality

## Features

✅ **Dynamic Tab Management**
- Rename any tab (Services, Products, Procedures, Events, News, Webinars, Publications)
- Add custom HTML content to tabs
- Hide unwanted tabs per vendor

✅ **Custom Styling**
- Add vendor-specific CSS without modifying theme files
- Inject custom JavaScript with jQuery support

✅ **Performance Optimized**
- Object caching integration
- Prepared SQL statements for security
- Minimal database queries with intelligent caching
- Cache expiration: 1 hour (configurable)

✅ **Admin Dashboard**
- Clean, modern interface
- Real-time vendor search
- One-click vendor selection
- Preview channel links

## Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin zip file
2. Navigate to **Plugins > Add New > Upload Plugin**
3. Choose the downloaded zip file
4. Click **Install Now**
5. Activate the plugin

### Method 2: Manual Installation

1. Upload the `vendor-tab-customizer` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress

### Method 3: GitHub Clone

```bash
cd wp-content/plugins/
git clone https://github.com/linkhub/vendor-tab-customizer.git
```

Then activate through WordPress admin.

## Usage

### Access Admin Dashboard

1. Navigate to **Vendor Tabs** in WordPress admin menu
2. Search or select a vendor from the list
3. Enable tab customization for the vendor
4. Add customizations as needed
5. Click **Save Customizations**

### Tab Actions

**Rename Tab**
- Select the tab to rename
- Choose "Rename Tab" action
- Enter the new tab name

**Custom Content**
- Select the tab
- Choose "Custom Content" action
- Add your custom HTML content

**Hide Tab**
- Select the tab
- Choose "Hide Tab" action
- Tab will be hidden on the vendor channel

### Adding Custom CSS

```css
/* Example: Change tab colors */
.tab-link {
    background: #your-color;
}

.tab-link.active {
    background: #your-active-color;
}
```

### Adding Custom JavaScript

```javascript
// Example: Replace vendor name with logo
jQuery(document).ready(function ($) {
    const logoHTML = `
        <img 
            src="https://example.com/logo.png" 
            alt="Vendor Logo" 
            width="450"
        />
    `;
    $('h1.vendor-name').each(function () {
        if ($(this).text().trim() === 'Vendor Name') {
            $(this).replaceWith(logoHTML);
        }
    });
});
```

## Integration with Theme

The plugin provides global helper functions for easy integration:

### In your theme's `sc_vendor_channel.php`:

**1. Replace static tab menu:**

```php
// OLD CODE:
<ul class="tab-menu">
    <li class="tab-link active">Services</li>
    <li class="tab-link">Products</li>
    <!-- etc -->
</ul>

// NEW CODE:
<ul class="tab-menu">
    <?php render_dynamic_tab_menu($vendorID->user_id, $has_services); ?>
</ul>
```

**2. Wrap tab content:**

```php
// OLD CODE:
<div class="tab-content" id="tab-services">
    <?php echo $services_html; ?>
</div>

// NEW CODE:
<?php if (!is_tab_hidden('services', $vendorID->user_id)): ?>
<div class="tab-content" id="tab-services">
    <?php 
    render_dynamic_tab_content('services', $vendorID->user_id, function() use ($services_html) {
        echo $services_html;
    });
    ?>
</div>
<?php endif; ?>
```

**3. Output custom styles (at the end):**

```php
<?php 
// Before closing tag
output_channel_custom_styles($vendorID->user_id); 
?>
```

## File Structure

```
vendor-tab-customizer/
├── vendor-tab-customizer.php    # Main plugin file
├── README.md                     # Documentation
├── includes/
│   ├── class-vtc-admin.php      # Admin functionality
│   ├── class-vtc-frontend.php   # Frontend functionality
│   └── class-vtc-cache.php      # Cache management
├── templates/
│   ├── admin-page.php            # Admin page template
│   └── vendor-form.php           # Vendor form template
└── assets/
    ├── css/
    │   └── admin-style.css       # Admin styles
    └── js/
        └── admin-script.js       # Admin JavaScript
```

## Performance Optimization

### Caching Strategy

- **Vendor list cache**: 1 hour
- **Vendor config cache**: 1 hour  
- **Auto-clear**: Cache cleared on customization save
- **Cache group**: `vendor_tab_customizer`

### Database Optimization

- Prepared statements for all queries
- Minimal query execution
- Indexed user meta lookups
- Efficient vendor filtering

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Frequently Asked Questions

**Q: Will this affect site performance?**  
A: No, the plugin uses intelligent caching to minimize database queries and prevent server overload.

**Q: Can I customize multiple tabs for one vendor?**  
A: Yes, you can add multiple customizations for different tabs.

**Q: Does it work with any theme?**  
A: The plugin provides helper functions that need to be integrated into your theme's vendor channel template.

**Q: Can I revert changes?**  
A: Yes, simply remove the customization or disable tab customization for the vendor.

**Q: Is the custom JavaScript safe?**  
A: The plugin allows JavaScript for flexibility. Only trusted administrators should have access.

## Changelog

### Version 1.0.0
- Initial release
- Dynamic tab customization
- Custom CSS/JS support
- Performance optimization with caching
- Admin dashboard with search
- Translation ready

## Support

For issues, questions, or contributions:
- GitHub: [https://github.com/linkhub](https://github.com/linkhub)

## License

GPL v2 or later - [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Credits

Developed by **Roly Estemonio**