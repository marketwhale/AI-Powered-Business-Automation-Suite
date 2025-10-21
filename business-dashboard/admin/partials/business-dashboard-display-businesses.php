<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Business_Dashboard
 * @subpackage Business_Dashboard/admin/partials
 */

// Get all business users
$args = array(
    'role'       => 'business_user',
    'orderby'    => 'registered',
    'order'      => 'DESC',
    'fields'     => 'all_with_meta',
);
$business_users = get_users( $args );
?>

<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php _e( 'Business Name', 'business-dashboard' ); ?></th>
            <th><?php _e( 'Email', 'business-dashboard' ); ?></th>
            <th><?php _e( 'Website', 'business-dashboard' ); ?></th>
            <th><?php _e( 'Registration Date', 'business-dashboard' ); ?></th>
            <th><?php _e( 'Status', 'business-dashboard' ); ?></th>
            <th><?php _e( 'Actions', 'business-dashboard' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty( $business_users ) ) : ?>
            <?php foreach ( $business_users as $user ) :
                $business_name = get_user_meta( $user->ID, 'business_name', true );
                $website_url = get_user_meta( $user->ID, 'website_url', true );
                $business_status = get_user_meta( $user->ID, 'business_status', true );
                ?>
                <tr>
                    <td><?php echo esc_html( $business_name ); ?></td>
                    <td><?php echo esc_html( $user->user_email ); ?></td>
                    <td><a href="<?php echo esc_url( $website_url ); ?>" target="_blank"><?php echo esc_html( $website_url ); ?></a></td>
                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) ); ?></td>
                    <td><?php echo esc_html( ucfirst( $business_status ) ); ?></td>
                    <td>
                        <?php if ( 'pending' === $business_status ) : ?>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=business_dashboard_approve_business&user_id=' . $user->ID ), 'business_dashboard_approve_business_nonce' ) ); ?>" class="button button-primary"><?php _e( 'Approve', 'business-dashboard' ); ?></a>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=business_dashboard_reject_business&user_id=' . $user->ID ), 'business_dashboard_reject_business_nonce' ) ); ?>" class="button button-secondary"><?php _e( 'Reject', 'business-dashboard' ); ?></a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>" class="button"><?php _e( 'View Profile', 'business-dashboard' ); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6"><?php _e( 'No businesses registered yet.', 'business-dashboard' ); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
