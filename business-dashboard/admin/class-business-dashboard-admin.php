<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/admin
 * @author     Your Name <email@example.com>
 */
class Business_Dashboard_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version           The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, BUSINESS_DASHBOARD_PLUGIN_URL . 'admin/css/business-dashboard-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, BUSINESS_DASHBOARD_PLUGIN_URL . 'admin/js/business-dashboard-admin.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Add admin menu for businesses under WooCommerce.
     *
     * @since    1.0.0
     */
    public function add_business_dashboard_admin_menu() {
        add_menu_page(
            __( 'Business Dashboard', 'business-dashboard' ), // Page title
            __( 'Business Dashboard', 'business-dashboard' ), // Menu title
            'manage_options', // Capability
            $this->plugin_name, // Menu slug (top-level)
            array( $this, 'display_main_admin_page' ), // Callback function for the main page
            'dashicons-store', // Icon URL
            25 // Position
        );

        add_submenu_page(
            $this->plugin_name, // Parent slug (new top-level menu)
            __( 'Businesses', 'business-dashboard' ), // Page title
            __( 'Businesses', 'business-dashboard' ), // Menu title
            'manage_woocommerce', // Capability
            $this->plugin_name . '-businesses', // Menu slug
            array( $this, 'display_businesses_page' ) // Callback function
        );

        add_submenu_page(
            $this->plugin_name, // Parent slug (new top-level menu)
            __( 'Business Dashboard Settings', 'business-dashboard' ), // Page title
            __( 'Settings', 'business-dashboard' ), // Menu title
            'manage_options', // Capability
            $this->plugin_name . '-settings', // Menu slug
            array( $this, 'display_settings_page' ) // Callback function
        );
    }

    /**
     * Display the main admin page for the plugin.
     * This will serve as the container for the tabbed layout.
     *
     * @since    1.0.0
     */
    public function display_main_admin_page() {
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'admin/partials/business-dashboard-main-admin-page.php';
    }

    /**
     * Display the businesses management page.
     *
     * @since    1.0.0
     */
    public function display_businesses_page() {
        // This content is now loaded directly into the main admin page partial.
        // This function can remain for direct access if needed, but its primary use
        // will be through the main admin page.
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'admin/partials/business-dashboard-display-businesses.php';
    }

    /**
     * Approve a business.
     *
     * @since    1.0.0
     */
    public function approve_business() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'business-dashboard' ) );
        }

        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'business_dashboard_approve_business_nonce' ) ) {
            wp_die( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;

        if ( $user_id ) {
            update_user_meta( $user_id, 'business_status', 'approved' );
            // Send email notification
            $user_info = get_userdata( $user_id );
            if ( $user_info ) {
                $to = $user_info->user_email;
                $subject = __( 'Your Business Account Has Been Approved', 'business-dashboard' );
                $message = sprintf(
                    __( 'Dear %s, your business account has been approved. You can now log in to your dashboard: %s', 'business-dashboard' ),
                    $user_info->display_name,
                    wp_login_url( home_url( '/business-dashboard/' ) )
                );
                wp_mail( $to, $subject, $message );
            }
        }

        wp_redirect( admin_url( 'admin.php?page=' . $this->plugin_name . '-businesses' ) );
        exit;
    }

    /**
     * Reject a business.
     *
     * @since    1.0.0
     */
    public function reject_business() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'business-dashboard' ) );
        }

        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'business_dashboard_reject_business_nonce' ) ) {
            wp_die( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;

        if ( $user_id ) {
            update_user_meta( $user_id, 'business_status', 'rejected' );
            // Send email notification
            $user_info = get_userdata( $user_id );
            if ( $user_info ) {
                $to = $user_info->user_email;
                $subject = __( 'Your Business Account Has Been Rejected', 'business-dashboard' );
                $message = sprintf(
                    __( 'Dear %s, your business account has been rejected. Please contact support for more information.', 'business-dashboard' ),
                    $user_info->display_name
                );
                wp_mail( $to, $subject, $message );
            }
        }

        wp_redirect( admin_url( 'admin.php?page=' . $this->plugin_name . '-businesses' ) );
        exit;
    }

    /**
     * Display the plugin settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        // This content is now loaded directly into the main admin page partial.
        // This function can remain for direct access if needed, but its primary use
        // will be through the main admin page.
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields( $this->plugin_name . '-settings-group' );
            do_settings_sections( $this->plugin_name . '-settings-group' );
            submit_button();
            ?>
        </form>
        <?php
    }

    /**
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_business_dashboard_settings() {
        register_setting(
            $this->plugin_name . '-settings-group',
            'business_dashboard_sync_frequency',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'daily',
            )
        );

        register_setting(
            $this->plugin_name . '-settings-group',
            'business_dashboard_enable_sync_automation',
            array(
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default'           => true,
            )
        );

        add_settings_section(
            'business_dashboard_general_settings',
            __( 'General Settings', 'business-dashboard' ),
            array( $this, 'print_general_settings_section_info' ),
            $this->plugin_name . '-settings-group'
        );

        add_settings_field(
            'business_dashboard_sync_frequency',
            __( 'Default Sync Frequency', 'business-dashboard' ),
            array( $this, 'sync_frequency_callback' ),
            $this->plugin_name . '-settings-group',
            'business_dashboard_general_settings'
        );

        add_settings_field(
            'business_dashboard_enable_sync_automation',
            __( 'Enable Sync Automation', 'business-dashboard' ),
            array( $this, 'enable_sync_automation_callback' ),
            $this->plugin_name . '-settings-group',
            'business_dashboard_general_settings'
        );
    }

    /**
     * Get the settings option array and print one of its values.
     *
     * @since    1.0.0
     */
    public function enable_sync_automation_callback() {
        $option = get_option( 'business_dashboard_enable_sync_automation', true );
        ?>
        <input type="checkbox" id="business_dashboard_enable_sync_automation" name="business_dashboard_enable_sync_automation" value="1" <?php checked( $option, 1 ); ?> />
        <label for="business_dashboard_enable_sync_automation"><?php _e( 'Enable automatic product synchronization for all businesses.', 'business-dashboard' ); ?></label>
        <?php
    }

    /**
     * Manually trigger sync for all approved businesses.
     *
     * @since    1.0.0
     */
    public function manual_sync_all_businesses() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'business-dashboard' ) );
        }

        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'business_dashboard_manual_sync_all_nonce' ) ) {
            wp_die( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $args = array(
            'role'       => 'business_user',
            'meta_key'   => 'business_status',
            'meta_value' => 'approved',
            'fields'     => 'ID',
        );
        $approved_business_ids = get_users( $args );

        $public_class = new Business_Dashboard_Public( $this->plugin_name, $this->version );
        $sync_count = 0;
        foreach ( $approved_business_ids as $user_id ) {
            $base_sync_url = get_user_meta( $user_id, 'sync_url', true );
            $api_key = get_user_meta( $user_id, 'api_key', true );
            $consumer_secret = get_user_meta( $user_id, 'consumer_secret', true );
            $data_source_type = get_user_meta( $user_id, 'data_source_type', true );

            if ( ! empty( $base_sync_url ) ) {
                $sync_url = $public_class->get_woocommerce_api_endpoint( $base_sync_url );
                $sync_result = $public_class->perform_product_sync( $user_id, $sync_url, $api_key, $consumer_secret, $data_source_type );
                if ( ! is_wp_error( $sync_result ) ) {
                    update_user_meta( $user_id, 'last_sync_date', current_time( 'mysql' ) );
                    $sync_count++;
                } else {
                    error_log( 'Business Dashboard Manual Sync All Error for user ' . $user_id . ': ' . $sync_result->get_error_message() );
                }
            }
        }

        $message = sprintf( __( '%d businesses synced successfully.', 'business-dashboard' ), $sync_count );
        wp_redirect( admin_url( 'admin.php?page=' . $this->plugin_name . '-settings&message=' . urlencode( $message ) ) );
        exit;
    }

    /**
     * Print the Section text for General Settings.
     *
     * @since    1.0.0
     */
    public function print_general_settings_section_info() {
        _e( 'Configure general settings for the Business Dashboard plugin.', 'business-dashboard' );
    }

    /**
     * Get the settings option array and print one of its values.
     *
     * @since    1.0.0
     */
    public function sync_frequency_callback() {
        $option = get_option( 'business_dashboard_sync_frequency', 'daily' );
        ?>
        <select id="business_dashboard_sync_frequency" name="business_dashboard_sync_frequency">
            <option value="hourly" <?php selected( $option, 'hourly' ); ?>><?php _e( 'Hourly', 'business-dashboard' ); ?></option>
            <option value="daily" <?php selected( $option, 'daily' ); ?>><?php _e( 'Daily', 'business-dashboard' ); ?></option>
            <option value="weekly" <?php selected( $option, 'weekly' ); ?>><?php _e( 'Weekly', 'business-dashboard' ); ?></option>
        </select>
        <?php
    }
}
