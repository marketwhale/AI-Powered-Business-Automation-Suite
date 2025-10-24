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
        // Handle sidebar navigation for the front-end business dashboard with AJAX
        $(document).on('click', '.business-dashboard-nav-link', function(e) {
            e.preventDefault();
            var $this = $(this);
            var section = $this.data('section'); // Use data-section attribute

            if (section) { // Only process if a section is defined (not for logout link)
                // Update active sidebar link class
                $('.business-dashboard-nav-link').removeClass('active');
                $this.addClass('active');

                // Update URL using history.pushState
                var newUrl = window.location.pathname + '?section=' + section;
                history.pushState({ section: section }, '', newUrl);

                // Load content via AJAX
                loadDashboardSection(section);
            } else {
                // For logout link, proceed with default navigation
                window.location.href = $this.attr('href');
            }
        });

        // Function to load dashboard section content via AJAX
        function loadDashboardSection(section) {
            var $contentArea = $('#business-dashboard-content-area');
            $contentArea.html('<div class="business-dashboard-loading">' + business_dashboard_public_vars.loading_text + '</div>'); // Show loading indicator

            $.ajax({
                url: business_dashboard_public_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'business_dashboard_load_section',
                    section: section,
                    nonce: business_dashboard_public_vars.dashboard_nonce
                },
                success: function(response) {
                    if (response.success) {
                        $contentArea.html(response.data.content);
                        // Re-initialize any scripts or event listeners for the new content if necessary
                        // For now, the existing sync form handler is delegated, so it should work.
                    } else {
                        $contentArea.html('<p class="business-dashboard-error">' + response.data + '</p>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error loading section:', textStatus, errorThrown, jqXHR.responseText);
                    $contentArea.html('<p class="business-dashboard-error">' + 'Failed to load section. Please try again.' + '</p>');
                }
            });
        }

        // Handle browser back/forward buttons
        $(window).on('popstate', function(event) {
            var state = event.originalEvent.state;
            if (state && state.section) {
                $('.business-dashboard-nav-link').removeClass('active');
                $('.business-dashboard-nav-link[data-section="' + state.section + '"]').addClass('active');
                loadDashboardSection(state.section);
            } else {
                // If no state or initial page load, load default section
                var initialSection = new URLSearchParams(window.location.search).get('section') || 'profile';
                $('.business-dashboard-nav-link[data-section="' + initialSection + '"]').addClass('active');
                loadDashboardSection(initialSection);
            }
        });

        // Initial load for the current section on page load
        var initialSection = new URLSearchParams(window.location.search).get('section') || 'profile';
        $('.business-dashboard-nav-link[data-section="' + initialSection + '"]').addClass('active');
        // No need to call loadDashboardSection here, as PHP already renders the initial content.
        // This ensures that the initial page load is not an AJAX call.

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
                            $form.prepend('<p class="business-dashboard-success business-dashboard-message">' + response.data.message + '</p>');
                            $('#last-sync-date').text(response.data.last_sync);
                            $('#synced-product-list').html(response.data.product_list);
                            $('#sync-logs-display').html(response.data.sync_logs);
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
