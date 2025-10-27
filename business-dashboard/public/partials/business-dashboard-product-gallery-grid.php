<?php
/**
 * Provide a public-facing view for the Product Gallery Grid section.
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

// Query for business posts with images
$args = array(
    'post_type'      => 'business_post', // Custom post type for business posts
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'author'         => $current_user_id,
    'meta_query'     => array(
        array(
            'key'     => '_business_post_image_id', // Meta key for the uploaded image
            'compare' => 'EXISTS',
        ),
    ),
);
$business_posts = new WP_Query( $args );
?>

<div class="business-dashboard-product-grid-wrapper">
    <div class="business-dashboard-product-gallery-grid">
        <?php if ( $business_posts->have_posts() ) : ?>
            <?php while ( $business_posts->have_posts() ) : $business_posts->the_post();
                $image_id = get_post_meta( get_the_ID(), '_business_post_image_id', true );
                $linked_product_id = get_post_meta( get_the_ID(), '_linked_product_id', true );
                $product_name = '';
                $product_price = '';
                $product_link = '';

                if ( $linked_product_id ) {
                    $_product = wc_get_product( $linked_product_id );
                    if ( $_product ) {
                        $product_name = $_product->get_name();
                        $product_price = $_product->get_price_html();
                        $product_link = get_permalink( $linked_product_id );
                    }
                }
                ?>
                <div class="business-dashboard-grid-card">
                    <div class="business-dashboard-grid-image-wrap">
                        <?php if ( $image_id ) : ?>
                            <?php echo wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => 'business-dashboard-grid-image' ) ); ?>
                        <?php endif; ?>
                        <?php if ( $product_link ) : ?>
                            <div class="business-dashboard-grid-overlay">
                                <span class="business-dashboard-overlay-price"><?php echo wp_kses_post( $product_price ); ?></span>
                                <a href="<?php echo esc_url( $product_link ); ?>" class="business-dashboard-overlay-button"><?php _e( 'View Details', 'business-dashboard' ); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="business-dashboard-grid-title"><?php the_title(); ?></h3>
                    <?php if ( $product_name ) : ?>
                        <p class="business-dashboard-grid-product-name"><?php echo esc_html( $product_name ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php _e( 'No business posts with images found.', 'business-dashboard' ); ?></p>
        <?php endif; ?>
    </div>
</div>
