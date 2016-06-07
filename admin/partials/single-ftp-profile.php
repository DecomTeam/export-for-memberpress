<?php

/**
 * Ftp profile details/edit template
 *
 *
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */

$profile_details = get_post_meta($profile->ID, '_decom_profile_details', true);
if ($profile && $profile_details) :
?>

<li id="defm_profile_<?php echo $profile->ID; ?>" class="defm-profile">
	<div class="defm-profile-preview">
		<h2 class="defm-profile-title"><?php echo esc_html($profile->post_title); ?></h2>
		<p class="defm-profile-description"><?php echo apply_filters('the_content', $profile->post_content); ?></p>
		<ul class="profile-details">
			<li class="defm-profile-details-server" data-decom-server="<?php echo esc_attr($profile_details['server']); ?>"><span class="dashicons dashicons-networking"></span> <strong><?php _e('Server:', 'export-for-memberpress'); ?></strong> <?php echo esc_html($profile_details['server']); ?></li>
			<li class="defm-profile-details-username" data-decom-username="<?php echo esc_attr($profile_details['username']); ?>"><span class="dashicons dashicons-admin-users"></span> <strong><?php _e('Username:', 'export-for-memberpress'); ?></strong> <?php echo esc_html($profile_details['username']); ?></li>
			<li class="defm-profile-details-password" data-decom-password="<?php echo esc_attr('********'); ?>"><span class="dashicons dashicons-lock"></span> <strong><?php _e('Password:', 'export-for-memberpress'); ?></strong> <?php echo esc_html('********'); ?></li>
		</ul>
	</div>
	<div class="defm-profile-edit">
		<div class="defm-profile-buttons">
			<button id="defm_profile_edit_<?php echo $profile->ID; ?>" class="button button-secondary defm-button defm-edit dashicons-before dashicons-edit" data-decom-profile="<?php echo $profile->ID; ?>">
			    <span class="defm-button-label"><?php _e('Edit', 'export-for-memberpress'); ?></span>
			</button>
			<button id="defm_profile_delete_<?php echo $profile->ID; ?>" class="button button-primary defm-button defm-delete dashicons-before dashicons-trash" data-decom-profile="<?php echo $profile->ID; ?>">
			    <span class="defm-button-label"><?php _e('Delete', 'export-for-memberpress'); ?></span>
			</button>
		</div>
	</div>
</li>

<?php
endif;