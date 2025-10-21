<?php
/**
 * Fired during plugin activation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 * @author     Your Name <email@example.com>
 */
class Business_Dashboard_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Register custom user role
        add_role(
            'business_user',
            __( 'Business User', 'business-dashboard' ),
            array(
                'read'         => true,  // Can read posts
                'edit_posts'   => false, // Cannot edit posts
                'delete_posts' => false, // Cannot delete posts
            )
        );

        // Flush rewrite rules to ensure shortcodes work
        flush_rewrite_rules();

        // Schedule product sync cron job
        if ( ! wp_next_scheduled( 'business_dashboard_scheduled_sync' ) ) {
            wp_schedule_event( time(), 'daily', 'business_dashboard_scheduled_sync' );
        }
    }

}
