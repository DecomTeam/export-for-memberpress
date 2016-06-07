<?php

/**
 * Schedule export template
 *
 *
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */

$jobs = Export_For_MemberPress_Admin::get_jobs();

?>

    <header class="defm-tab-header">
        <h1 class="defm-tab-panel-title"><?php _e('Scheduled Reports', 'export-for-memberpress'); ?></h1>
    </header>

    <section class="defm-tab-panel-content-section">
        <ul id="defm_scheduled_reports">
            <?php if ($jobs) :

                    foreach ($jobs as $job) {
                         include plugin_dir_path( __FILE__ ) . 'single-job.php';
                     }

                 else : ?>
                <li id="defm_no_jobs"><?php _e('You haven’t created any scheduled reports yet.', 'export-for-memberpress'); ?></li>
            <?php endif; ?>
        </ul>
    </section>

    <footer id="defm_scheduled_footer" class="defm-tab-footer">
        <div id="defm_scheduled_report_form_wrapper" class="defm-form-wrapper defm-scheduled-report-form-wrapper">
           <form name="defm_scheduled_report_form" id="defm_scheduled_report_form" class="defm-form" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add-scheduled-report-form">
                <?php wp_nonce_field('new-scheduled-report'); ?>
                <div class="defm-report-name">
                    <label for="scheduled_report_name"><?php _e('Name', 'export-for-memberpress'); ?>:</label>
                    <input type="text" id="defm_scheduled_report_name" name="scheduled_report_name" placeholder="Enter a unique name for this scheduled report" value="" />
                </div>
                <div class="defm-file-name">
                    <label for="scheduled_filename"><?php _e('File name', 'export-for-memberpress'); ?>:</label>
                    <input type="text" id="defm_scheduled_filename" name="scheduled_filename" value="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                </div>
                <div class="defm-select-membership">
                    <label for="products"><?php _e('Membership', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::membership_options('defm_scheduled_product'); ?>
                </div>
                <div class="defm-select-schedule">
                    <label for="schedule"><?php _e('Schedule', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::schedule_options('defm_schedule'); ?>
                </div>
                <div id="defm_schedule_weekday_wrapper" class="defm-select-weekday" style="display: none;">
                    <label for="schedule_weekday"><?php _e('On', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::weekday_options('defm_schedule_weekday'); ?>
                </div>
                <div id="defm_schedule_ftp_wrapper" class="defm-select-ftp-profile">
                    <label for="schedule_ftp_profile"><?php _e('FTP', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::ftp_options('defm_schedule_ftp_profile'); ?>
                </div>
                <div id="defm_schedule_email_wrapper" class="defm-select-email-profile">
                    <label for="schedule_email_profile"><?php _e('Email', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::email_options('defm_schedule_email_profile'); ?>
                </div>
                <div id="defm_scheduled_submit_wrapper" class="defm-submit defm-scheduled-submit">
                    <button type="submit" id="defm_scheduled_submit" name="defm_scheduled_submit" class="button button-primary defm-submit defm-button dashicons-before dashicons-yes">
                        <?php _e('Save', 'export-for-memberpress'); ?>
                    </button>
                    <button id="defm_scheduled_cancel" class="button button-secondary defm-button defm-cancel dashicons-before dashicons-no-alt">
                        <?php _e('Cancel', 'export-for-memberpress'); ?>
                    </button>
                </div>
          </form>
        </div>
        <button id="defm_add_scheduled_report" class="button button-primary defm-button defm-add-scheduled-report dashicons-before dashicons-plus-alt">
            <?php _e('Add a new scheduled report', 'export-for-memberpress'); ?>
        </button>
    </footer>

<?php
