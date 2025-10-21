/*
 * Admin-specific JavaScript for this plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/admin/js
 */
(function( $ ) {
    'use strict';

    $(function() {
        // Handle tab switching in the admin area
        $('.nav-tab-wrapper a').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var tab = $this.attr('href').split('tab=')[1];

            // Update active tab class
            $('.nav-tab-wrapper a').removeClass('nav-tab-active');
            $this.addClass('nav-tab-active');

            // Redirect to the new tab URL
            window.location.href = $this.attr('href');
        });
    });

})( jQuery );
