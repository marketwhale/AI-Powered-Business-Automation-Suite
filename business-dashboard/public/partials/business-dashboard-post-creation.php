<?php
/**
 * Provide a public-facing view for the Business Post Creation section.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$current_user_id = get_current_user_id();
?>

<div class="business-dashboard-section">
    <h2><?php _e( 'Create New Business Post', 'business-dashboard' ); ?></h2>

    <form id="business-post-creation-form" method="post" enctype="multipart/form-data">
        <p>
            <label for="post_title"><?php _e( 'Post Title', 'business-dashboard' ); ?></label>
            <input type="text" name="post_title" id="post_title" required />
        </p>
        <p>
            <label for="post_description"><?php _e( 'Description (max 160 chars)', 'business-dashboard' ); ?></label>
            <textarea name="post_description" id="post_description" rows="5" maxlength="160"></textarea>
        </p>
        <p>
            <label for="post_image"><?php _e( 'Upload Photo', 'business-dashboard' ); ?></label>
            <input type="file" name="post_image" id="post_image" accept="image/*" required />
        </p>
        <p>
            <label for="linked_product_search"><?php _e( 'Link to Synced Product (search by name/SKU)', 'business-dashboard' ); ?></label>
            <input type="text" name="linked_product_search" id="linked_product_search" placeholder="<?php _e( 'Start typing product name or SKU...', 'business-dashboard' ); ?>" />
            <input type="hidden" name="linked_product_id" id="linked_product_id" value="" />
            <div id="product-search-results" class="business-dashboard-search-results"></div>
            <div id="selected-product-display" class="business-dashboard-selected-product"></div>
        </p>
        <p>
            <input type="submit" name="publish_post" id="publish-post-button" value="<?php _e( 'Publish Post', 'business-dashboard' ); ?>" class="button button-primary" />
        </p>
        <?php wp_nonce_field( 'business_post_creation_action', 'business_post_creation_nonce' ); ?>
    </form>
    <div id="post-creation-feedback" class="business-dashboard-message"></div>
</div>
