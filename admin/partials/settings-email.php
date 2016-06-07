<?php

/**
 * Email profiles template
 *
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */


$profiles = Export_For_MemberPress_Admin::get_profiles('email');
?>


<header class="defm-tab-header">
    <h1 class="defm-tab-panel-title"><?php _e('Email Profiles', 'export-for-memberpress'); ?></h1>
</header>

<section class="defm-tab-panel-content-section">
    <ul id="defm_email_profiles">
        <?php if ($profiles) :

                foreach ($profiles as $profile) {
                     include plugin_dir_path( __FILE__ ) . 'single-email-profile.php';
                 }

             else : ?>
            <li id="defm_no_email_profiles"><?php _e('You haven’t created any email profiles yet.', 'export-for-memberpress'); ?></li>
        <?php endif; ?>
    </ul>
</section>

<footer id="defm_email_profile_footer" class="defm-tab-footer">
    <div id="defm_email_profile_form_wrapper" class="defm-form-wrapper defm-email-profile-form-wrapper">
       <form name="defm_email_profile_form" id="defm_email_profile_form" class="defm-form" method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add-email-profile-form">
            <?php wp_nonce_field('new-email-profile'); ?>
            <div class="defm-profile-name">
                <label for="email_profile_name"><?php _e('Name', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_name" name="email_profile_name" placeholder="Enter a unique name for this email profile" value="" />
            </div>
            <div class="defm-email-from">
                <label for="email_profile_from"><?php _e('From', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_from" name="email_profile_from" value="{admin_email}" />
            </div>
            <div class="defm-email-to">
                <label for="email_profile_to"><?php _e('To', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_to" name="email_profile_to" value="{admin_email}" />
            </div>
            <div class="defm-email-subject">
                <label for="email_profile_subject"><?php _e('Subject', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_subject" name="email_profile_subject" value="<?php _e('New MemberPress report generated on {site_name}', 'export-for-memberpress'); ?>" />
            </div>
            <div class="defm-email-cc">
                <label for="email_profile_cc"><?php _e('Cc', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_cc" name="email_profile_cc" value="" />
            </div>
            <div class="defm-email-bcc">
                <label for="email_profile_bcc"><?php _e('Bcc', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_email_profile_bcc" name="email_profile_bcc" value="" />
            </div>
            <div class="defm-email-text">
                <label for="email_profile_text"><?php _e('Text', 'export-for-memberpress'); ?>:</label>
                <textarea id="defm_email_profile_text" name="email_profile_text"><?php echo esc_textarea(Export_For_MemberPress_Admin::default_email_text()); ?></textarea>
            </div>
            <div class="defm-email-attachment">
                <label for="email_profile_attachment">
                    <input type="checkbox" id="defm_email_profile_attachment" name="email_profile_attachment" />
                    <?php _e('Attach generated report', 'export-for-memberpress'); ?>
                </label>
            </div>
            <div id="defm_email_profile_submit_wrapper" class="defm-submit defm-email-submit">
                <button type="submit" id="defm_email_profile_submit" name="defm_email_profile_submit" class="button button-primary defm-submit defm-button dashicons-before dashicons-yes">
                    <?php _e('Save', 'export-for-memberpress'); ?>
                </button>
                <button id="defm_email_profile_cancel" class="button button-secondary defm-button defm-cancel dashicons-before dashicons-no-alt">
                    <?php _e('Cancel', 'export-for-memberpress'); ?>
                </button>
            </div>
      </form>
    </div>
    <button id="defm_add_email_profile" class="button button-primary defm-button defm-add-email-profile dashicons-before dashicons-plus-alt">
        <?php _e('Add a new email profile', 'export-for-memberpress'); ?>
    </button>
</footer>

<?php
