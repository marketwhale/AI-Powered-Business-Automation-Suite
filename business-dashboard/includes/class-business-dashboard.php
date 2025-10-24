<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/includes
 * @author     Your Name <email@example.com>
 */
class Business_Dashboard {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Business_Dashboard_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'business-dashboard';
        $this->version = BUSINESS_DASHBOARD_VERSION;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_custom_user_meta();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Business_Dashboard_Loader. Orchestrates the hooks of the plugin.
     * - Business_Dashboard_i18n. Defines internationalization for this plugin.
     * - Business_Dashboard_Admin. Defines all hooks for the admin area.
     * - Business_Dashboard_Public. Defines all hooks for the public side of the site.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the core plugin.
         */
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'includes/class-business-dashboard-loader.php';

        /**
         * The class responsible for defining internationalization functionality of the plugin.
         */
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'includes/class-business-dashboard-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'admin/class-business-dashboard-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing side of the site.
         */
        require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/class-business-dashboard-public.php';

        $this->loader = new Business_Dashboard_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Business_Dashboard_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Business_Dashboard_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Add admin menu for businesses
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_business_dashboard_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_business_dashboard_settings' );
        $this->loader->add_action( 'admin_post_business_dashboard_approve_business', $plugin_admin, 'approve_business' );
        $this->loader->add_action( 'admin_post_business_dashboard_reject_business', $plugin_admin, 'reject_business' );
        $this->loader->add_action( 'admin_post_business_dashboard_manual_sync_all', $plugin_admin, 'manual_sync_all_businesses' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Business_Dashboard_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // Shortcodes
        $this->loader->add_shortcode( 'business_login', $plugin_public, 'business_login_shortcode' );
        $this->loader->add_shortcode( 'business_dashboard', $plugin_public, 'business_dashboard_shortcode' );
        $this->loader->add_shortcode( 'business_register', $plugin_public, 'business_register_shortcode' );

        // AJAX for manual product sync
        $this->loader->add_action( 'wp_ajax_business_dashboard_manual_product_sync', $plugin_public, 'manual_product_sync' );
        $this->loader->add_action( 'wp_ajax_nopriv_business_dashboard_manual_product_sync', $plugin_public, 'manual_product_sync' );

        // AJAX for loading dashboard sections
        $this->loader->add_action( 'wp_ajax_business_dashboard_load_section', $plugin_public, 'load_dashboard_section' );
        $this->loader->add_action( 'wp_ajax_nopriv_business_dashboard_load_section', $plugin_public, 'load_dashboard_section' );

        // WP Cron for scheduled product sync
        $this->loader->add_action( 'business_dashboard_scheduled_sync', $plugin_public, 'scheduled_product_sync' );
    }

    /**
     * Define custom user meta fields for businesses.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_custom_user_meta() {
        add_action( 'show_user_profile', array( $this, 'add_custom_user_profile_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'add_custom_user_profile_fields' ) );
        add_action( 'personal_options_update', array( $this, 'save_custom_user_profile_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_profile_fields' ) );
    }

    /**
     * Add custom user profile fields.
     *
     * @since    1.0.0
     * @param    WP_User $user The user object.
     */
    public function add_custom_user_profile_fields( $user ) {
        if ( ! current_user_can( 'edit_user', $user->ID ) ) {
            return;
        }

        if ( in_array( 'business_user', (array) $user->roles ) ) {
            ?>
            <h3><?php _e( 'Business Information', 'business-dashboard' ); ?></h3>

            <table class="form-table">
                <tr>
                    <th><label for="business_status"><?php _e( 'Business Status', 'business-dashboard' ); ?></label></th>
                    <td>
                        <select name="business_status" id="business_status">
                            <option value="pending" <?php selected( get_user_meta( $user->ID, 'business_status', true ), 'pending' ); ?>><?php _e( 'Pending Approval', 'business-dashboard' ); ?></option>
                            <option value="approved" <?php selected( get_user_meta( $user->ID, 'business_status', true ), 'approved' ); ?>><?php _e( 'Approved', 'business-dashboard' ); ?></option>
                            <option value="rejected" <?php selected( get_user_meta( $user->ID, 'business_status', true ), 'rejected' ); ?>><?php _e( 'Rejected', 'business-dashboard' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="website_url"><?php _e( 'Website URL', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="url" name="website_url" id="website_url" value="<?php echo esc_attr( get_user_meta( $user->ID, 'website_url', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="business_description"><?php _e( 'Business Description', 'business-dashboard' ); ?></label></th>
                    <td>
                        <textarea name="business_description" id="business_description" rows="5" cols="30"><?php echo esc_textarea( get_user_meta( $user->ID, 'business_description', true ) ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="contact_phone"><?php _e( 'Contact Phone', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'contact_phone', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="profile_image"><?php _e( 'Profile Image URL', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="url" name="profile_image" id="profile_image" value="<?php echo esc_attr( get_user_meta( $user->ID, 'profile_image', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="cover_image"><?php _e( 'Cover Image URL', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="url" name="cover_image" id="cover_image" value="<?php echo esc_attr( get_user_meta( $user->ID, 'cover_image', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="sync_url"><?php _e( 'Product Sync URL', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="url" name="sync_url" id="sync_url" value="<?php echo esc_attr( get_user_meta( $user->ID, 'sync_url', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="api_key"><?php _e( 'Consumer Key', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( get_user_meta( $user->ID, 'api_key', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="consumer_secret"><?php _e( 'Consumer Secret', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="text" name="consumer_secret" id="consumer_secret" value="<?php echo esc_attr( get_user_meta( $user->ID, 'consumer_secret', true ) ); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="data_source_type"><?php _e( 'Data Source Type', 'business-dashboard' ); ?></label></th>
                    <td>
                        <select name="data_source_type" id="data_source_type">
                            <option value="json" <?php selected( get_user_meta( $user->ID, 'data_source_type', true ), 'json' ); ?>><?php _e( 'JSON REST API', 'business-dashboard' ); ?></option>
                            <option value="csv" <?php selected( get_user_meta( $user->ID, 'data_source_type', true ), 'csv' ); ?>><?php _e( 'CSV Feed', 'business-dashboard' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="last_sync_date"><?php _e( 'Last Sync Date', 'business-dashboard' ); ?></label></th>
                    <td>
                        <input type="text" name="last_sync_date" id="last_sync_date" value="<?php echo esc_attr( get_user_meta( $user->ID, 'last_sync_date', true ) ); ?>" class="regular-text" readonly />
                    </td>
                </tr>
            </table>
            <?php
        }
    }

    /**
     * Save custom user profile fields.
     *
     * @since    1.0.0
     * @param    int $user_id The user ID.
     */
    public function save_custom_user_profile_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        $fields = array(
            'business_status',
            'website_url',
            'business_description',
            'contact_phone',
            'profile_image',
            'cover_image',
            'sync_url',
            'api_key',
            'consumer_secret',
            'data_source_type',
            'last_sync_date',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_user_meta( $user_id, $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of WordPress and
     * to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Business_Dashboard_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
