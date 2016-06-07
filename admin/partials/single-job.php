<?php

/**
 * Job details/edit template
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 *
 * @since      1.0.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */

$job_details = get_post_meta($job->ID, '_decom_job_details', true);
if ($job && $job_details) :
	if ($job->post_status === 'future') {
		$job_class = '';
		$toggle_button_icon = 'dashicons-controls-pause';
		$toggle_button_text = __('Disable', 'export-for-memberpress');
	} else {
		$job_class = 'disabled';
		$toggle_button_icon = 'dashicons-controls-play';
		$toggle_button_text = __('Enable', 'export-for-memberpress');
	}
?>

<li id="defm_job_<?php echo $job->ID; ?>" class="defm-job <?php echo $job_class; ?>">
	<div class="defm-job-preview">
		<h2 class="defm-job-title"><?php echo esc_html($job->post_title); ?></h2>
		<p class="defm-job-description">
			<?php echo Export_For_MemberPress_Admin::job_schedule($job); ?>
		</p>
		<ul class="job-details">
			<li class="defm-job-details-filename" data-decom-filename="<?php echo esc_attr($job_details['filename']); ?>"><span class="dashicons dashicons-media-spreadsheet"></span> <strong><?php _e('File name:', 'export-for-memberpress'); ?></strong> <?php echo esc_html($job_details['filename']); ?></li>
			<li class="defm-job-details-product" data-decom-product="<?php echo esc_attr($job_details['product']); ?>"><span class="dashicons dashicons-groups"></span> <strong><?php _e('Membership:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::$memberships[$job_details['product']]); ?></li>
			<li class="defm-job-details-schedule" data-decom-schedule="<?php echo esc_attr($job_details['schedule']); ?>" data-decom-weekday="<?php echo isset($job_details['weekday']) ? esc_attr($job_details['weekday']) : 0; ?>">
				<span class="dashicons dashicons-calendar"></span>
				<strong><?php _e('Schedule:', 'export-for-memberpress'); ?></strong>
				<?php echo esc_html(Export_For_MemberPress_Admin::$schedules[$job_details['schedule']]); ?>
				<?php if ($job_details['schedule'] === 'week') : ?>
					(<?php echo esc_html(Export_For_MemberPress_Admin::$weekdays[$job_details['weekday']]); ?>)
				<?php endif; ?>
			</li>
			<li class="defm-job-details-ftp-profile" data-decom-ftp-profile="<?php echo esc_attr(isset($job_details['ftp_profile']) ? $job_details['ftp_profile'] : 0 ); ?>">
				<span class="dashicons dashicons-migrate"></span>
				<strong><?php _e('FTP:', 'export-for-memberpress'); ?></strong>
				<?php if (isset($job_details['ftp_profile']) && $profile_name = Export_For_MemberPress_Admin::get_profile_title($job_details['ftp_profile'])) : ?>
					<?php echo esc_html($profile_name); ?>
				<?php else: ?>
					<?php _e('Disabled', 'export-for-memberpress'); ?>
				<?php endif; ?>
			</li>
			<li class="defm-job-details-email-profile" data-decom-email-profile="<?php echo esc_attr(isset($job_details['email_profile']) ? $job_details['email_profile'] : 0 ); ?>">
				<span class="dashicons dashicons-email-alt"></span>
				<strong><?php _e('Email:', 'export-for-memberpress'); ?></strong>
				<?php if (isset($job_details['email_profile']) && $profile_name = Export_For_MemberPress_Admin::get_profile_title($job_details['email_profile'])) : ?>
					<?php echo esc_html(Export_For_MemberPress_Admin::filter_email_text($profile_name)); ?>
				<?php else: ?>
					<?php _e('Disabled', 'export-for-memberpress'); ?>
				<?php endif; ?>
			</li>
		</ul>
	</div>
	<div class="defm-job-edit">
		<div class="defm-job-buttons">
			<button id="defm_scheduled_edit_<?php echo $job->ID; ?>" class="button button-secondary defm-button defm-edit dashicons-before dashicons-edit" data-decom-job="<?php echo $job->ID; ?>">
			    <span class="defm-button-label"><?php _e('Edit', 'export-for-memberpress'); ?></span>
			</button>
			<button id="defm_scheduled_toggle_<?php echo $job->ID; ?>" class="button button-secondary defm-button defm-toggle dashicons-before <?php echo $toggle_button_icon; ?>" data-decom-job="<?php echo $job->ID; ?>">
			    <span class="defm-button-label"><?php echo $toggle_button_text; ?></span>
			</button>
			<button id="defm_scheduled_delete_<?php echo $job->ID; ?>" class="button button-primary defm-button defm-delete dashicons-before dashicons-trash" data-decom-job="<?php echo $job->ID; ?>">
			    <span class="defm-button-label"><?php _e('Delete', 'export-for-memberpress'); ?></span>
			</button>
		</div>
	</div>
</li>

<?php
endif;