<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 * @author     Your Name <email@example.com>
 */
class Business_Dashboard_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Remove custom user role
        remove_role( 'business_user' );

        // Flush rewrite rules
        flush_rewrite_rules();

        // Unschedule product sync cron job
        wp_clear_scheduled_hook( 'business_dashboard_scheduled_sync' );
    }

}
