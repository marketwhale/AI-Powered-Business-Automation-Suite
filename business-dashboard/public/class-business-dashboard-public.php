<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/public
 * @author     Your Name <email@example.com>
 */
class Business_Dashboard_Public {

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
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, BUSINESS_DASHBOARD_PLUGIN_URL . 'public/css/business-dashboard-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, BUSINESS_DASHBOARD_PLUGIN_URL . 'public/js/business-dashboard-public.js', array( 'jquery' ), $this->version, false );

        wp_localize_script(
            $this->plugin_name,
            'business_dashboard_public_vars',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'business_product_sync_action' ), // Ensure nonce is passed
                'dashboard_nonce' => wp_create_nonce( 'business_dashboard_load_section_nonce' ),
                'post_creation_nonce' => wp_create_nonce( 'business_post_creation_action' ),
                'product_search_nonce' => wp_create_nonce( 'business_product_search_action' ),
                'loading_text' => __( 'Loading...', 'business-dashboard' ),
            )
        );
    }

    /**
     * Shortcode for business registration form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML content for the registration form.
     */
    public function business_register_shortcode( $atts ) {
        ob_start();
        if ( is_user_logged_in() ) {
            echo '<p>' . __( 'You are already logged in. <a href="' . esc_url( wp_logout_url( home_url() ) ) . '">Logout</a>', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        $errors = array();
        if ( isset( $_POST['business_register_nonce'] ) && wp_verify_nonce( $_POST['business_register_nonce'], 'business_register_action' ) ) {
            $errors = $this->process_business_registration();
            if ( empty( $errors ) ) {
                // Redirect to business dashboard after successful registration
                wp_redirect( home_url( '/business-dashboard/?section=profile' ) );
                exit;
            }
        }
        ?>
        <div class="business-dashboard-form-wrap">
            <h2><?php _e( 'Business Registration', 'business-dashboard' ); ?></h2>
            <?php if ( ! empty( $errors ) ) : ?>
                <?php foreach ( $errors as $error ) : ?>
                    <p class="business-dashboard-error"><?php echo esc_html( $error ); ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <p>
                    <label for="business_name"><?php _e( 'Business Name', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_name" id="business_name" required value="<?php echo isset( $_POST['business_name'] ) ? esc_attr( $_POST['business_name'] ) : ''; ?>" />
                </p>
                <p>
                    <label for="business_url_slug"><?php _e( 'Business URL Slug', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_url_slug" id="business_url_slug_register" required value="<?php echo isset( $_POST['business_url_slug'] ) ? esc_attr( $_POST['business_url_slug'] ) : ''; ?>" />
                    <p class="description">
                        <?php _e( 'Your public URL will be:', 'business-dashboard' ); ?> <strong id="full-business-url-preview-register">https://bestbrands.live/<?php echo isset( $_POST['business_url_slug'] ) ? esc_attr( $_POST['business_url_slug'] ) : '{your-business-name}'; ?>/</strong>
                        <span id="business-url-availability-register" style="margin-left: 10px;"></span>
                    </p>
                </p>
                <p>
                    <label for="website_url"><?php _e( 'Website URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="website_url" id="website_url" required value="<?php echo isset( $_POST['website_url'] ) ? esc_attr( $_POST['website_url'] ) : ''; ?>" />
                </p>
                <p>
                    <label for="contact_phone"><?php _e( 'Contact Phone (+Country Code)', 'business-dashboard' ); ?></label>
                    <input type="text" name="contact_phone" id="contact_phone" required value="<?php echo isset( $_POST['contact_phone'] ) ? esc_attr( $_POST['contact_phone'] ) : ''; ?>" />
                </p>
                <p>
                    <label for="user_email"><?php _e( 'Email', 'business-dashboard' ); ?></label>
                    <input type="email" name="user_email" id="user_email" required value="<?php echo isset( $_POST['user_email'] ) ? esc_attr( $_POST['user_email'] ) : ''; ?>" />
                </p>
                <p>
                    <label for="user_pass"><?php _e( 'Password', 'business-dashboard' ); ?></label>
                    <input type="password" name="user_pass" id="user_pass" required />
                </p>
                <p>
                    <input type="submit" name="submit_registration" value="<?php _e( 'Register', 'business-dashboard' ); ?>" />
                </p>
                <?php wp_nonce_field( 'business_register_action', 'business_register_nonce' ); ?>
            </form>
            <p class="business-dashboard-form-links">
                <?php _e( 'Already have an account?', 'business-dashboard' ); ?> <a href="<?php echo esc_url( home_url( '/business-login/' ) ); ?>"><?php _e( 'Login here', 'business-dashboard' ); ?></a><br />
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Forgot Password?', 'business-dashboard' ); ?></a>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Process business registration form submission.
     *
     * @since    1.0.0
     * @return   array    Array of errors, empty if successful.
     */
    private function process_business_registration() {
        $errors = array();

        $business_name = sanitize_text_field( $_POST['business_name'] );
        $business_url_slug = sanitize_title( $_POST['business_url_slug'] );
        $website_url = esc_url_raw( $_POST['website_url'] );
        $contact_phone = sanitize_text_field( $_POST['contact_phone'] );
        $user_email = sanitize_email( $_POST['user_email'] );
        $user_pass = $_POST['user_pass'];
        // Removed business_description, profile_image, cover_image from registration form

        if ( empty( $business_name ) ) {
            $errors[] = __( 'Business Name is required.', 'business-dashboard' );
        }
        if ( empty( $business_url_slug ) ) {
            $errors[] = __( 'Business URL Slug is required.', 'business-dashboard' );
        }
        if ( $this->get_user_id_by_business_url_slug( $business_url_slug ) ) {
            $errors[] = __( 'This Business URL Slug is already taken. Please choose a different one.', 'business-dashboard' );
        }
        if ( empty( $website_url ) ) {
            $errors[] = __( 'Website URL is required.', 'business-dashboard' );
        }
        if ( empty( $contact_phone ) ) {
            $errors[] = __( 'Contact Phone is required.', 'business-dashboard' );
        }
        if ( ! is_email( $user_email ) ) {
            $errors[] = __( 'A valid email address is required.', 'business-dashboard' );
        }
        if ( email_exists( $user_email ) ) {
            $errors[] = __( 'This email is already registered.', 'business-dashboard' );
        }
        if ( empty( $user_pass ) ) {
            $errors[] = __( 'Password is required.', 'business-dashboard' );
        }

        if ( ! empty( $errors ) ) {
            return $errors;
        }

        $user_id = wp_create_user( $user_email, $user_pass, $user_email );

        if ( is_wp_error( $user_id ) ) {
            $errors[] = $user_id->get_error_message();
            return $errors;
        }

        // Set user role
        $user = new WP_User( $user_id );
        $user->set_role( 'business_user' );

        // Update user meta
        update_user_meta( $user_id, 'business_name', $business_name );
        update_user_meta( $user_id, 'business_url_slug', $business_url_slug );
        update_user_meta( $user_id, 'website_url', $website_url );
        update_user_meta( $user_id, 'contact_phone', $contact_phone );
        update_user_meta( $user_id, 'business_status', 'pending' ); // Default status
        // Initialize other meta fields that are now set in profile settings
        update_user_meta( $user_id, 'business_description', '' );
        update_user_meta( $user_id, 'profile_image', '' );
        update_user_meta( $user_id, 'cover_image', '' );
        update_user_meta( $user_id, 'verification_status', 'pending' ); // Default verification status

        return $errors;
    }

    /**
     * Handle image uploads for user meta.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     * @param    string $file_input_name The name of the file input field.
     * @param    string $meta_key  The meta key to save the image URL.
     */
    private function handle_image_upload( $user_id, $file_input_name, $meta_key ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        if ( ! empty( $_FILES[ $file_input_name ]['name'] ) ) {
            $uploaded_file = $_FILES[ $file_input_name ];
            $upload_overrides = array( 'test_form' => false );
            $move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $move_file && ! isset( $move_file['error'] ) ) {
                update_user_meta( $user_id, $meta_key, $move_file['url'] );
            } else {
                // Handle upload error
                error_log( 'Business Dashboard Image Upload Error: ' . $move_file['error'] );
            }
        }
    }

    /**
     * Shortcode for business login form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML content for the login form.
     */
    public function business_login_shortcode( $atts ) {
        ob_start();
        if ( is_user_logged_in() ) {
            echo '<p>' . __( 'You are already logged in. <a href="' . esc_url( wp_logout_url( home_url() ) ) . '">Logout</a>', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        $args = array(
            'echo'           => false,
            'redirect'       => home_url( '/business-dashboard/' ), // Redirect to the business dashboard page
            'form_id'        => 'business-login-form',
            'label_username' => __( 'Email', 'business-dashboard' ),
            'label_password' => __( 'Password', 'business-dashboard' ),
            'label_remember' => __( 'Remember Me', 'business-dashboard' ),
            'label_log_in'   => __( 'Log In', 'business-dashboard' ),
            'id_username'    => 'user_login_email',
            'id_password'    => 'user_pass_login',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'remember'       => true,
            'value_username' => '',
            'value_remember' => false,
        );

        // Check for login errors
        if ( isset( $_GET['login'] ) && $_GET['login'] == 'failed' ) {
            echo '<p class="business-dashboard-error">' . __( 'Login failed. Please check your credentials and ensure your account is approved.', 'business-dashboard' ) . '</p>';
        }

        echo '<div class="business-dashboard-form-wrap">';
        echo '<h2>' . __( 'Business Login', 'business-dashboard' ) . '</h2>';
        echo wp_login_form( $args );
        echo '</div>';
        ?>
        <p class="business-dashboard-form-links">
            <?php _e( 'Don\'t have an account?', 'business-dashboard' ); ?> <a href="<?php echo esc_url( home_url( '/business-register/' ) ); ?>"><?php _e( 'Register here', 'business-dashboard' ); ?></a><br />
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Forgot Password?', 'business-dashboard' ); ?></a>
        </p>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode for the front-end business dashboard.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string   HTML content for the business dashboard.
     */
    public function business_dashboard_shortcode( $atts ) {
        ob_start();
        if ( ! is_user_logged_in() ) {
            echo '<p>' . __( 'You must be logged in to view this page.', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        $current_user = wp_get_current_user();
        if ( ! in_array( 'business_user', (array) $current_user->roles ) ) {
            echo '<p>' . __( 'You do not have permission to view this page.', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        $business_status = get_user_meta( $current_user->ID, 'business_status', true );
        if ( 'approved' !== $business_status ) {
            echo '<p>' . __( 'Your business account is awaiting approval or has been rejected. You cannot access the dashboard yet.', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        // Handle profile updates
        if ( isset( $_POST['business_profile_nonce'] ) && wp_verify_nonce( $_POST['business_profile_nonce'], 'business_profile_update_action' ) ) {
            $this->process_business_profile_update( $current_user->ID );
            echo '<p class="business-dashboard-success">' . __( 'Profile updated successfully!', 'business-dashboard' ) . '</p>';
        }

        $current_section = isset( $_GET['section'] ) ? $_GET['section'] : 'profile';
        $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

        if ( $is_ajax ) {
            // Only output the content for the requested section during AJAX calls
            $this->render_dashboard_section_content( $current_user->ID, $current_section );
            return ob_get_clean();
        }
        ?>
        <div class="business-dashboard-wrap">
            <div class="business-dashboard-layout">
                <div class="business-dashboard-sidebar">
                    <div class="business-dashboard-logo">
                        <h2><?php _e( 'Business Dashboard', 'business-dashboard' ); ?></h2>
                    </div>
                    <ul>
                        <li><a href="?section=profile" class="business-dashboard-nav-link <?php echo 'profile' === $current_section ? 'active' : ''; ?>" data-section="profile"><?php _e( 'Business Profile', 'business-dashboard' ); ?></a></li>
                        <li><a href="?section=product-sync" class="business-dashboard-nav-link <?php echo 'product-sync' === $current_section ? 'active' : ''; ?>" data-section="product-sync"><?php _e( 'Product Sync', 'business-dashboard' ); ?></a></li>
                        <li><a href="?section=post-creation" class="business-dashboard-nav-link <?php echo 'post-creation' === $current_section ? 'active' : ''; ?>" data-section="post-creation"><?php _e( 'Business Post Creation', 'business-dashboard' ); ?></a></li>
                        <li><a href="?section=settings" class="business-dashboard-nav-link <?php echo 'settings' === $current_section ? 'active' : ''; ?>" data-section="settings"><?php _e( 'Business Profile Settings', 'business-dashboard' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="business-dashboard-nav-link"><?php _e( 'Logout', 'business-dashboard' ); ?></a></li>
                    </ul>
                </div>

                <div class="business-dashboard-content-area" id="business-dashboard-content-area">
                    <?php $this->render_dashboard_section_content( $current_user->ID, $current_section ); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the content for a specific dashboard section.
     *
     * @since    1.0.0
     * @param    int    $user_id    The current user ID.
     * @param    string $section    The section to render.
     */
    private function render_dashboard_section_content( $user_id, $section ) {
        $current_user = get_userdata( $user_id ); // Re-fetch user data for consistency
        if ( 'profile' === $section ) {
            require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-profile-view.php';
        } elseif ( 'product-sync' === $section ) {
            require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-product-sync-section.php';
        } elseif ( 'post-creation' === $section ) {
            require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-post-creation.php';
        } elseif ( 'settings' === $section ) {
            require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-profile-settings.php';
        }
    }

    /**
     * AJAX handler for loading dashboard sections.
     *
     * @since    1.0.0
     */
    public function load_dashboard_section() {
        check_ajax_referer( 'business_dashboard_load_section_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to view this page.', 'business-dashboard' ) );
        }

        $current_user = wp_get_current_user();
        if ( ! in_array( 'business_user', (array) $current_user->roles ) ) {
            wp_send_json_error( __( 'You do not have permission to view this page.', 'business-dashboard' ) );
        }

        $business_status = get_user_meta( $current_user->ID, 'business_status', true );
        if ( 'approved' !== $business_status ) {
            wp_send_json_error( __( 'Your business account is awaiting approval or has been rejected. You cannot access the dashboard yet.', 'business-dashboard' ) );
        }

        $section = isset( $_POST['section'] ) ? sanitize_text_field( $_POST['section'] ) : 'profile';

        ob_start();
        $this->render_dashboard_section_content( $current_user->ID, $section );
        $content = ob_get_clean();

        wp_send_json_success( array( 'content' => $content ) );
    }

    /**
     * AJAX handler for retrying a failed sync log.
     *
     * @since    1.0.0
     */
    public function ajax_retry_sync_log() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'business_product_sync_action' ) ) {
            wp_send_json_error( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $log_index = isset( $_POST['log_id'] ) ? absint( $_POST['log_id'] ) : -1;
        $logs = get_user_meta( $current_user_id, 'business_dashboard_sync_logs', true );

        if ( ! is_array( $logs ) || ! isset( $logs[ $log_index ] ) ) {
            wp_send_json_error( __( 'Invalid sync log ID.', 'business-dashboard' ) );
        }

        $log_entry = $logs[ $log_index ];

        // For simplicity, we'll re-run the last successful sync parameters if available
        // In a real-world scenario, you might store the original sync parameters in the log entry
        $base_sync_url = get_user_meta( $current_user_id, 'sync_url', true );
        $api_key = get_user_meta( $current_user_id, 'api_key', true );
        $consumer_secret = get_user_meta( $current_user_id, 'consumer_secret', true );
        $data_source_type = get_user_meta( $current_user_id, 'data_source_type', true );

        if ( empty( $base_sync_url ) ) {
            wp_send_json_error( __( 'Product sync URL is not set for retry.', 'business-dashboard' ) );
        }

        $sync_url = $this->get_woocommerce_api_endpoint( $base_sync_url );
        $sync_result = $this->perform_product_sync( $current_user_id, $sync_url, $api_key, $consumer_secret, $data_source_type );

        if ( is_wp_error( $sync_result ) ) {
            wp_send_json_error( $sync_result->get_error_message() );
        } else {
            // Update the original log entry status to 'retried' or similar, or just let a new log entry be created
            // For now, we'll just create a new log entry and let the UI refresh
            wp_send_json_success( __( 'Sync log retried successfully!', 'business-dashboard' ) );
        }
    }

    /**
     * AJAX handler for deleting a sync log entry.
     *
     * @since    1.0.0
     */
    public function ajax_delete_sync_log() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'business_product_sync_action' ) ) {
            wp_send_json_error( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $log_index = isset( $_POST['log_id'] ) ? absint( $_POST['log_id'] ) : -1;
        $logs = get_user_meta( $current_user_id, 'business_dashboard_sync_logs', true );

        if ( ! is_array( $logs ) || ! isset( $logs[ $log_index ] ) ) {
            wp_send_json_error( __( 'Invalid sync log ID.', 'business-dashboard' ) );
        }

        // Remove the log entry
        unset( $logs[ $log_index ] );
        $logs = array_values( $logs ); // Re-index the array

        update_user_meta( $current_user_id, 'business_dashboard_sync_logs', $logs );

        wp_send_json_success( __( 'Sync log deleted successfully!', 'business-dashboard' ) );
    }

    /**
     * Display synced products for a business user.
     *
     * @since    1.0.0
     * @param    int    $user_id    The business user ID.
     * @return   string   HTML content for the synced products.
     */
    private function display_synced_products( $user_id ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<p>' . __( 'WooCommerce is not active.', 'business-dashboard' ) . '</p>';
        }

        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_business_user_id',
                    'value'   => $user_id,
                    'compare' => '=',
                ),
            ),
        );
        $products = new WP_Query( $args );
        ob_start();
        if ( $products->have_posts() ) : ?>
            <table class="business-dashboard-synced-products-list">
                <thead>
                    <tr>
                        <th><?php _e( 'Image', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Name', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Price', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Stock', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Last Synced', 'business-dashboard' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ( $products->have_posts() ) : $products->the_post();
                        $_product = wc_get_product( get_the_ID() );
                        $last_synced_timestamp = get_post_meta( get_the_ID(), '_last_synced_timestamp', true );
                        ?>
                        <tr>
                            <td><?php echo $_product->get_image( 'thumbnail' ); ?></td>
                            <td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>" target="_blank"><?php echo esc_html( $_product->get_name() ); ?></a></td>
                            <td><?php echo wp_kses_post( $_product->get_price_html() ); ?></td>
                            <td><?php echo esc_html( $_product->get_stock_quantity() ); ?></td>
                            <td><?php echo esc_html( $last_synced_timestamp ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $last_synced_timestamp ) ) : __( 'N/A', 'business-dashboard' ) ); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e( 'No products synced yet.', 'business-dashboard' ); ?></p>
        <?php endif;
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Display sync logs for a business user.
     *
     * @since    1.0.0
     * @param    int    $user_id    The business user ID.
     * @return   string   HTML content for the sync logs.
     */
    public function display_sync_logs( $user_id ) {
        $logs = get_user_meta( $user_id, 'business_dashboard_sync_logs', true );
        if ( ! is_array( $logs ) ) {
            $logs = array();
        }
        ob_start();
        if ( ! empty( $logs ) ) : ?>
            <table class="business-dashboard-sync-logs-list">
                <thead>
                    <tr>
                        <th><?php _e( 'Last Sync Time', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Status', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Message', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Actions', 'business-dashboard' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_reverse( $logs ) as $index => $log_entry ) : // Display most recent first ?>
                        <tr>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log_entry['timestamp'] ) ) ); ?></td>
                            <td class="status-<?php echo esc_attr( $log_entry['status'] ); ?>"><?php echo esc_html( ucfirst( $log_entry['status'] ) ); ?></td>
                            <td><?php echo esc_html( $log_entry['message'] ); ?></td>
                            <td class="actions">
                                <?php if ( 'failed' === $log_entry['status'] ) : ?>
                                    <button class="retry-button button button-secondary" data-log-id="<?php echo count( $logs ) - 1 - $index; ?>"><?php _e( 'Retry', 'business-dashboard' ); ?></button>
                                <?php endif; ?>
                                <button class="delete-button button button-secondary" data-log-id="<?php echo count( $logs ) - 1 - $index; ?>"><?php _e( 'Delete', 'business-dashboard' ); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e( 'No sync logs available.', 'business-dashboard' ); ?></p>
        <?php endif;
        return ob_get_clean();
    }

    /**
     * Process business profile update from the dashboard.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     */
    public function process_business_profile_update( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        $fields_to_update = array(
            'business_name'        => sanitize_text_field( $_POST['business_name'] ),
            'website_url'          => esc_url_raw( $_POST['website_url'] ),
            'business_description' => sanitize_textarea_field( $_POST['business_description'] ),
            'contact_phone'        => sanitize_text_field( $_POST['contact_phone'] ),
            'sync_url'             => esc_url_raw( $_POST['sync_url'] ),
            'api_key'              => sanitize_text_field( $_POST['api_key'] ),
            'consumer_secret'      => sanitize_text_field( $_POST['consumer_secret'] ),
            'data_source_type'     => sanitize_text_field( $_POST['data_source_type'] ),
            'facebook_url'         => esc_url_raw( $_POST['facebook_url'] ),
            'instagram_url'        => esc_url_raw( $_POST['instagram_url'] ),
            'linkedin_url'         => esc_url_raw( $_POST['linkedin_url'] ),
            'twitter_url'          => esc_url_raw( $_POST['twitter_url'] ),
            'business_url_slug'    => sanitize_title( $_POST['business_url_slug'] ), // Sanitize for URL slug
        );

        // Check for business_url_slug uniqueness before saving
        if ( isset( $fields_to_update['business_url_slug'] ) ) {
            $new_slug = $fields_to_update['business_url_slug'];
            $existing_user_id = $this->get_user_id_by_business_url_slug( $new_slug );

            if ( $existing_user_id && (int) $existing_user_id !== (int) $user_id ) {
                return new WP_Error( 'business_url_slug_taken', __( 'This business URL slug is already taken. Please choose a different one.', 'business-dashboard' ) );
            }
        }

        foreach ( $fields_to_update as $meta_key => $meta_value ) {
            update_user_meta( $user_id, $meta_key, $meta_value );
        }

        // Handle image uploads
        $this->handle_image_upload( $user_id, 'profile_image', 'profile_image' );
        $this->handle_image_upload( $user_id, 'cover_image', 'cover_image' );
    }

    /**
     * Process business profile settings update from the dashboard.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     */
    public function process_business_profile_settings_update( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return new WP_Error( 'permission_denied', __( 'You do not have permission to edit this profile.', 'business-dashboard' ) );
        }

        // Verify nonce
        if ( ! isset( $_POST['business_profile_settings_nonce'] ) || ! wp_verify_nonce( $_POST['business_profile_settings_nonce'], 'business_profile_settings_action' ) ) {
            return new WP_Error( 'nonce_failed', __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $fields_to_update = array(
            'business_name'             => sanitize_text_field( $_POST['business_name'] ),
            'country'                   => sanitize_text_field( $_POST['country'] ),
            'business_type'             => sanitize_text_field( $_POST['business_type'] ),
            'industry'                  => sanitize_text_field( $_POST['industry'] ),
            'established_year'          => absint( $_POST['established_year'] ),
            'business_description'      => sanitize_textarea_field( $_POST['business_description'] ), // Short description
            'full_description'          => sanitize_textarea_field( $_POST['full_description'] ),
            'contact_phone'             => sanitize_text_field( $_POST['contact_phone'] ),
            'website_url'               => esc_url_raw( $_POST['website_url'] ),
            'business_address'          => sanitize_textarea_field( $_POST['business_address'] ),
            'facebook_url'              => esc_url_raw( $_POST['facebook_url'] ),
            'instagram_url'             => esc_url_raw( $_POST['instagram_url'] ),
            'linkedin_url'              => esc_url_raw( $_POST['linkedin_url'] ),
            'twitter_url'               => esc_url_raw( $_POST['twitter_url'] ),
            'business_registration_number' => sanitize_text_field( $_POST['business_registration_number'] ),
            'tax_id'                    => sanitize_text_field( $_POST['tax_id'] ),
        );

        foreach ( $fields_to_update as $meta_key => $meta_value ) {
            update_user_meta( $user_id, $meta_key, $meta_value );
        }

        // Handle image uploads for profile and cover images
        $this->handle_image_upload( $user_id, 'profile_image', 'profile_image' );
        $this->handle_image_upload( $user_id, 'cover_image', 'cover_image' );

        // Handle certificate upload
        if ( ! empty( $_FILES['certificate_upload']['name'] ) ) {
            $certificate_upload_result = $this->handle_file_upload( $user_id, 'certificate_upload', 'certificate_upload', array('pdf', 'jpg', 'jpeg', 'png') );
            if ( is_wp_error( $certificate_upload_result ) ) {
                return $certificate_upload_result;
            }
        }

        return true;
    }

    /**
     * Get user ID by business URL slug.
     *
     * @since    1.0.0
     * @param    string $slug The business URL slug.
     * @return   int|false    User ID if found, false otherwise.
     */
    private function get_user_id_by_business_url_slug( $slug ) {
        $users = get_users( array(
            'meta_key'     => 'business_url_slug',
            'meta_value'   => $slug,
            'number'       => 1,
            'fields'       => 'ID',
        ) );
        return ! empty( $users ) ? $users[0] : false;
    }

    /**
     * AJAX handler for checking business URL slug availability.
     *
     * @since    1.0.0
     */
    public function ajax_check_business_url_availability() {
        check_ajax_referer( 'business_product_sync_action', 'nonce' ); // Using general nonce for now

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();
        $slug = isset( $_POST['business_url_slug'] ) ? sanitize_title( $_POST['business_url_slug'] ) : '';

        if ( empty( $slug ) ) {
            wp_send_json_error( __( 'URL slug cannot be empty.', 'business-dashboard' ) );
        }

        $existing_user_id = $this->get_user_id_by_business_url_slug( $slug );

        $is_available = ( ! $existing_user_id || (int) $existing_user_id === (int) $current_user_id );

        wp_send_json_success( array( 'available' => $is_available ) );
    }

    /**
     * AJAX handler for updating business profile settings.
     *
     * @since    1.0.0
     */
    public function ajax_update_business_profile_settings() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();
        $update_result = $this->process_business_profile_settings_update( $current_user_id );

        if ( is_wp_error( $update_result ) ) {
            wp_send_json_error( $update_result->get_error_message() );
        } else {
            wp_send_json_success( __( 'Profile settings updated successfully!', 'business-dashboard' ) );
        }
    }

    /**
     * Handle generic file uploads for user meta.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     * @param    string $file_input_name The name of the file input field.
     * @param    string $meta_key  The meta key to save the file URL.
     * @param    array  $allowed_exts Allowed file extensions.
     * @return   string|WP_Error    File URL on success, WP_Error on failure.
     */
    private function handle_file_upload( $user_id, $file_input_name, $meta_key, $allowed_exts = array() ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        if ( ! empty( $_FILES[ $file_input_name ]['name'] ) ) {
            $uploaded_file = $_FILES[ $file_input_name ];
            $upload_overrides = array( 'test_form' => false );

            // Check file type
            $file_info = wp_check_filetype( basename( $uploaded_file['name'] ) );
            if ( ! empty( $allowed_exts ) && ! in_array( $file_info['ext'], $allowed_exts ) ) {
                return new WP_Error( 'invalid_file_type', sprintf( __( 'Invalid file type. Allowed types: %s', 'business-dashboard' ), implode( ', ', $allowed_exts ) ) );
            }

            $move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $move_file && ! isset( $move_file['error'] ) ) {
                update_user_meta( $user_id, $meta_key, $move_file['url'] );
                return $move_file['url'];
            } else {
                error_log( 'Business Dashboard File Upload Error: ' . $move_file['error'] );
                return new WP_Error( 'upload_error', __( 'Failed to upload file.', 'business-dashboard' ) . ' ' . $move_file['error'] );
            }
        }
        return new WP_Error( 'no_file_uploaded', __( 'No file uploaded.', 'business-dashboard' ) );
    }

    /**
     * AJAX handler for manual product sync.
     *
     * @since    1.0.0
     */
    public function manual_product_sync() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();

        // Log received POST data for debugging
        error_log( 'Business Dashboard AJAX Sync: POST data received: ' . print_r( $_POST, true ) );

        if ( ! isset( $_POST['business_product_sync_nonce'] ) || ! wp_verify_nonce( $_POST['business_product_sync_nonce'], 'business_product_sync_action' ) ) {
            error_log( 'Business Dashboard AJAX Sync Error: Nonce verification failed for user ' . $current_user_id );
            wp_send_json_error( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $base_sync_url = get_user_meta( $current_user_id, 'sync_url', true );
        $api_key = get_user_meta( $current_user_id, 'api_key', true );
        $consumer_secret = get_user_meta( $current_user_id, 'consumer_secret', true );
        $data_source_type = get_user_meta( $current_user_id, 'data_source_type', true );

        if ( empty( $base_sync_url ) ) {
            error_log( 'Business Dashboard AJAX Sync Error: Product sync URL is not set for user ' . $current_user_id );
            wp_send_json_error( __( 'Product sync URL is not set.', 'business-dashboard' ) );
        }

        $sync_url = $this->get_woocommerce_api_endpoint( $base_sync_url );
        error_log( 'Business Dashboard AJAX Sync: Attempting sync for user ' . $current_user_id . ' from URL: ' . $sync_url );

        $sync_result = $this->perform_product_sync( $current_user_id, $sync_url, $api_key, $consumer_secret, $data_source_type );

        if ( is_wp_error( $sync_result ) ) {
            error_log( 'Business Dashboard AJAX Sync Error for user ' . $current_user_id . ': ' . $sync_result->get_error_message() );
            wp_send_json_error( $sync_result->get_error_message() );
        } else {
            update_user_meta( $current_user_id, 'last_sync_date', current_time( 'mysql' ) );
            error_log( 'Business Dashboard AJAX Sync Success for user ' . $current_user_id );
            
            // Prepare updated content for AJAX response
            $response_data = array(
                'message'       => __( 'Products synced successfully!', 'business-dashboard' ),
                'last_sync'     => get_user_meta( $current_user_id, 'last_sync_date', true ),
                'product_list'  => $this->display_synced_products( $current_user_id ),
                'sync_logs'     => $this->display_sync_logs( $current_user_id ),
            );
            wp_send_json_success( $response_data );
        }
    }

    /**
     * Perform product synchronization from external source.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     * @param    string $sync_url   The URL of the external product data.
     * @param    string $api_key    API key for authentication (optional).
     * @param    string $consumer_secret Consumer Secret for WooCommerce API authentication.
     * @param    string $data_source_type Type of data source (json or csv).
     * @return   true|WP_Error      True on success, WP_Error on failure.
     */
    public function perform_product_sync( $user_id, $sync_url, $api_key = '', $consumer_secret = '', $data_source_type = 'json' ) {
        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'status'    => 'failed',
            'message'   => '',
        );

        // Determine if it's a WooCommerce REST API URL
        $is_woocommerce_api = ( strpos( $sync_url, '/wp-json/wc/v' ) !== false );

        $request_args = array();
        if ( $is_woocommerce_api && ! empty( $api_key ) && ! empty( $consumer_secret ) ) {
            // For WooCommerce REST API, use Basic Auth
            $request_args['headers'] = array(
                'Authorization' => 'Basic ' . base64_encode( $api_key . ':' . $consumer_secret ),
            );
        }

        // Fetch data
        $response = wp_remote_get( $sync_url, $request_args );

        if ( is_wp_error( $response ) ) {
            $log_entry['message'] = __( 'Failed to fetch data from external source.', 'business-dashboard' ) . ' ' . $response->get_error_message();
            $this->add_sync_log( $user_id, $log_entry );
            return new WP_Error( 'sync_error', $log_entry['message'] );
        }

        $body = wp_remote_retrieve_body( $response );
        $products_data = array();

        if ( 'json' === $data_source_type ) {
            $products_data = json_decode( $body, true );
            if ( ! is_array( $products_data ) ) {
                $log_entry['message'] = __( 'Invalid JSON data received.', 'business-dashboard' );
                $this->add_sync_log( $user_id, $log_entry );
                return new WP_Error( 'sync_error', $log_entry['message'] );
            }
        } elseif ( 'csv' === $data_source_type ) {
            $products_data = $this->parse_csv_data( $body );
            if ( is_wp_error( $products_data ) ) {
                $log_entry['message'] = $products_data->get_error_message();
                $this->add_sync_log( $user_id, $log_entry );
                return $products_data;
            }
        } else {
            $log_entry['message'] = __( 'Unsupported data source type.', 'business-dashboard' );
            $this->add_sync_log( $user_id, $log_entry );
            return new WP_Error( 'sync_error', $log_entry['message'] );
        }

        if ( empty( $products_data ) ) {
            $log_entry['message'] = __( 'No product data found.', 'business-dashboard' );
            $this->add_sync_log( $user_id, $log_entry );
            return new WP_Error( 'sync_error', $log_entry['message'] );
        }

        // Process and import products
        $imported_count = 0;
        foreach ( $products_data as $product_data ) {
            $result = $this->import_woocommerce_product( $user_id, $product_data );
            if ( ! is_wp_error( $result ) ) {
                $imported_count++;
            } else {
                error_log( 'Business Dashboard Product Import Error for user ' . $user_id . ': ' . $result->get_error_message() );
            }
        }

        $log_entry['status'] = 'success';
        $log_entry['message'] = sprintf( __( '%d products synced successfully.', 'business-dashboard' ), $imported_count );
        $this->add_sync_log( $user_id, $log_entry );
        return true;
    }

    /**
     * Add a sync log entry for a business user.
     *
     * @since    1.0.0
     * @param    int    $user_id    The business user ID.
     * @param    array  $log_entry  The log entry to add.
     */
    private function add_sync_log( $user_id, $log_entry ) {
        $logs = get_user_meta( $user_id, 'business_dashboard_sync_logs', true );
        if ( ! is_array( $logs ) ) {
            $logs = array();
        }
        $logs[] = $log_entry;
        // Keep only the last 10 logs to prevent meta bloat
        $logs = array_slice( $logs, -10 );
        update_user_meta( $user_id, 'business_dashboard_sync_logs', $logs );
    }

    /**
     * Helper function to construct WooCommerce REST API endpoint.
     *
     * @since    1.0.0
     * @param    string $base_url The base URL of the external website.
     * @return   string The full WooCommerce REST API endpoint.
     */
    private function get_woocommerce_api_endpoint( $base_url ) {
        $base_url = trailingslashit( $base_url );
        if ( strpos( $base_url, '/wp-json/wc/v' ) === false ) {
            return $base_url . 'wp-json/wc/v3/products'; // Assuming v3 and products endpoint
        }
        return $base_url;
    }

    /**
     * Parse CSV data into an array of products.
     *
     * @since    1.0.0
     * @param    string $csv_string The CSV data as a string.
     * @return   array|WP_Error     Array of products on success, WP_Error on failure.
     */
    private function parse_csv_data( $csv_string ) {
        $lines = explode( "\n", $csv_string );
        $header = str_getcsv( array_shift( $lines ) );
        $products = array();

        foreach ( $lines as $line ) {
            if ( empty( trim( $line ) ) ) continue;
            $data = str_getcsv( $line );
            if ( count( $header ) === count( $data ) ) {
                $products[] = array_combine( $header, $data );
            } else {
                error_log( 'Business Dashboard CSV Parse Error: Mismatched column count in line: ' . $line );
            }
        }
        return $products;
    }

    /**
     * Import or update a WooCommerce product.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID (business owner).
     * @param    array  $product_data Product data from external source.
     */
    private function import_woocommerce_product( $user_id, $product_data ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return new WP_Error( 'woocommerce_not_active', __( 'WooCommerce is not active.', 'business-dashboard' ) );
        }

        $sku = isset( $product_data['sku'] ) ? sanitize_text_field( $product_data['sku'] ) : '';
        if ( empty( $sku ) ) {
            error_log( 'Business Dashboard Product Sync Error: Product SKU is missing for user ' . $user_id );
            return new WP_Error( 'product_sku_missing', __( 'Product SKU is missing.', 'business-dashboard' ) );
        }

        // Check if product exists by SKU
        $product_id = wc_get_product_id_by_sku( $sku );
        $product = $product_id ? wc_get_product( $product_id ) : new WC_Product();

        $product->set_name( isset( $product_data['name'] ) ? sanitize_text_field( $product_data['name'] ) : (isset( $product_data['title'] ) ? sanitize_text_field( $product_data['title'] ) : '') );
        $product->set_sku( $sku );
        $product->set_price( isset( $product_data['price'] ) ? wc_format_decimal( $product_data['price'] ) : '' );
        $product->set_regular_price( isset( $product_data['regular_price'] ) ? wc_format_decimal( $product_data['regular_price'] ) : (isset( $product_data['price'] ) ? wc_format_decimal( $product_data['price'] ) : '') );
        $product->set_sale_price( isset( $product_data['sale_price'] ) ? wc_format_decimal( $product_data['sale_price'] ) : '' );
        $product->set_description( isset( $product_data['description'] ) ? wp_kses_post( $product_data['description'] ) : '' );
        $product->set_short_description( isset( $product_data['short_description'] ) ? wp_kses_post( $product_data['short_description'] ) : '' );
        $product->set_stock_quantity( isset( $product_data['stock_quantity'] ) ? absint( $product_data['stock_quantity'] ) : 0 );
        $product->set_manage_stock( true ); // Always manage stock for synced products
        $product->set_status( 'publish' ); // Ensure products are published

        // Categories
        if ( isset( $product_data['categories'] ) ) {
            $categories = is_array( $product_data['categories'] ) ? $product_data['categories'] : explode( ',', $product_data['categories'] );
            $term_ids = array();
            foreach ( $categories as $category_item ) {
                $category_name = is_array( $category_item ) && isset( $category_item['name'] ) ? $category_item['name'] : $category_item;
                $term = get_term_by( 'name', trim( $category_name ), 'product_cat' );
                if ( ! $term ) {
                    $term = wp_insert_term( trim( $category_name ), 'product_cat' );
                    if ( ! is_wp_error( $term ) ) {
                        $term_ids[] = $term['term_id'];
                    }
                } else {
                    $term_ids[] = $term->term_id;
                }
            }
            $product->set_category_ids( $term_ids );
        }

        // Images
        if ( isset( $product_data['images'] ) && is_array( $product_data['images'] ) && ! empty( $product_data['images'] ) ) {
            $gallery_image_ids = array();
            foreach ( $product_data['images'] as $image ) {
                if ( isset( $image['src'] ) && ! empty( $image['src'] ) ) {
                    $image_id = $this->upload_product_image( $image['src'] );
                    if ( $image_id ) {
                        $gallery_image_ids[] = $image_id;
                    }
                }
            }
            if ( ! empty( $gallery_image_ids ) ) {
                $product->set_image_id( array_shift( $gallery_image_ids ) ); // First image as featured
                $product->set_gallery_image_ids( $gallery_image_ids ); // Remaining as gallery
            }
        } elseif ( isset( $product_data['image_url'] ) && ! empty( $product_data['image_url'] ) ) {
            $image_id = $this->upload_product_image( $product_data['image_url'] );
            if ( $image_id ) {
                $product->set_image_id( $image_id );
            }
        }

        $product->save();

        // Link product to business user and update last synced timestamp
        update_post_meta( $product->get_id(), '_business_user_id', $user_id );
        update_post_meta( $product->get_id(), '_last_synced_timestamp', current_time( 'mysql' ) );
        return true;
    }

    /**
     * AJAX handler for changing password.
     *
     * @since    1.0.0
     */
    public function ajax_change_password() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to change your password.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'business_change_password_action' ) ) {
            wp_send_json_error( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        $current_password = isset( $_POST['current_password'] ) ? $_POST['current_password'] : '';
        $new_password = isset( $_POST['new_password'] ) ? $_POST['new_password'] : '';

        if ( empty( $current_password ) || empty( $new_password ) ) {
            wp_send_json_error( __( 'Current and new passwords are required.', 'business-dashboard' ) );
        }

        if ( ! wp_check_password( $current_password, $current_user->user_pass, $current_user_id ) ) {
            wp_send_json_error( __( 'Your current password is incorrect.', 'business-dashboard' ) );
        }

        wp_set_password( $new_password, $current_user_id );
        wp_send_json_success( __( 'Password changed successfully!', 'business-dashboard' ) );
    }

    /**
     * AJAX handler for requesting business verification.
     *
     * @since    1.0.0
     */
    public function ajax_request_verification() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to request verification.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'business_request_verification_action' ) ) {
            wp_send_json_error( __( 'Nonce verification failed.', 'business-dashboard' ) );
        }

        // Check if all required verification fields are filled
        $business_registration_number = get_user_meta( $current_user_id, 'business_registration_number', true );
        $tax_id = get_user_meta( $current_user_id, 'tax_id', true );
        $certificate_upload_url = get_user_meta( $current_user_id, 'certificate_upload', true );

        if ( empty( $business_registration_number ) || empty( $tax_id ) || empty( $certificate_upload_url ) ) {
            wp_send_json_error( __( 'Please fill in all verification information (Registration Number, Tax ID, and upload Certificate) before requesting verification.', 'business-dashboard' ) );
        }

        // Update verification status to pending
        update_user_meta( $current_user_id, 'verification_status', 'pending' );

        // Optionally, notify admin
        $admin_email = get_option( 'admin_email' );
        $user_info = get_userdata( $current_user_id );
        $subject = __( 'Business Verification Request', 'business-dashboard' );
        $message = sprintf(
            __( 'Business user %s (ID: %d) has requested verification. Please review their profile: %s', 'business-dashboard' ),
            $user_info->display_name,
            $current_user_id,
            admin_url( 'user-edit.php?user_id=' . $current_user_id )
        );
        wp_mail( $admin_email, $subject, $message );

        wp_send_json_success( __( 'Verification request submitted successfully! Please await admin review.', 'business-dashboard' ) );
    }

    /**
     * Scheduled product synchronization for all approved businesses.
     *
     * @since    1.0.0
     */
    public function scheduled_product_sync() {
        $args = array(
            'role'       => 'business_user',
            'meta_key'   => 'business_status',
            'meta_value' => 'approved',
            'fields'     => 'ID',
        );
        $approved_business_ids = get_users( $args );

        foreach ( $approved_business_ids as $user_id ) {
            $base_sync_url = get_user_meta( $user_id, 'sync_url', true );
            $api_key = get_user_meta( $user_id, 'api_key', true );
            $consumer_secret = get_user_meta( $user_id, 'consumer_secret', true );
            $data_source_type = get_user_meta( $user_id, 'data_source_type', true );

            if ( ! empty( $base_sync_url ) ) {
                $sync_url = $this->get_woocommerce_api_endpoint( $base_sync_url );
                $sync_result = $this->perform_product_sync( $user_id, $sync_url, $api_key, $consumer_secret, $data_source_type );
                if ( ! is_wp_error( $sync_result ) ) {
                    update_user_meta( $user_id, 'last_sync_date', current_time( 'mysql' ) );
                    // Log success
                    error_log( 'Business Dashboard Scheduled Sync Success for user ' . $user_id );
                } else {
                    // Log error
                    error_log( 'Business Dashboard Scheduled Sync Error for user ' . $user_id . ': ' . $sync_result->get_error_message() );
                }
            }
        }
    }

    /**
     * AJAX handler for creating a new business post.
     *
     * @since    1.0.0
     */
    public function create_business_post() {
        check_ajax_referer( 'business_post_creation_action', 'nonce' );

        if ( ! is_user_logged_in() || ! current_user_can( 'business_user' ) ) {
            wp_send_json_error( __( 'You do not have permission to create posts.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();
        $post_title = sanitize_text_field( $_POST['post_title'] );
        $post_description = sanitize_textarea_field( $_POST['post_description'] );
        $linked_product_id = isset( $_POST['linked_product_id'] ) ? absint( $_POST['linked_product_id'] ) : 0;

        if ( empty( $post_title ) ) {
            wp_send_json_error( __( 'Post title is required.', 'business-dashboard' ) );
        }

        $post_data = array(
            'post_title'    => $post_title,
            'post_content'  => $post_description,
            'post_status'   => 'publish',
            'post_author'   => $current_user_id,
            'post_type'     => 'business_post',
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        // Handle image upload for the post
        $image_id = $this->handle_post_image_upload( $post_id, 'post_image' );
        if ( $image_id ) {
            update_post_meta( $post_id, '_business_post_image_id', $image_id );
            set_post_thumbnail( $post_id, $image_id ); // Set as featured image
        }

        // Link product if provided
        if ( $linked_product_id ) {
            update_post_meta( $post_id, '_linked_product_id', $linked_product_id );
        }

        wp_send_json_success( __( 'Business post created successfully!', 'business-dashboard' ) );
    }

    /**
     * Handle image uploads for business posts.
     *
     * @since    1.0.0
     * @param    int    $post_id    The post ID.
     * @param    string $file_input_name The name of the file input field.
     * @return   int|bool           Attachment ID on success, false on failure.
     */
    private function handle_post_image_upload( $post_id, $file_input_name ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        if ( ! function_exists( 'wp_insert_attachment' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/post.php' );
        }
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        if ( ! empty( $_FILES[ $file_input_name ]['name'] ) ) {
            $uploaded_file = $_FILES[ $file_input_name ];
            $upload_overrides = array( 'test_form' => false );
            $move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $move_file && ! isset( $move_file['error'] ) ) {
                $attachment = array(
                    'guid'           => $move_file['url'],
                    'post_mime_type' => $move_file['type'],
                    'post_title'     => sanitize_file_name( $move_file['file'] ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $move_file['file'], $post_id );
                $attach_data = wp_generate_attachment_metadata( $attach_id, $move_file['file'] );
                wp_update_attachment_metadata( $attach_id, $attach_data );
                return $attach_id;
            } else {
                error_log( 'Business Dashboard Post Image Upload Error: ' . $move_file['error'] );
            }
        }
        return false;
    }

    /**
     * AJAX handler for searching synced products.
     *
     * @since    1.0.0
     */
    public function search_synced_products() {
        check_ajax_referer( 'business_product_search_action', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to perform this action.', 'business-dashboard' ) );
        }

        $current_user_id = get_current_user_id();
        $search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( $_POST['search_term'] ) : '';

        if ( empty( $search_term ) ) {
            wp_send_json_success( array() );
        }

        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            's'              => $search_term,
            'meta_query'     => array(
                array(
                    'key'     => '_business_user_id',
                    'value'   => $current_user_id,
                    'compare' => '=',
                ),
            ),
        );

        $products = new WP_Query( $args );
        $results = array();

        if ( $products->have_posts() ) {
            while ( $products->have_posts() ) {
                $products->the_post();
                $_product = wc_get_product( get_the_ID() );
                $results[] = array(
                    'id'   => get_the_ID(),
                    'name' => $_product->get_name(),
                    'sku'  => $_product->get_sku(),
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success( $results );
    }

    /**
     * Add a custom body class when the business dashboard shortcode is active.
     *
     * @since    1.0.0
     * @param    array $classes The existing body classes.
     * @return   array          Modified body classes.
     */
    public function add_body_class_for_dashboard( $classes ) {
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'business_dashboard' ) ) {
            $classes[] = 'business-dashboard-page';
        }
        return $classes;
    }

    /**
     * Upload product image from URL.
     *
     * @since    1.0.0
     * @param    string $image_url The URL of the image.
     * @return   int|bool           Attachment ID on success, false on failure.
     */
    private function upload_product_image( $image_url ) {
        if ( empty( $image_url ) ) {
            return false;
        }

        // Add support for media uploads
        if ( ! function_exists( 'media_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
        }

        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents( $image_url );
        $filename = basename( $image_url );

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents( $file, $image_data );

        $wp_filetype = wp_check_filetype( $filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( $filename ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

    /**
     * Display business profile by URL slug.
     *
     * This function is hooked into 'template_redirect' to display a business profile
     * when a custom URL slug is matched.
     *
     * @since    1.0.0
     */
    public function display_business_profile_by_url() {
        global $wp_query;
        error_log( 'Business Dashboard: display_business_profile_by_url called.' );

        if ( ! isset( $wp_query->query_vars['business_url_slug'] ) ) {
            error_log( 'Business Dashboard: business_url_slug query var not set.' );
            return;
        }

        $slug = sanitize_title( $wp_query->query_vars['business_url_slug'] );
        error_log( 'Business Dashboard: Detected slug: ' . $slug );

        $user_id = $this->get_user_id_by_business_url_slug( $slug );
        error_log( 'Business Dashboard: User ID for slug ' . $slug . ': ' . ( $user_id ? $user_id : 'Not found' ) );

        if ( ! $user_id ) {
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
            return;
        }

        // Set the current user for the profile view context
        // This is a workaround to make get_current_user_id() work in the partial
        // In a real scenario, you might pass the user_id directly to the partial
        // or use a custom query to fetch user data.
        $GLOBALS['business_dashboard_current_profile_user_id'] = $user_id;

        // Load a custom template for the business profile
        add_filter( 'template_include', array( $this, 'include_business_profile_template' ) );
    }

    /**
     * Include the custom business profile template.
     *
     * @since    1.0.0
     * @param    string $template The path to the template file.
     * @return   string           The path to the custom template file.
     */
    public function include_business_profile_template( $template ) {
        if ( file_exists( BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-public-profile-template.php' ) ) {
            error_log( 'Business Dashboard: Loading custom template: ' . BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-public-profile-template.php' );
            return BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-public-profile-template.php';
        }
        error_log( 'Business Dashboard: Custom template not found, falling back to default: ' . $template );
        return $template;
    }
}
