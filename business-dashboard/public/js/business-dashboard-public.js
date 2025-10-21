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
    });

})( jQuery );
