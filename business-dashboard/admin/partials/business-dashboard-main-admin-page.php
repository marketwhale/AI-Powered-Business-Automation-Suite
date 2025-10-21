<?php
/**
 * Provide a admin area view for the plugin's main page with tabbed layout.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/admin/partials
 */

$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'businesses';
?>

<div class="wrap">
    <h1><?php _e( 'Business Dashboard', 'business-dashboard' ); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=business-dashboard&tab=businesses" class="nav-tab <?php echo 'businesses' === $current_tab ? 'nav-tab-active' : ''; ?>">
            <?php _e( 'Businesses', 'business-dashboard' ); ?>
        </a>
        <a href="?page=business-dashboard&tab=settings" class="nav-tab <?php echo 'settings' === $current_tab ? 'nav-tab-active' : ''; ?>">
            <?php _e( 'Settings', 'business-dashboard' ); ?>
        </a>
    </h2>

    <div class="business-dashboard-admin-content">
        <?php
        if ( 'businesses' === $current_tab ) {
            require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'admin/partials/business-dashboard-display-businesses.php';
        } elseif ( 'settings' === $current_tab ) {
            // Include the settings page content
            // For now, we'll just call the display_settings_page method from the admin class
            // In a more complex scenario, you might have a separate partial for settings.
            $admin_class = new Business_Dashboard_Admin( 'business-dashboard', BUSINESS_DASHBOARD_VERSION );
            $admin_class->display_settings_page();
        }
        ?>
    </div>
</div>
