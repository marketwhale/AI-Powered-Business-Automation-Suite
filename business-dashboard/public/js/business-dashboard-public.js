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

        // Handle product search for linking to business posts
        var searchTimeout;
        $(document).on('keyup', '#linked_product_search', function() {
            var $this = $(this);
            var searchTerm = $this.val();
            var $searchResults = $('#product-search-results');
            clearTimeout(searchTimeout);

            if (searchTerm.length < 3) {
                $searchResults.empty();
                return;
            }

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: business_dashboard_public_vars.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'business_dashboard_search_products',
                        search_term: searchTerm,
                        nonce: business_dashboard_public_vars.product_search_nonce
                    },
                    success: function(response) {
                        $searchResults.empty();
                        if (response.success && response.data.length > 0) {
                            $.each(response.data, function(index, product) {
                                $searchResults.append(
                                    '<div class="business-dashboard-search-result-item" data-product-id="' + product.id + '" data-product-name="' + product.name + '">' +
                                        product.name + ' (SKU: ' + product.sku + ')' +
                                    '</div>'
                                );
                            });
                        } else {
                            $searchResults.append('<div class="business-dashboard-search-result-item">' + 'No products found.' + '</div>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error searching products:', textStatus, errorThrown, jqXHR.responseText);
                        $searchResults.empty().append('<p class="business-dashboard-error">' + 'Error searching products.' + '</p>');
                    }
                });
            }, 500); // 500ms debounce
        });

        // Handle selection of a product from search results
        $(document).on('click', '.business-dashboard-search-result-item', function() {
            var $this = $(this);
            var productId = $this.data('product-id');
            var productName = $this.data('product-name');

            $('#linked_product_id').val(productId);
            $('#linked_product_search').val(productName);
            $('#selected-product-display').html('<p><strong>' + 'Selected Product:' + '</strong> ' + productName + '</p>');
            $('#product-search-results').empty();
        });

        // Handle business post creation form submission
        $(document).on('submit', '#business-post-creation-form', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitButton = $('#publish-post-button');
            var $feedbackArea = $('#post-creation-feedback');
            var originalButtonText = $submitButton.val();

            $submitButton.val('Publishing...').prop('disabled', true).addClass('loading');
            $feedbackArea.empty().removeClass('business-dashboard-success business-dashboard-error');

            var formData = new FormData(this);
            formData.append('action', 'business_dashboard_create_post');
            formData.append('nonce', business_dashboard_public_vars.post_creation_nonce);

            $.ajax({
                url: business_dashboard_public_vars.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $feedbackArea.addClass('business-dashboard-success').text(response.data);
                        $form[0].reset(); // Clear form
                        $('#linked_product_id').val('');
                        $('#selected-product-display').empty();
                    } else {
                        $feedbackArea.addClass('business-dashboard-error').text(response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error creating post:', textStatus, errorThrown, jqXHR.responseText);
                    $feedbackArea.addClass('business-dashboard-error').text('An unexpected error occurred. Please try again.');
                },
                complete: function() {
                    $submitButton.val(originalButtonText).prop('disabled', false).removeClass('loading');
                }
            });
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
                        if (section === 'settings') {
                            initProfileSettingsTabs();
                            initProfileSettingsForm();
                            initChangePasswordModal();
                            initVerificationInfo();
                        }
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
        if (initialSection === 'settings') {
            initProfileSettingsTabs();
            initProfileSettingsForm();
            initChangePasswordModal();
            initVerificationInfo();
        }

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

        // --- New Functionality for Business Profile Settings ---

        // Initialize tabs for profile settings
        function initProfileSettingsTabs() {
            $(document).off('click', '.business-dashboard-tabs-wrap .nav-tab').on('click', '.business-dashboard-tabs-wrap .nav-tab', function(e) {
                e.preventDefault();
                var $this = $(this);
                var targetTab = $this.data('tab');

                $('.business-dashboard-tabs-wrap .nav-tab').removeClass('nav-tab-active');
                $this.addClass('nav-tab-active');

                $('.business-dashboard-tab-content').hide();
                $('#' + targetTab + '-tab').show();
            });

            // Set initial active tab
            var initialTab = new URLSearchParams(window.location.hash.substring(1)).get('tab') || 'business-info';
            $('.business-dashboard-tabs-wrap .nav-tab[data-tab="' + initialTab + '"]').click();
        }

        // Handle profile settings form submission
        function initProfileSettingsForm() {
            $(document).off('submit', '#business-profile-settings-form').on('submit', '#business-profile-settings-form', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $submitButton = $('#submit-profile-settings');
                var $feedbackArea = $('#profile-settings-feedback');
                var originalButtonText = $submitButton.val();

                $submitButton.val('Saving...').prop('disabled', true).addClass('loading');
                $feedbackArea.empty().removeClass('business-dashboard-success business-dashboard-error');

                var formData = new FormData(this);
                formData.append('action', 'business_dashboard_update_profile_settings');
                formData.append('nonce', business_dashboard_public_vars.profile_settings_nonce);

                $.ajax({
                    url: business_dashboard_public_vars.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $feedbackArea.addClass('business-dashboard-success').text(response.data);
                            // Trigger glowing animation
                            $submitButton.addClass('glowing-success');
                            setTimeout(function() {
                                $submitButton.removeClass('glowing-success');
                            }, 2000); // Remove class after 2 seconds
                            // Reload section to update displayed profile info
                            loadDashboardSection('settings');
                        } else {
                            $feedbackArea.addClass('business-dashboard-error').text(response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error updating profile settings:', textStatus, errorThrown, jqXHR.responseText);
                        $feedbackArea.addClass('business-dashboard-error').text('An unexpected error occurred. Please try again.');
                    },
                    complete: function() {
                        $submitButton.val(originalButtonText).prop('disabled', false).removeClass('loading');
                    }
                });
            });
        }

        // Initialize change password modal
        function initChangePasswordModal() {
            var $modal = $('#change-password-modal');
            var $closeButton = $modal.find('.business-dashboard-modal-close');
            var $changePasswordButton = $('#change-password-button');
            var $passwordChangeForm = $('#change-password-form');
            var $passwordChangeFeedback = $('#password-change-feedback');

            $changePasswordButton.off('click').on('click', function() {
                $modal.show();
            });

            $closeButton.off('click').on('click', function() {
                $modal.hide();
                $passwordChangeForm[0].reset();
                $passwordChangeFeedback.empty().removeClass('business-dashboard-success business-dashboard-error');
            });

            $(window).off('click.modal').on('click.modal', function(event) {
                if ($(event.target).is($modal)) {
                    $modal.hide();
                    $passwordChangeForm[0].reset();
                    $passwordChangeFeedback.empty().removeClass('business-dashboard-success business-dashboard-error');
                }
            });

            $passwordChangeForm.off('submit').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $submitButton = $form.find('input[type="submit"]');
                var originalButtonText = $submitButton.val();

                $submitButton.val('Changing...').prop('disabled', true).addClass('loading');
                $passwordChangeFeedback.empty().removeClass('business-dashboard-success business-dashboard-error');

                var currentPassword = $('#current_password').val();
                var newPassword = $('#new_password').val();
                var confirmNewPassword = $('#confirm_new_password').val();

                if (newPassword !== confirmNewPassword) {
                    $passwordChangeFeedback.addClass('business-dashboard-error').text('New passwords do not match.');
                    $submitButton.val(originalButtonText).prop('disabled', false).removeClass('loading');
                    return;
                }

                $.ajax({
                    url: business_dashboard_public_vars.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'business_dashboard_change_password',
                        current_password: currentPassword,
                        new_password: newPassword,
                        nonce: business_dashboard_public_vars.change_password_nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $passwordChangeFeedback.addClass('business-dashboard-success').text(response.data);
                            $form[0].reset();
                            setTimeout(function() {
                                $modal.hide();
                            }, 2000);
                        } else {
                            $passwordChangeFeedback.addClass('business-dashboard-error').text(response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error changing password:', textStatus, errorThrown, jqXHR.responseText);
                        $passwordChangeFeedback.addClass('business-dashboard-error').text('An unexpected error occurred. Please try again.');
                    },
                    complete: function() {
                        $submitButton.val(originalButtonText).prop('disabled', false).removeClass('loading');
                    }
                });
            });
        }

        // Initialize verification info logic
        function initVerificationInfo() {
            var $regNum = $('#business_registration_number');
            var $taxId = $('#tax_id');
            var $certificateUpload = $('#certificate_upload');
            var $requestVerificationButton = $('#request-verification-button');
            var $verificationStatusDisplay = $('#verification-status-display');

            function checkVerificationFields() {
                var allFilled = $regNum.val().length > 0 && $taxId.val().length > 0;
                // Check if a file is selected or if a certificate URL already exists
                var fileProvided = $certificateUpload[0].files.length > 0 || ($certificateUpload.next('a').length > 0 && $certificateUpload.next('a').attr('href').length > 0);
                
                if (allFilled && fileProvided && $verificationStatusDisplay.text().toLowerCase().indexOf('pending') !== -1) {
                    $requestVerificationButton.prop('disabled', false);
                } else {
                    $requestVerificationButton.prop('disabled', true);
                }
            }

            $regNum.off('keyup change').on('keyup change', checkVerificationFields);
            $taxId.off('keyup change').on('keyup change', checkVerificationFields);
            $certificateUpload.off('change').on('change', checkVerificationFields);

            // Initial check on load
            checkVerificationFields();

            $requestVerificationButton.off('click').on('click', function() {
                var $thisButton = $(this);
                var originalButtonText = $thisButton.text();

                $thisButton.text('Requesting...').prop('disabled', true).addClass('loading');

                $.ajax({
                    url: business_dashboard_public_vars.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'business_dashboard_request_verification',
                        nonce: business_dashboard_public_vars.request_verification_nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $verificationStatusDisplay.text('Pending');
                            alert(response.data); // Or use a more styled feedback
                        } else {
                            alert(response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error requesting verification:', textStatus, errorThrown, jqXHR.responseText);
                        alert('An unexpected error occurred. Please try again.');
                    },
                    complete: function() {
                        $thisButton.text(originalButtonText).prop('disabled', false).removeClass('loading');
                        checkVerificationFields(); // Re-check status
                    }
                });
            });
        }
    });

})( jQuery );
