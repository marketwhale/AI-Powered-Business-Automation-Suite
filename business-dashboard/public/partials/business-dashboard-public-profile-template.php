<?php
/**
 * Public-facing template for displaying a single business profile.
 *
 * This template is loaded when a custom business URL slug is matched.
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

// Get the user ID from the global variable set by the plugin
$user_id = isset( $GLOBALS['business_dashboard_current_profile_user_id'] ) ? $GLOBALS['business_dashboard_current_profile_user_id'] : 0;

if ( ! $user_id ) {
    // Fallback to 404 if user ID is not set (should not happen if rewrite rules work)
    status_header( 404 );
    nocache_headers();
    include( get_query_template( '404' ) );
    exit;
}

$profile_user = get_userdata( $user_id );

if ( ! $profile_user || ! in_array( 'business_user', (array) $profile_user->roles ) ) {
    // If user not found or not a business user, show 404
    status_header( 404 );
    nocache_headers();
    include( get_query_template( '404' ) );
    exit;
}

// Fetch business data
$business_name = get_user_meta( $user_id, 'business_name', true );
$website_url = get_user_meta( $user_id, 'website_url', true );
$business_description = get_user_meta( $user_id, 'business_description', true ); // Short description
$full_description = get_user_meta( $user_id, 'full_description', true );
$contact_phone = get_user_meta( $user_id, 'contact_phone', true );
$profile_image_url = get_user_meta( $user_id, 'profile_image', true );
$cover_image_url = get_user_meta( $user_id, 'cover_image', true );
$facebook_url = get_user_meta( $user_id, 'facebook_url', true );
$instagram_url = get_user_meta( $user_id, 'instagram_url', true );
$linkedin_url = get_user_meta( $user_id, 'linkedin_url', true );
$twitter_url = get_user_meta( $user_id, 'twitter_url', true );
$verification_status = get_user_meta( $user_id, 'verification_status', true );

// Determine verification badge
$verification_badge = '';
if ( 'verified' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge verified" title="' . esc_attr__( 'Verified Business', 'business-dashboard' ) . '">✔️</span>';
} elseif ( 'pending' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge pending" title="' . esc_attr__( 'Pending Verification', 'business-dashboard' ) . '">🕓</span>';
}

// Instantiate Business_Dashboard_Public to access its methods for displaying products
$public_class = new Business_Dashboard_Public( 'business-dashboard', BUSINESS_DASHBOARD_VERSION );

get_header(); // WordPress header
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <div class="business-dashboard-profile-view public-profile">
            <div class="business-dashboard-cover-image" style="background-image: url('<?php echo esc_url( $cover_image_url ); ?>');"></div>

            <div class="business-dashboard-profile-card-wrapper">
                <div class="business-dashboard-profile-card">
                    <div class="business-dashboard-profile-image-wrap">
                        <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr( $business_name ); ?>" class="business-dashboard-profile-image" />
                    </div>
                    <div class="business-dashboard-profile-details">
                        <h1 class="business-dashboard-business-name">
                            <?php echo esc_html( $business_name ); ?> <?php echo $verification_badge; ?>
                        </h1>
                        <?php if ( $contact_phone ) : ?>
                            <p class="business-dashboard-contact-phone"><a href="tel:<?php echo esc_attr( $contact_phone ); ?>"><?php echo esc_html( $contact_phone ); ?></a></p>
                        <?php endif; ?>
                        <?php if ( $website_url ) : ?>
                            <p class="business-dashboard-website-url"><a href="<?php echo esc_url( $website_url ); ?>" target="_blank"><?php echo esc_html( $website_url ); ?></a></p>
                        <?php endif; ?>
                        <div class="business-dashboard-social-links">
                            <?php if ( $facebook_url ) : ?><a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" class="social-icon facebook">F</a><?php endif; ?>
                            <?php if ( $instagram_url ) : ?><a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" class="social-icon instagram">I</a><?php endif; ?>
                            <?php if ( $linkedin_url ) : ?><a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" class="social-icon linkedin">L</a><?php endif; ?>
                            <?php if ( $twitter_url ) : ?><a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" class="social-icon twitter">X</a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ( $business_description ) : ?>
                <p class="business-dashboard-short-description"><?php echo esc_html( $business_description ); ?></p>
            <?php endif; ?>

            <div class="business-dashboard-tabs-wrap">
                <h2 class="nav-tab-wrapper">
                    <a href="#full-description" class="nav-tab nav-tab-active" data-tab="full-description"><?php _e( 'About Us', 'business-dashboard' ); ?></a>
                    <a href="#products" class="nav-tab" data-tab="products"><?php _e( 'Products', 'business-dashboard' ); ?></a>
                    <a href="#posts" class="nav-tab" data-tab="posts"><?php _e( 'Posts', 'business-dashboard' ); ?></a>
                </h2>

                <div class="business-dashboard-tab-content" id="full-description-tab">
                    <?php if ( $full_description ) : ?>
                        <p><?php echo nl2br( esc_html( $full_description ) ); ?></p>
                    <?php else : ?>
                        <p><?php _e( 'No detailed description available.', 'business-dashboard' ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="business-dashboard-tab-content" id="products-tab" style="display:none;">
                    <h3><?php _e( 'Our Products', 'business-dashboard' ); ?></h3>
                    <?php echo $public_class->display_synced_products( $user_id ); ?>
                </div>

                <div class="business-dashboard-tab-content" id="posts-tab" style="display:none;">
                    <h3><?php _e( 'Our Latest Posts', 'business-dashboard' ); ?></h3>
                    <?php
                    // Query for business posts by this user
                    $args = array(
                        'post_type'      => 'business_post',
                        'post_status'    => 'publish',
                        'posts_per_page' => 10, // Display latest 10 posts
                        'author'         => $user_id,
                    );
                    $business_posts = new WP_Query( $args );

                    if ( $business_posts->have_posts() ) : ?>
                        <div class="business-dashboard-product-gallery-grid">
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
                                                <a href="<?php echo esc_url( $product_link ); ?>" class="business-dashboard-overlay-button"><?php _e( 'View Product', 'business-dashboard' ); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="business-dashboard-grid-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <?php if ( $product_name ) : ?>
                                        <p class="business-dashboard-grid-product-name"><?php echo esc_html( $product_name ); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <p><?php _e( 'No business posts available.', 'business-dashboard' ); ?></p>
                    <?php endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<script>
    jQuery(document).ready(function($) {
        // Handle tab switching for public profile
        $(document).on('click', '.business-dashboard-profile-view.public-profile .nav-tab-wrapper .nav-tab', function(e) {
            e.preventDefault();
            var $this = $(this);
            var targetTab = $this.data('tab');

            $('.business-dashboard-profile-view.public-profile .nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
            $this.addClass('nav-tab-active');

            $('.business-dashboard-profile-view.public-profile .business-dashboard-tab-content').hide();
            $('#' + targetTab + '-tab').show();
        });

        // Set initial active tab
        var initialPublicTab = new URLSearchParams(window.location.hash.substring(1)).get('tab') || 'full-description';
        $('.business-dashboard-profile-view.public-profile .nav-tab-wrapper .nav-tab[data-tab="' + initialPublicTab + '"]').click();
    });
</script>

<?php
get_footer(); // WordPress footer
