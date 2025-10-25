<?php
/**
 * Provide a public-facing view for the Business Profile View section.
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

$current_user = wp_get_current_user();
$business_name = get_user_meta( $current_user->ID, 'business_name', true );
$website_url = get_user_meta( $current_user->ID, 'website_url', true );
$business_description = get_user_meta( $current_user->ID, 'business_description', true );
$contact_phone = get_user_meta( $current_user->ID, 'contact_phone', true );
$profile_image_url = get_user_meta( $current_user->ID, 'profile_image', true );
$cover_image_url = get_user_meta( $current_user->ID, 'cover_image', true );
$facebook_url = get_user_meta( $current_user->ID, 'facebook_url', true );
$instagram_url = get_user_meta( $current_user->ID, 'instagram_url', true );
$linkedin_url = get_user_meta( $current_user->ID, 'linkedin_url', true );
$twitter_url = get_user_meta( $current_user->ID, 'twitter_url', true );
$verification_status = get_user_meta( $current_user->ID, 'verification_status', true );

// Determine verification badge
$verification_badge = '';
if ( 'verified' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge verified" title="' . esc_attr__( 'Verified Business', 'business-dashboard' ) . '">✔️</span>';
} elseif ( 'pending' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge pending" title="' . esc_attr__( 'Pending Verification', 'business-dashboard' ) . '">🕓</span>';
}
?>

<div class="business-dashboard-profile-view">
    <div class="business-dashboard-cover-image" style="background-image: url('<?php echo esc_url( $cover_image_url ); ?>');"></div>

    <div class="business-dashboard-profile-card-wrapper">
        <div class="business-dashboard-profile-card">
            <div class="business-dashboard-profile-image-wrap">
                <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr( $business_name ); ?>" class="business-dashboard-profile-image" />
            </div>
            <div class="business-dashboard-profile-details">
                <h2 class="business-dashboard-business-name">
                    <?php echo esc_html( $business_name ); ?> <?php echo $verification_badge; ?>
                </h2>
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
        <p class="business-dashboard-short-description"><?php echo esc_html( substr( $business_description, 0, 160 ) ); ?></p>
    <?php endif; ?>

    <div class="business-dashboard-tabs-wrap">
        <h2 class="nav-tab-wrapper">
            <a href="?section=profile&tab=synced-products" class="nav-tab <?php echo ( ! isset( $_GET['tab'] ) || $_GET['tab'] === 'synced-products' ) ? 'nav-tab-active' : ''; ?>" data-tab="synced-products"><?php _e( 'Synced Products', 'business-dashboard' ); ?></a>
            <a href="?section=profile&tab=product-gallery" class="nav-tab <?php echo ( isset( $_GET['tab'] ) && $_GET['tab'] === 'product-gallery' ) ? 'nav-tab-active' : ''; ?>" data-tab="product-gallery"><?php _e( 'Product Gallery', 'business-dashboard' ); ?></a>
        </h2>

        <div class="business-dashboard-tab-content">
            <?php
            $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'synced-products';
            if ( 'synced-products' === $current_tab ) {
                echo $this->display_synced_products( $current_user->ID ); // Assuming this method exists in Business_Dashboard_Public
            } elseif ( 'product-gallery' === $current_tab ) {
                require_once BUSINESS_DASHBOARD_PLUGIN_DIR . 'public/partials/business-dashboard-product-gallery-grid.php';
            }
            ?>
        </div>
    </div>
</div>
