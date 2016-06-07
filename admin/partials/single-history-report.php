<?php

/**
 * Report details template
 *
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */

$report_details = get_post_meta($report->ID, '_decom_report_details', true);
$report_category = wp_get_object_terms($report->ID, 'decom_report_categories', array('fields' => 'slugs'));

if ($report && $report_details) :
	$report_class = isset($report_details['job_id']) ? 'defm-auto-report defm-generated-by-' . $report_details['job_id'] : in_array('group', $report_category) ? 'defm-group-report' : 'defm-manual-report';
?>

<li id="defm_report_<?php echo $report->ID; ?>" class="defm-report <?php echo $report_class; ?>"  data-decom-report="<?php echo $report->ID; ?>">
	<div class="defm-report-preview">
		<h2 class="defm-report-title"><?php echo esc_html($report->post_title); ?> (#<?php echo $report->ID; ?>)</h2>
		<p class="defm-report-description"><?php echo apply_filters('the_content', $report->post_content); ?></p>
		<ul class="report-details">
			<li><span class="dashicons dashicons-clock"></span> <strong><?php _e('Time of export:', 'export-for-memberpress'); ?></strong> <?php echo get_post_time(get_option('date_format') . ' ' . get_option('time_format'), false, $report->ID); ?></li>
			<li><span class="dashicons dashicons-groups"></span> <strong><?php _e('Membership:', 'export-for-memberpress'); ?></strong> <?php echo esc_html(Export_For_MemberPress_Admin::$memberships[$report_details['product']]); ?></li>
			<li>
				<span class="dashicons dashicons-calendar"></span>
				<strong><?php _e('Start date:', 'export-for-memberpress'); ?></strong>
				<?php echo esc_html(date('l, F j, Y', strtotime($report_details['date_start']))); ?>
			</li>
			<li>
				<span class="dashicons dashicons-calendar"></span>
				<strong><?php _e('End date:', 'export-for-memberpress'); ?></strong>
				<?php echo esc_html(date('l, F j, Y', strtotime($report_details['date_end']))); ?>
			</li>
		</ul>
	</div>
	<div class="defm-report-edit">
		<div class="defm-report-buttons">
			<a href="<?php echo esc_attr($report_details['report_url']); ?>" id="defm_report_download_<?php echo $report->ID; ?>" class="defm-download-link button button-download defm-button dashicons-before dashicons-download" data-decom-report="<?php echo $report->ID; ?>">
			    <span class="defm-button-label"><?php _e('Download', 'export-for-memberpress'); ?></span>
			</a>
			<button id="defm_report_delete_<?php echo $report->ID; ?>" class="button button-primary defm-button defm-delete dashicons-before dashicons-trash" data-decom-report="<?php echo $report->ID; ?>">
			    <span class="defm-button-label"><?php _e('Delete', 'export-for-memberpress'); ?></span>
			</button>
		</div>
	</div>
</li>

<?php
endif;