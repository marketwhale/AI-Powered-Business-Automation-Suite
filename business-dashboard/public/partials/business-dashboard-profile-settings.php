<?php
/**
 * Provide a public-facing view for the Business Profile Settings section.
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
$business_description = get_user_meta( $current_user->ID, 'business_description', true ); // Short description
$full_description = get_user_meta( $current_user->ID, 'full_description', true );
$contact_phone = get_user_meta( $current_user->ID, 'contact_phone', true );
$profile_image_url = get_user_meta( $current_user->ID, 'profile_image', true );
$cover_image_url = get_user_meta( $current_user->ID, 'cover_image', true );
$facebook_url = get_user_meta( $current_user->ID, 'facebook_url', true );
$instagram_url = get_user_meta( $current_user->ID, 'instagram_url', true );
$linkedin_url = get_user_meta( $current_user->ID, 'linkedin_url', true );
$twitter_url = get_user_meta( $current_user->ID, 'twitter_url', true );
$verification_status = get_user_meta( $current_user->ID, 'verification_status', true );
$country = get_user_meta( $current_user->ID, 'country', true );
$business_type = get_user_meta( $current_user->ID, 'business_type', true );
$industry = get_user_meta( $current_user->ID, 'industry', true );
$established_year = get_user_meta( $current_user->ID, 'established_year', true );
$business_address = get_user_meta( $current_user->ID, 'business_address', true );
$business_registration_number = get_user_meta( $current_user->ID, 'business_registration_number', true );
$tax_id = get_user_meta( $current_user->ID, 'tax_id', true );
$certificate_upload_url = get_user_meta( $current_user->ID, 'certificate_upload', true );
$business_url_slug = get_user_meta( $current_user->ID, 'business_url_slug', true ); // New meta field
$base_business_url = 'https://bestbrands.live/';
$full_business_url_preview = $base_business_url . ( ! empty( $business_url_slug ) ? $business_url_slug . '/' : '{your-business-name}/' );

// Determine verification badge
$verification_badge = '';
if ( 'verified' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge verified" title="' . esc_attr__( 'Verified Business', 'business-dashboard' ) . '">✔️</span>';
} elseif ( 'pending' === $verification_status ) {
    $verification_badge = '<span class="business-dashboard-badge pending" title="' . esc_attr__( 'Pending Verification', 'business-dashboard' ) . '">🕓</span>';
}

// Placeholder for country, business type, industry dropdowns - in a real scenario, these would be dynamic
$countries = array( 'USA', 'Canada', 'UK', 'Australia', 'India', 'Germany', 'France' );
$business_types = array( 'Sole Proprietorship', 'Partnership', 'Corporation', 'LLC' );
$industries = array( 'Retail', 'Food & Beverage', 'Technology', 'Healthcare', 'Education', 'Manufacturing', 'Services' );
?>

<div class="business-dashboard-profile-view business-dashboard-profile-settings-page">
    <div class="business-dashboard-cover-image" style="background-image: url('<?php echo esc_url( $cover_image_url ); ?>');">
        <button type="button" class="button button-secondary business-dashboard-upload-cover-button" data-image-type="cover"><?php _e( 'Upload Cover Photo', 'business-dashboard' ); ?></button>
    </div>

    <div class="business-dashboard-profile-card-wrapper">
        <div class="business-dashboard-profile-card">
            <div class="business-dashboard-profile-image-wrap">
                <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr( $business_name ); ?>" class="business-dashboard-profile-image" />
                <button type="button" class="button button-secondary business-dashboard-upload-profile-button" data-image-type="profile"><?php _e( 'Upload Profile Photo', 'business-dashboard' ); ?></button>
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
            <a href="#business-info" class="nav-tab nav-tab-active" data-tab="business-info"><?php _e( 'Business Information', 'business-dashboard' ); ?></a>
            <a href="#contact-details" class="nav-tab" data-tab="contact-details"><?php _e( 'Contact Details', 'business-dashboard' ); ?></a>
            <a href="#social-links" class="nav-tab" data-tab="social-links"><?php _e( 'Social Links', 'business-dashboard' ); ?></a>
            <a href="#account-settings" class="nav-tab" data-tab="account-settings"><?php _e( 'Account Settings', 'business-dashboard' ); ?></a>
            <a href="#verification-info" class="nav-tab" data-tab="verification-info"><?php _e( 'Verification Info', 'business-dashboard' ); ?></a>
        </h2>

        <form id="business-profile-settings-form" method="post" enctype="multipart/form-data">
            <div class="business-dashboard-tab-content" id="business-info-tab">
                <h3><?php _e( 'Business Information', 'business-dashboard' ); ?></h3>
                <div class="form-group">
                    <label for="business_name"><?php _e( 'Business Name', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_name" id="business_name" value="<?php echo esc_attr( $business_name ); ?>" required />
                </div>
                <div class="form-group">
                    <label for="country"><?php _e( 'Country', 'business-dashboard' ); ?></label>
                    <select name="country" id="country">
                        <option value=""><?php _e( 'Select Country', 'business-dashboard' ); ?></option>
                        <?php foreach ( $countries as $c ) : ?>
                            <option value="<?php echo esc_attr( $c ); ?>" <?php selected( $country, $c ); ?>><?php echo esc_html( $c ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="business_type"><?php _e( 'Business Type', 'business-dashboard' ); ?></label>
                    <select name="business_type" id="business_type">
                        <option value=""><?php _e( 'Select Business Type', 'business-dashboard' ); ?></option>
                        <?php foreach ( $business_types as $type ) : ?>
                            <option value="<?php echo esc_attr( $type ); ?>" <?php selected( $business_type, $type ); ?>><?php echo esc_html( $type ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="industry"><?php _e( 'Industry', 'business-dashboard' ); ?></label>
                    <select name="industry" id="industry">
                        <option value=""><?php _e( 'Select Industry', 'business-dashboard' ); ?></option>
                        <?php foreach ( $industries as $ind ) : ?>
                            <option value="<?php echo esc_attr( $ind ); ?>" <?php selected( $industry, $ind ); ?>><?php echo esc_html( $ind ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="established_year"><?php _e( 'Established Year', 'business-dashboard' ); ?></label>
                    <input type="number" name="established_year" id="established_year" value="<?php echo esc_attr( $established_year ); ?>" min="1900" max="<?php echo date('Y'); ?>" />
                </div>
                <div class="form-group">
                    <label for="brand"><?php _e( 'Brand', 'business-dashboard' ); ?></label>
                    <input type="text" name="brand" id="brand" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'brand', true ) ); ?>" placeholder="<?php _e( 'e.g., My Awesome Brand', 'business-dashboard' ); ?>" />
                </div>
                <div class="form-group">
                    <label for="business_url_slug"><?php _e( 'Public Business URL Slug', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_url_slug" id="business_url_slug" value="<?php echo esc_attr( $business_url_slug ); ?>" placeholder="<?php _e( 'e.g., my-awesome-brand', 'business-dashboard' ); ?>" />
                    <p class="description">
                        <?php _e( 'Your public URL will be:', 'business-dashboard' ); ?> <strong id="full-business-url-preview"><?php echo esc_url( $full_business_url_preview ); ?></strong>
                        <span id="business-url-availability" style="margin-left: 10px;"></span>
                    </p>
                </div>
                <div class="form-group">
                    <label for="business_description"><?php _e( 'Short Description (max 160 chars)', 'business-dashboard' ); ?></label>
                    <textarea name="business_description" id="business_description" rows="3" maxlength="160"><?php echo esc_textarea( $business_description ); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="full_description"><?php _e( 'Full Description', 'business-dashboard' ); ?></label>
                    <textarea name="full_description" id="full_description" rows="10"><?php echo esc_textarea( $full_description ); ?></textarea>
                </div>
            </div>

            <div class="business-dashboard-tab-content" id="contact-details-tab" style="display:none;">
                <h3><?php _e( 'Contact Details', 'business-dashboard' ); ?></h3>
                <div class="form-group">
                    <label for="contact_phone"><?php _e( 'Contact Phone (+Country Code)', 'business-dashboard' ); ?></label>
                    <input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr( $contact_phone ); ?>" />
                </div>
                <div class="form-group">
                    <label for="website_url"><?php _e( 'Website URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="website_url" id="website_url" value="<?php echo esc_attr( $website_url ); ?>" />
                </div>
                <div class="form-group">
                    <label for="business_address"><?php _e( 'Business Address', 'business-dashboard' ); ?></label>
                    <textarea name="business_address" id="business_address" rows="5"><?php echo esc_textarea( $business_address ); ?></textarea>
                </div>
            </div>

            <div class="business-dashboard-tab-content" id="social-links-tab" style="display:none;">
                <h3><?php _e( 'Social Links', 'business-dashboard' ); ?></h3>
                <div class="form-group">
                    <label for="facebook_url"><?php _e( 'Facebook URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="facebook_url" id="facebook_url" value="<?php echo esc_attr( $facebook_url ); ?>" />
                </div>
                <div class="form-group">
                    <label for="instagram_url"><?php _e( 'Instagram URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="instagram_url" id="instagram_url" value="<?php echo esc_attr( $instagram_url ); ?>" />
                </div>
                <div class="form-group">
                    <label for="linkedin_url"><?php _e( 'LinkedIn URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="linkedin_url" id="linkedin_url" value="<?php echo esc_attr( $linkedin_url ); ?>" />
                </div>
                <div class="form-group">
                    <label for="twitter_url"><?php _e( 'X (Twitter) URL', 'business-dashboard' ); ?></label>
                    <input type="url" name="twitter_url" id="twitter_url" value="<?php echo esc_attr( $twitter_url ); ?>" />
                </div>
            </div>

            <div class="business-dashboard-tab-content" id="account-settings-tab" style="display:none;">
                <h3><?php _e( 'Account Settings', 'business-dashboard' ); ?></h3>
                <div class="form-group">
                    <label for="user_email_readonly"><?php _e( 'Email', 'business-dashboard' ); ?></label>
                    <input type="email" id="user_email_readonly" value="<?php echo esc_attr( $current_user->user_email ); ?>" readonly />
                </div>
                <div class="form-group">
                    <button type="button" id="change-password-button" class="button button-secondary"><?php _e( 'Change Password', 'business-dashboard' ); ?></button>
                </div>
            </div>

            <div class="business-dashboard-tab-content" id="verification-info-tab" style="display:none;">
                <h3><?php _e( 'Verification Information', 'business-dashboard' ); ?></h3>
                <div class="form-group">
                    <label for="business_registration_number"><?php _e( 'Business Registration Number', 'business-dashboard' ); ?></label>
                    <input type="text" name="business_registration_number" id="business_registration_number" value="<?php echo esc_attr( $business_registration_number ); ?>" />
                </div>
                <div class="form-group">
                    <label for="tax_id"><?php _e( 'Tax ID', 'business-dashboard' ); ?></label>
                    <input type="text" name="tax_id" id="tax_id" value="<?php echo esc_attr( $tax_id ); ?>" />
                </div>
                <div class="form-group">
                    <label for="certificate_upload"><?php _e( 'Upload Certificate (PDF/Image)', 'business-dashboard' ); ?></label>
                    <input type="file" name="certificate_upload" id="certificate_upload" accept=".pdf,image/*" />
                    <?php if ( $certificate_upload_url ) : ?><a href="<?php echo esc_url( $certificate_upload_url ); ?>" target="_blank"><?php _e( 'View Current Certificate', 'business-dashboard' ); ?></a><?php endif; ?>
                </div>
                <div class="form-group">
                    <button type="button" id="request-verification-button" class="button button-primary" <?php echo ( 'pending' !== $verification_status ) ? 'disabled' : ''; ?>><?php _e( 'Request Verification', 'business-dashboard' ); ?></button>
                    <span id="verification-status-display" style="margin-left: 10px; font-weight: bold;"><?php echo esc_html( ucfirst( $verification_status ) ); ?></span>
                </div>
            </div>

            <p class="submit">
                <?php wp_nonce_field( 'business_profile_settings_action', 'business_profile_settings_nonce' ); ?>
                <input type="submit" name="submit_profile_settings" id="submit-profile-settings" class="button button-primary" value="<?php _e( 'Save Changes', 'business-dashboard' ); ?>" />
            </p>
            <div id="profile-settings-feedback" class="business-dashboard-message"></div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" class="business-dashboard-modal" style="display:none;">
    <div class="business-dashboard-modal-content">
        <span class="business-dashboard-modal-close">&times;</span>
        <h2><?php _e( 'Change Password', 'business-dashboard' ); ?></h2>
        <form id="change-password-form">
            <p>
                <label for="current_password"><?php _e( 'Current Password', 'business-dashboard' ); ?></label>
                <input type="password" name="current_password" id="current_password" required />
            </p>
            <p>
                <label for="new_password"><?php _e( 'New Password', 'business-dashboard' ); ?></label>
                <input type="password" name="new_password" id="new_password" required />
            </p>
            <p>
                <label for="confirm_new_password"><?php _e( 'Confirm New Password', 'business-dashboard' ); ?></label>
                <input type="password" name="confirm_new_password" id="confirm_new_password" required />
            </p>
            <p>
                <?php wp_nonce_field( 'business_change_password_action', 'business_change_password_nonce' ); ?>
                <input type="submit" value="<?php _e( 'Change Password', 'business-dashboard' ); ?>" class="button button-primary" />
            </p>
            <div id="password-change-feedback" class="business-dashboard-message"></div>
        </form>
    </div>
</div>

<!-- Generic Image Upload Modal -->
<div id="image-upload-modal" class="business-dashboard-modal" style="display:none;">
    <div class="business-dashboard-modal-content">
        <span class="business-dashboard-modal-close">&times;</span>
        <h2 id="image-upload-modal-title"><?php _e( 'Upload Image', 'business-dashboard' ); ?></h2>
        <form id="image-upload-form" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="upload_image_file"><?php _e( 'Select Image', 'business-dashboard' ); ?></label>
                <input type="file" name="upload_image_file" id="upload_image_file" accept="image/*" required />
                <div id="image-upload-preview" class="image-preview" style="display: none;">
                    <img src="" alt="<?php _e( 'Image Preview', 'business-dashboard' ); ?>" />
                </div>
            </div>
            <p>
                <input type="hidden" name="image_type" id="image_type" value="" />
                <?php wp_nonce_field( 'business_image_upload_action', 'business_image_upload_nonce' ); ?>
                <input type="submit" value="<?php _e( 'Upload and Save', 'business-dashboard' ); ?>" class="button button-primary" id="upload-image-submit" />
            </p>
            <div id="image-upload-feedback" class="business-dashboard-message"></div>
        </form>
    </div>
</div>
