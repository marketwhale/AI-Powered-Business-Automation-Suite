<?php
/**
 * Provide a public-facing view for the Commerce Management section.
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

$commerce_management_url = 'https://shopnow.marketwhaleai.com/';
?>

<div class="business-dashboard-section business-dashboard-commerce-management">
    <h2><?php _e( 'Commerce Management', 'business-dashboard' ); ?></h2>
    <p><?php _e( 'Manage your products and orders directly from here.', 'business-dashboard' ); ?></p>
    <div class="commerce-management-iframe-wrap">
        <iframe src="<?php echo esc_url( $commerce_management_url ); ?>" class="commerce-management-iframe" frameborder="0"></iframe>
    </div>
</div>
