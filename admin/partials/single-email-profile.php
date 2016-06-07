<?php

/**
 * Email profile details/edit template
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
		<h2 class="defm-profile-title"><?php echo esc_html(Export_For_MemberPress_Admin::filter_email_text($profile->post_title)); ?></h2>
		<ul class="profile-details">
			<li class="defm-profile-details-from" data-decom-from="<?php echo esc_attr($profile_details['from']); ?>"><span class="dashicons dashicons-businessman"></span> <strong><?php _e('From:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::filter_email($profile_details['from'])); ?></li>
			<li class="defm-profile-details-to" data-decom-to="<?php echo esc_attr($profile_details['to']); ?>"><span class="dashicons dashicons-groups"></span> <strong><?php _e('To:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::filter_email($profile_details['to'])); ?></li>
			<li class="defm-profile-details-subject" data-decom-subject="<?php echo esc_attr($profile_details['subject']); ?>"><span class="dashicons dashicons-shield-alt"></span> <strong><?php _e('Subject:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::filter_email_text($profile_details['subject'])); ?></li>
			<?php if ($profile_details['cc']) : ?>
				<li class="defm-profile-details-cc" data-decom-cc="<?php echo esc_attr($profile_details['cc']); ?>"><span class="dashicons dashicons-groups"></span> <strong><?php _e('Cc:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::filter_email($profile_details['cc'])); ?></li>
			<?php endif; ?>
			<?php if ($profile_details['bcc']) : ?>
				<li class="defm-profile-details-bcc" data-decom-bcc="<?php echo esc_attr($profile_details['bcc']); ?>"><span class="dashicons dashicons-groups"></span> <strong><?php _e('Bcc:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::filter_email($profile_details['bcc'])); ?></li>
			<?php endif; ?>
			<li class="defm-profile-details-attachment" data-decom-attachment="<?php echo esc_attr($profile_details['attachment']); ?>">
				<span class="dashicons dashicons-media-spreadsheet"></span>
				<strong><?php _e('Attachment:', 'export-for-memberpress'); ?></strong>
				<?php if ($profile_details['attachment']) {
						_e('Yes', 'export-for-memberpress');
					} else {
						_e('No', 'export-for-memberpress');
					}?>
			</li>
		</ul>
		<p class="defm-email-text-title"><span class="dashicons dashicons-format-aside"></span> <strong><?php _e('Text:', 'export-for-memberpress'); ?></strong></p>
		<div class="defm-email-profile-description" data-decom-text="<?php echo esc_attr($profile->post_content); ?>"><?php echo apply_filters('the_content', Export_For_MemberPress_Admin::filter_email_text($profile->post_content)); ?></div>
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