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

<div class="business-dashboard-section business-dashboard-post-creation">
    <h2><?php _e( 'Create New Business Post', 'business-dashboard' ); ?></h2>

    <form id="business-post-creation-form" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="post_title"><?php _e( 'Post Title', 'business-dashboard' ); ?></label>
            <input type="text" name="post_title" id="post_title" required placeholder="<?php _e( 'Enter post title', 'business-dashboard' ); ?>" />
        </div>
        <div class="form-group">
            <label for="post_description"><?php _e( 'Description (max 160 chars)', 'business-dashboard' ); ?></label>
            <textarea name="post_description" id="post_description" rows="5" maxlength="160" placeholder="<?php _e( 'A short description for your post...', 'business-dashboard' ); ?>"></textarea>
        </div>
        <div class="form-group">
            <label for="post_image"><?php _e( 'Upload Photo', 'business-dashboard' ); ?></label>
            <input type="file" name="post_image" id="post_image" accept="image/*" required />
            <div id="post-image-preview" class="image-preview" style="display: none;">
                <img src="" alt="<?php _e( 'Image Preview', 'business-dashboard' ); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label for="linked_product_search"><?php _e( 'Link to Synced Product (search by name/SKU)', 'business-dashboard' ); ?></label>
            <input type="text" name="linked_product_search" id="linked_product_search" placeholder="<?php _e( 'Start typing product name or SKU...', 'business-dashboard' ); ?>" />
            <input type="hidden" name="linked_product_id" id="linked_product_id" value="" />
            <div id="product-search-results" class="business-dashboard-search-results"></div>
            <div id="selected-product-display" class="business-dashboard-selected-product"></div>
        </div>
        <div class="form-group">
            <input type="submit" name="publish_post" id="publish-post-button" value="<?php _e( 'Publish Post', 'business-dashboard' ); ?>" class="button button-primary" />
        </div>
        <?php wp_nonce_field( 'business_post_creation_action', 'business_post_creation_nonce' ); ?>
    </form>
    <div id="post-creation-feedback" class="business-dashboard-message"></div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#post_image').on('change', function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#post-image-preview img').attr('src', e.target.result);
                    $('#post-image-preview').show();
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#post-image-preview').hide();
                $('#post-image-preview img').attr('src', '');
            }
        });
    });
</script>
