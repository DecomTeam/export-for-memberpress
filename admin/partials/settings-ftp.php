<?php

/**
 * Ftp profiles template
 *
 * @since      1.2.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */


$profiles = Export_For_MemberPress_Admin::get_profiles('ftp');

?>


<header class="defm-tab-header">
    <h1 class="defm-tab-panel-title"><?php _e('FTP Profiles', 'export-for-memberpress'); ?></h1>
</header>

<section class="defm-tab-panel-content-section">
    <ul id="defm_ftp_profiles">
        <?php if ($profiles) :

                foreach ($profiles as $profile) {
                     include plugin_dir_path( __FILE__ ) . 'single-ftp-profile.php';
                 }

             else : ?>
            <li id="defm_no_ftp_profiles"><?php _e('You haven’t created any FTP profiles yet.', 'export-for-memberpress'); ?></li>
        <?php endif; ?>
    </ul>
</section>

<footer id="defm_ftp_profile_footer" class="defm-tab-footer">
    <div id="defm_ftp_profile_form_wrapper" class="defm-form-wrapper defm-ftp-profile-form-wrapper">
       <form name="defm_ftp_profile_form" id="defm_ftp_profile_form" class="defm-form" method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add-ftp-profile-form">
            <?php wp_nonce_field('new-ftp-profile'); ?>
            <div class="defm-profile-name">
                <label for="ftp_profile_name"><?php _e('Name', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_ftp_profile_name" name="ftp_profile_name" placeholder="Enter a unique name for this FTP profile" value="" />
            </div>
            <div class="defm-ftp-server">
                <label for="ftp_profile_server"><?php _e('Server', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_ftp_profile_server" name="ftp_profile_server" value="" />
            </div>
            <div class="defm-ftp-username">
                <label for="ftp_profile_username"><?php _e('Username', 'export-for-memberpress'); ?>:</label>
                <input type="text" id="defm_ftp_profile_username" name="ftp_profile_username" value="" />
            </div>
            <div class="defm-ftp-password">
                <label for="ftp_profile_password"><?php _e('Password', 'export-for-memberpress'); ?>:</label>
                <input type="password" id="defm_ftp_profile_password" name="ftp_profile_password" value="" />
            </div>
            <div id="defm_ftp_profile_submit_wrapper" class="defm-submit defm-ftp-submit">
                <button type="submit" id="defm_ftp_profile_submit" name="defm_ftp_profile_submit" class="button button-primary defm-submit defm-button dashicons-before dashicons-yes">
                    <?php _e('Save', 'export-for-memberpress'); ?>
                </button>
                <button id="defm_ftp_profile_cancel" class="button button-secondary defm-button defm-cancel dashicons-before dashicons-no-alt">
                    <?php _e('Cancel', 'export-for-memberpress'); ?>
                </button>
            </div>
      </form>
    </div>
    <button id="defm_add_ftp_profile" class="button button-primary defm-button defm-add-ftp-profile dashicons-before dashicons-plus-alt">
        <?php _e('Add a new FTP profile', 'export-for-memberpress'); ?>
    </button>
</footer>

<?php
