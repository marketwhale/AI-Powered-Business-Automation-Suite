/*
 * Public-facing JavaScript for this plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/public/js
 */
(function( $ ) {
    'use strict';

    $(function() {
        // Handle sidebar navigation for the front-end business dashboard
        $('.business-dashboard-sidebar ul li a').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var section = $this.attr('href').split('section=')[1];

            // Update active sidebar link class
            $('.business-dashboard-sidebar ul li a').removeClass('active');
            $this.addClass('active');

            // Redirect to the new section URL
            window.location.href = $this.attr('href');
        });

        // Handle manual product sync via AJAX
        $(document).on('submit', '.business-dashboard-section form', function(e) {
            if ($(this).find('input[name="sync_products_manual"]').length) {
                e.preventDefault();

                var $form = $(this);
                var $submitButton = $form.find('input[name="sync_products_manual"]');
                var originalButtonText = $submitButton.val();
                var formData = $form.serialize();

                // Add nonce to form data
                formData += '&action=business_dashboard_manual_product_sync';

                // Display loading state
                $submitButton.val('Syncing...').prop('disabled', true);
                $form.find('.business-dashboard-message').remove(); // Clear previous messages

                $.ajax({
                    url: business_dashboard_public_vars.ajax_url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $form.prepend('<p class="business-dashboard-success business-dashboard-message">' + response.data + '</p>');
                            // Optionally update last sync date without full page reload
                            var newDate = new Date().toLocaleString(); // Or get from response if available
                            $form.closest('.business-dashboard-section').find('p:contains("Last Sync:")').html('Last Sync: ' + newDate);
                        } else {
                            $form.prepend('<p class="business-dashboard-error business-dashboard-message">' + response.data + '</p>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                        $form.prepend('<p class="business-dashboard-error business-dashboard-message">' + 'An unexpected error occurred. Please check console for details.' + '</p>');
                    },
                    complete: function() {
                        $submitButton.val(originalButtonText).prop('disabled', false);
                    }
                });
            }
        });
    });

})( jQuery );
