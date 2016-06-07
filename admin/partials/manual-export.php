<?php

/**
 * Manual export template
 *
 * @link       http://example.com
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */

?>

    <div id="defm_notice" class="defm-download-report updated">
        <?php if (isset($_POST['defm_submit']) && $report_url = Export_For_MemberPress_Admin::generate_report()) : ?>
            <h3><?php _e('Your report has been generated.', 'export-for-memberpress'); ?></h3>
            <p class="defm-download-button">
                <a href="<?php echo esc_url($report_url); ?>" class="defm-download-link button button-download defm-button dashicons-before dashicons-download">Download report</a>
            </p>
        <?php endif; ?>
    </div>
    <header class="defm-tab-header">
        <h1 class="defm-tab-panel-title"><?php _e('Export Now', 'export-for-memberpress'); ?></h1>
    </header>

    <section class="defm-tab-panel-content-section">
        <div class="defm-form-wrapper">
           <form name="defm_options_form" id="defm_options_form" class="defm-form" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="process-form">
                <?php wp_nonce_field('update-options'); ?>
                <div class="defm-file-name">
                    <label for="filename"><?php _e('Filename', 'export-for-memberpress'); ?>:</label>
                    <input type="text" id="defm_filename" name="filename" value="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                </div>
                <div class="defm-select-membership">
                    <label for="products"><?php _e('Membership', 'export-for-memberpress'); ?>:</label>
                    <?php Export_For_MemberPress_Admin::membership_options('defm_product'); ?>
                </div>
                <div class="defm-select-week">
                    <label for="week"><?php _e('Week', 'export-for-memberpress'); ?>:</label>
                    <select name="week" class="defm-period-select" id="defm_week">
                        <option value="0"><?php _e('This week', 'export-for-memberpress'); ?></option>
                        <option value="-1"><?php _e('Last week', 'export-for-memberpress'); ?></option>
                        <option value="-2"><?php _e('Week before last', 'export-for-memberpress'); ?></option>
                        <option value="1"><?php _e('Custom date range...', 'export-for-memberpress'); ?></option>
                    </select>
                </div>
                <div class="defm-date-range" id="defm_date_range" style="display: none;">
                    <div class="defm-date-start">
                        <label for="start-date"><?php _e('Start date', 'export-for-memberpress'); ?>:</label>
                        <input type="text" id="defm_start_date" name="start_date" value="<?php echo date('m/d/Y', strtotime('- 1 week')); ?>" class="defm-datepicker" />
                    </div>
                    <div class="defm-date-end">
                        <label for="end-date"><?php _e('End date', 'export-for-memberpress'); ?>:</label>
                        <input type="text" id="defm_end_date" name="end_date" value="<?php echo date('m/d/Y'); ?>" class="defm-datepicker" />
                    </div>
                </div>
                <div class="defm-submit">
                    <input type="submit" id="defm_submit" name="defm_submit" class="button button-primary defm-submit" value="<?php _e('Export', 'export-for-memberpress'); ?>">
                </div>
          </form>
        </div>
    </section>

<?php
