<?php
/**
 * Plugin Name: Business Dashboard
 * Plugin URI: https://example.com/business-dashboard
 * Description: A WordPress WooCommerce plugin that provides a front-end portal for businesses to register, log in, and manage their profile, with admin approval and product synchronization from external websites.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'BUSINESS_DASHBOARD_VERSION', '1.0.0' );
define( 'BUSINESS_DASHBOARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUSINESS_DASHBOARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUSINESS_DASHBOARD_BASENAME', plugin_basename( __FILE__ ) );

// Include necessary files
require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'includes/class-business-dashboard-activator.php';
require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'includes/class-business-dashboard-deactivator.php';
require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'includes/class-business-dashboard.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-business-dashboard-activator.php
 */
function activate_business_dashboard() {
    Business_Dashboard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-business-dashboard-deactivator.php
 */
function deactivate_business_dashboard() {
    Business_Dashboard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_business_dashboard' );
register_deactivation_hook( __FILE__, 'deactivate_business_dashboard' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file means
 * that nothing within the plugin will be called unless the plugin is active.
 *
 * @since    1.0.0
 */
function run_business_dashboard() {
    $plugin = new Business_Dashboard();
    $plugin->run();
}
run_business_dashboard();
