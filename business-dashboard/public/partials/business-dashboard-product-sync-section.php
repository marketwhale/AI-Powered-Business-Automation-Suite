<?php
/**
 * Provide a public-facing view for the Product Sync section.
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
$sync_url = get_user_meta( $current_user->ID, 'sync_url', true );
$api_key = get_user_meta( $current_user->ID, 'api_key', true );
$consumer_secret = get_user_meta( $current_user->ID, 'consumer_secret', true );
$data_source_type = get_user_meta( $current_user->ID, 'data_source_type', true );
$last_sync_date = get_user_meta( $current_user->ID, 'last_sync_date', true );

// Instantiate Business_Dashboard_Public to access its methods
$public_class = new Business_Dashboard_Public( 'business-dashboard', BUSINESS_DASHBOARD_VERSION );
?>

<div class="business-dashboard-section business-dashboard-product-sync-section">
    <h2><?php _e( 'Product Synchronization', 'business-dashboard' ); ?></h2>

    <div class="business-dashboard-tabs-wrap">
        <h3 class="nav-tab-wrapper">
            <a href="#sync-settings" class="nav-tab nav-tab-active" data-tab="sync-settings"><?php _e( 'Sync Settings', 'business-dashboard' ); ?></a>
            <a href="#synced-products" class="nav-tab" data-tab="synced-products"><?php _e( 'Synced Products', 'business-dashboard' ); ?></a>
            <a href="#sync-logs" class="nav-tab" data-tab="sync-logs"><?php _e( 'Sync Logs', 'business-dashboard' ); ?></a>
        </h3>

        <div class="business-dashboard-tab-content" id="sync-settings-tab">
            <h3><?php _e( 'External Data Source Configuration', 'business-dashboard' ); ?></h3>
            <form id="business-product-sync-form" method="post" class="business-dashboard-sync-form">
                <div class="form-group">
                    <label for="sync_url"><?php _e( 'External Website URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="sync_url" id="sync_url" value="<?php echo esc_attr( $sync_url ); ?>" class="regular-text" placeholder="https://shopnow.marketwhaleai.com/" />
                </div>
                <div class="form-group">
                    <label for="api_key"><?php _e( 'Consumer Key (if required)', 'business-dashboard' ); ?></label>
                    <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" />
                </div>
                <div class="form-group">
                    <label for="consumer_secret"><?php _e( 'Consumer Secret (if required)', 'business-dashboard' ); ?></label>
                    <input type="text" name="consumer_secret" id="consumer_secret" value="<?php echo esc_attr( $consumer_secret ); ?>" class="regular-text" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" />
                </div>
                <div class="form-group">
                    <label for="data_source_type"><?php _e( 'Data Source Type', 'business-dashboard' ); ?></label>
                    <select name="data_source_type" id="data_source_type">
                        <option value="json" <?php selected( $data_source_type, 'json' ); ?>><?php _e( 'JSON REST API', 'business-dashboard' ); ?></option>
                        <option value="csv" <?php selected( $data_source_type, 'csv' ); ?>><?php _e( 'CSV Feed', 'business-dashboard' ); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <?php wp_nonce_field( 'business_product_sync_action', 'business_product_sync_nonce' ); ?>
                    <button type="submit" name="sync_products_manual" id="sync-products-manual-button" class="button button-primary"><?php _e( 'Sync Products Now', 'business-dashboard' ); ?></button>
                </div>
                <div id="sync-feedback" class="business-dashboard-message"></div>
                <p><?php _e( 'Last Sync:', 'business-dashboard' ); ?> <span id="last-sync-date"><?php echo esc_html( $last_sync_date ); ?></span></p>
            </form>
        </div>

        <div class="business-dashboard-tab-content" id="synced-products-tab" style="display:none;">
            <h3><?php _e( 'Your Synced Products', 'business-dashboard' ); ?></h3>
            <p><?php _e( 'Toggle the switch to publish or unpublish products on your public profile.', 'business-dashboard' ); ?></p>
            <div id="synced-product-list">
                <?php echo $public_class->display_synced_products( $current_user->ID ); ?>
            </div>
        </div>

        <div class="business-dashboard-tab-content" id="sync-logs-tab" style="display:none;">
            <h3><?php _e( 'Synchronization Logs', 'business-dashboard' ); ?></h3>
            <div id="sync-logs-display">
                <?php echo $public_class->display_sync_logs( $current_user->ID ); ?>
            </div>
        </div>
    </div>
</div>
