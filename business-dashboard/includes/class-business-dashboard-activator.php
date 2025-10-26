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

        // Register custom post type for business posts
        self::register_business_post_type();

        // Add custom rewrite rules
        self::add_rewrite_rules();
    }

    /**
     * Register the custom post type for Business Posts.
     *
     * @since    1.0.0
     */
    public static function register_business_post_type() {
        $labels = array(
            'name'                  => _x( 'Business Posts', 'Post Type General Name', 'business-dashboard' ),
            'singular_name'         => _x( 'Business Post', 'Post Type Singular Name', 'business-dashboard' ),
            'menu_name'             => __( 'Business Posts', 'business-dashboard' ),
            'name_admin_bar'        => __( 'Business Post', 'business-dashboard' ),
            'archives'              => __( 'Business Post Archives', 'business-dashboard' ),
            'attributes'            => __( 'Business Post Attributes', 'business-dashboard' ),
            'parent_item_colon'     => __( 'Parent Business Post:', 'business-dashboard' ),
            'all_items'             => __( 'All Business Posts', 'business-dashboard' ),
            'add_new_item'          => __( 'Add New Business Post', 'business-dashboard' ),
            'add_new'               => __( 'Add New', 'business-dashboard' ),
            'new_item'              => __( 'New Business Post', 'business-dashboard' ),
            'edit_item'             => __( 'Edit Business Post', 'business-dashboard' ),
            'update_item'           => __( 'Update Business Post', 'business-dashboard' ),
            'view_item'             => __( 'View Business Post', 'business-dashboard' ),
            'view_items'            => __( 'View Business Posts', 'business-dashboard' ),
            'search_items'          => __( 'Search Business Posts', 'business-dashboard' ),
            'not_found'             => __( 'Not found', 'business-dashboard' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'business-dashboard' ),
            'featured_image'        => __( 'Featured Image', 'business-dashboard' ),
            'set_featured_image'    => __( 'Set featured image', 'business-dashboard' ),
            'remove_featured_image' => __( 'Remove featured image', 'business-dashboard' ),
            'use_featured_image'    => __( 'Use as featured image', 'business-dashboard' ),
            'insert_into_item'      => __( 'Insert into business post', 'business-dashboard' ),
            'uploaded_to_this_item' => __( 'Uploaded to this business post', 'business-dashboard' ),
            'items_list'            => __( 'Business Posts list', 'business-dashboard' ),
            'items_list_navigation' => __( 'Business Posts list navigation', 'business-dashboard' ),
            'filter_items_list'     => __( 'Filter business posts list', 'business-dashboard' ),
        );
        $args = array(
            'label'                 => __( 'Business Post', 'business-dashboard' ),
            'description'           => __( 'Business posts created by business users.', 'business-dashboard' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-megaphone',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true, // Enable for Gutenberg and REST API
        );
        register_post_type( 'business_post', $args );
    }

    /**
     * Add custom rewrite rules for business profiles.
     *
     * @since    1.0.0
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule(
            '^business/([^/]+)/?$',
            'index.php?business_url_slug=$matches[1]',
            'top'
        );
        flush_rewrite_rules();
    }
}
