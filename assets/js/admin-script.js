jQuery(document).ready(function($) {
    'use strict';
    
    // Vendor search functionality
    $('#vendor-search').on('keyup', function() {
        var search = $(this).val().toLowerCase();
        $('.vendor-item').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(search) > -1);
        });
    });
    
    // Add new tab customization
    $('#add-tab-customization').on('click', function() {
        var template = $('#tab-customization-template').html();
        var index = $('.tab-customization-row').length;
        
        // Replace INDEX placeholder with actual index
        var html = template.replace(/INDEX/g, index);
        
        $('#tab-customizations-container').append(html);
        
        // Remove "no customizations" message if it exists
        $('#tab-customizations-container p').first().remove();
    });
    
    // Remove tab customization with confirmation
    $(document).on('click', '.remove-tab-btn', function() {
        if (confirm('Are you sure you want to remove this customization?')) {
            $(this).closest('.tab-customization-row').remove();
            
            // Show "no customizations" message if all removed
            if ($('.tab-customization-row').length === 0) {
                $('#tab-customizations-container').html(
                    '<p style="color: #666; font-style: italic;">No customizations yet. Click "Add Tab Customization" to get started.</p>'
                );
            }
        }
    });
    
    // Toggle fields based on selected action
    $(document).on('change', '.tab-action-select', function() {
        var $row = $(this).closest('.tab-customization-row');
        var action = $(this).val();
        
        $row.find('.rename-field').toggle(action === 'rename');
        $row.find('.custom-content-field').toggle(action === 'custom_content');
    });
    
    // Form validation before submit
    $('form').on('submit', function(e) {
        var hasError = false;
        
        $('.tab-customization-row').each(function() {
            var $row = $(this);
            var tabSelect = $row.find('select[name*="[tab_select]"]').val();
            var action = $row.find('.tab-action-select').val();
            
            if (!tabSelect) {
                alert('Please select a tab for all customizations.');
                hasError = true;
                return false;
            }
            
            if (action === 'rename') {
                var newName = $row.find('input[name*="[tab_new_name]"]').val();
                if (!newName || newName.trim() === '') {
                    alert('Please enter a new tab name for rename actions.');
                    hasError = true;
                    return false;
                }
            }
            
            if (action === 'custom_content') {
                var customContent = $row.find('textarea[name*="[tab_custom_content]"]').val();
                if (!customContent || customContent.trim() === '') {
                    alert('Please enter custom content for custom content actions.');
                    hasError = true;
                    return false;
                }
            }
        });
        
        if (hasError) {
            e.preventDefault();
        }
    });
    
    // Auto-save draft functionality (optional enhancement)
    var autoSaveTimer;
    $('form').on('change', 'input, select, textarea', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Show auto-save indicator
            if (!$('.auto-save-indicator').length) {
                $('<span class="auto-save-indicator" style="color: #666; font-size: 12px; margin-left: 10px;">Changes detected...</span>')
                    .insertAfter('h2')
                    .delay(2000)
                    .fadeOut(400, function() { $(this).remove(); });
            }
        }, 1000);
    });
});