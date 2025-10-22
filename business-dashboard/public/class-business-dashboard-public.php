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
            echo '<p>' . __( 'You are already logged in.', 'business-dashboard' ) . '</p>';
            return ob_get_clean();
        }

        if ( isset( $_POST['business_register_nonce'] ) && wp_verify_nonce( $_POST['business_register_nonce'], 'business_register_action' ) ) {
            $errors = $this->process_business_registration();
            if ( empty( $errors ) ) {
                echo '<p class="business-dashboard-success">' . __( 'Your registration has been submitted and is awaiting admin approval.', 'business-dashboard' ) . '</p>';
                return ob_get_clean();
            } else {
                foreach ( $errors as $error ) {
                    echo '<p class="business-dashboard-error">' . esc_html( $error ) . '</p>';
                }
            }
        }
        ?>
        <div class="business-dashboard-form-wrap">
            <h2><?php _e( 'Business Registration', 'business-dashboard' ); ?></h2>
            <form method="post" enctype="multipart/form-data">
                <p>
                    <label for="business_name"><?php _e( 'Business Name', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_name" id="business_name" required value="<?php echo isset( $_POST['business_name'] ) ? esc_attr( $_POST['business_name'] ) : ''; ?>" />
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
                    <label for="website_url"><?php _e( 'Website URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="website_url" id="website_url" value="<?php echo isset( $_POST['website_url'] ) ? esc_attr( $_POST['website_url'] ) : ''; ?>" />
                </p>
                <p>
                    <label for="business_description"><?php _e( 'Business Description', 'business-dashboard' ); ?></label>
                    <textarea name="business_description" id="business_description" rows="5"><?php echo isset( $_POST['business_description'] ) ? esc_textarea( $_POST['business_description'] ) : ''; ?></textarea>
                </p>
                <p>
                    <label for="profile_image"><?php _e( 'Profile Image', 'business-dashboard' ); ?></label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" />
                </p>
                <p>
                    <label for="cover_image"><?php _e( 'Cover Image', 'business-dashboard' ); ?></label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" />
                </p>
                <p>
                    <label for="contact_phone"><?php _e( 'Contact Phone', 'business-dashboard' ); ?></label>
                    <input type="text" name="contact_phone" id="contact_phone" value="<?php echo isset( $_POST['contact_phone'] ) ? esc_attr( $_POST['contact_phone'] ) : ''; ?>" />
                </p>
                <p>
                    <input type="submit" name="submit_registration" value="<?php _e( 'Register', 'business-dashboard' ); ?>" />
                </p>
                <?php wp_nonce_field( 'business_register_action', 'business_register_nonce' ); ?>
            </form>
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
        $user_email = sanitize_email( $_POST['user_email'] );
        $user_pass = $_POST['user_pass'];
        $website_url = esc_url_raw( $_POST['website_url'] );
        $business_description = sanitize_textarea_field( $_POST['business_description'] );
        $contact_phone = sanitize_text_field( $_POST['contact_phone'] );

        if ( empty( $business_name ) ) {
            $errors[] = __( 'Business Name is required.', 'business-dashboard' );
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
        update_user_meta( $user_id, 'website_url', $website_url );
        update_user_meta( $user_id, 'business_description', $business_description );
        update_user_meta( $user_id, 'contact_phone', $contact_phone );
        update_user_meta( $user_id, 'business_status', 'pending' ); // Default status

        // Handle image uploads
        $this->handle_image_upload( $user_id, 'profile_image', 'profile_image' );
        $this->handle_image_upload( $user_id, 'cover_image', 'cover_image' );

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
        ?>
        <div class="business-dashboard-wrap">
            <h1><?php _e( 'Business Dashboard', 'business-dashboard' ); ?></h1>

            <div class="business-dashboard-layout">
                <div class="business-dashboard-sidebar">
                    <ul>
                        <li><a href="?section=profile" class="<?php echo 'profile' === $current_section ? 'active' : ''; ?>"><?php _e( 'Business Profile', 'business-dashboard' ); ?></a></li>
                        <li><a href="?section=product-sync" class="<?php echo 'product-sync' === $current_section ? 'active' : ''; ?>"><?php _e( 'Product Sync', 'business-dashboard' ); ?></a></li>
                    </ul>
                </div>

                <div class="business-dashboard-content-area">
                    <?php if ( 'profile' === $current_section ) : ?>
                        <!-- Business Profile Section -->
                        <div class="business-dashboard-section">
                            <h2><?php _e( 'Business Profile', 'business-dashboard' ); ?></h2>
                            <form method="post" enctype="multipart/form-data">
                                <p>
                                    <label for="business_name"><?php _e( 'Business Name', 'business-dashboard' ); ?></label>
                                    <input type="text" name="business_name" id="business_name" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'business_name', true ) ); ?>" required />
                                </p>
                                <p>
                                    <label for="website_url"><?php _e( 'Website URL', 'business-dashboard' ); ?></label>
                                    <input type="url" name="website_url" id="website_url" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'website_url', true ) ); ?>" />
                                </p>
                                <p>
                                    <label for="business_description"><?php _e( 'Business Description', 'business-dashboard' ); ?></label>
                                    <textarea name="business_description" id="business_description" rows="5"><?php echo esc_textarea( get_user_meta( $current_user->ID, 'business_description', true ) ); ?></textarea>
                                </p>
                                <p>
                                    <label for="contact_phone"><?php _e( 'Contact Phone', 'business-dashboard' ); ?></label>
                                    <input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'contact_phone', true ) ); ?>" />
                                </p>
                                <p>
                                    <label for="profile_image"><?php _e( 'Profile Image', 'business-dashboard' ); ?></label>
                                    <input type="file" name="profile_image" id="profile_image" accept="image/*" />
                                    <?php
                                    $profile_image_url = get_user_meta( $current_user->ID, 'profile_image', true );
                                    if ( $profile_image_url ) {
                                        echo '<img src="' . esc_url( $profile_image_url ) . '" style="max-width: 100px; height: auto;" />';
                                    }
                                    ?>
                                </p>
                                <p>
                                    <label for="cover_image"><?php _e( 'Cover Image', 'business-dashboard' ); ?></label>
                                    <input type="file" name="cover_image" id="cover_image" accept="image/*" />
                                    <?php
                                    $cover_image_url = get_user_meta( $current_user->ID, 'cover_image', true );
                                    if ( $cover_image_url ) {
                                        echo '<img src="' . esc_url( $cover_image_url ) . '" style="max-width: 200px; height: auto;" />';
                                    }
                                    ?>
                                </p>
                                <p>
                                    <input type="submit" name="save_profile" value="<?php _e( 'Save Changes', 'business-dashboard' ); ?>" />
                                </p>
                                <?php wp_nonce_field( 'business_profile_update_action', 'business_profile_nonce' ); ?>
                            </form>
                        </div>
                    <?php elseif ( 'product-sync' === $current_section ) : ?>
                        <!-- Product Sync Section -->
                        <div class="business-dashboard-section">
                            <h2><?php _e( 'Product Sync', 'business-dashboard' ); ?></h2>
                            <form method="post">
                                <p>
                                    <label for="sync_url"><?php _e( 'External Website URL', 'business-dashboard' ); ?></label>
                                    <input type="url" name="sync_url" id="sync_url" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'sync_url', true ) ); ?>" class="regular-text" />
                                </p>
                                <p>
                                    <label for="api_key"><?php _e( 'Consumer Key (if required)', 'business-dashboard' ); ?></label>
                                    <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'api_key', true ) ); ?>" class="regular-text" />
                                </p>
                                <p>
                                    <label for="consumer_secret"><?php _e( 'Consumer Secret (if required)', 'business-dashboard' ); ?></label>
                                    <input type="text" name="consumer_secret" id="consumer_secret" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'consumer_secret', true ) ); ?>" class="regular-text" />
                                </p>
                                <p>
                                    <label for="data_source_type"><?php _e( 'Data Source Type', 'business-dashboard' ); ?></label>
                                    <select name="data_source_type" id="data_source_type">
                                        <option value="json" <?php selected( get_user_meta( $current_user->ID, 'data_source_type', true ), 'json' ); ?>><?php _e( 'JSON REST API', 'business-dashboard' ); ?></option>
                                        <option value="csv" <?php selected( get_user_meta( $current_user->ID, 'data_source_type', true ), 'csv' ); ?>><?php _e( 'CSV Feed', 'business-dashboard' ); ?></option>
                                    </select>
                                </p>
                                <p>
                                    <input type="submit" name="sync_products_manual" value="<?php _e( 'Sync Now', 'business-dashboard' ); ?>" class="button button-primary" />
                                </p>
                                <?php wp_nonce_field( 'business_product_sync_action', 'business_product_sync_nonce' ); ?>
                            </form>

                            <h3><?php _e( 'Synced Products', 'business-dashboard' ); ?></h3>
                            <p><?php _e( 'Last Sync:', 'business-dashboard' ); ?> <span id="last-sync-date"><?php echo esc_html( get_user_meta( $current_user->ID, 'last_sync_date', true ) ); ?></span></p>
                            <div id="synced-product-list">
                                <?php echo $this->display_synced_products( $current_user->ID ); ?>
                            </div>

                            <h3><?php _e( 'Sync Logs', 'business-dashboard' ); ?></h3>
                            <div id="sync-logs-display">
                                <?php echo $this->display_sync_logs( $current_user->ID ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
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
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Image', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Name', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'SKU', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Price', 'business-dashboard' ); ?></th>
                        <th><?php _e( 'Stock', 'business-dashboard' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ( $products->have_posts() ) : $products->the_post();
                        $_product = wc_get_product( get_the_ID() );
                        ?>
                        <tr>
                            <td><?php echo $_product->get_image( 'thumbnail' ); ?></td>
                            <td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>" target="_blank"><?php echo esc_html( $_product->get_name() ); ?></a></td>
                            <td><?php echo esc_html( $_product->get_sku() ); ?></td>
                            <td><?php echo wp_kses_post( $_product->get_price_html() ); ?></td>
                            <td><?php echo esc_html( $_product->get_stock_quantity() ); ?></td>
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
    private function display_sync_logs( $user_id ) {
        $logs = get_user_meta( $user_id, 'business_dashboard_sync_logs', true );
        if ( empty( $logs ) || ! is_array( $logs ) ) {
            return '<p>' . __( 'No sync logs available.', 'business-dashboard' ) . '</p>';
        }

        ob_start();
        echo '<ul class="business-dashboard-sync-logs">';
        foreach ( array_reverse( $logs ) as $log ) { // Show most recent first
            $status_class = ( $log['status'] === 'success' ) ? 'business-dashboard-success' : 'business-dashboard-error';
            echo '<li><span class="' . esc_attr( $status_class ) . '">' . esc_html( ucfirst( $log['status'] ) ) . '</span> - ' . esc_html( $log['timestamp'] ) . ': ' . esc_html( $log['message'] ) . '</li>';
        }
        echo '</ul>';
        return ob_get_clean();
    }

    /**
     * Process business profile update from the dashboard.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     */
    private function process_business_profile_update( $user_id ) {
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
        );

        foreach ( $fields_to_update as $meta_key => $meta_value ) {
            update_user_meta( $user_id, $meta_key, $meta_value );
        }

        // Handle image uploads
        $this->handle_image_upload( $user_id, 'profile_image', 'profile_image' );
        $this->handle_image_upload( $user_id, 'cover_image', 'cover_image' );
    }

    /**
     * Manually trigger product sync for a business.
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

        // Link product to business user
        update_post_meta( $product->get_id(), '_business_user_id', $user_id );
        return true;
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
}
