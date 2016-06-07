<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 *
 * @since      1.0.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin/partials
 */
?>
<?php
$tabs = Export_For_MemberPress_Admin::admin_tabs();

if ($tabs) :
?>

  <div class="wrap">
      <h2>Export for MemberPress</h2>
      <div id="defm_admin_tabs" class="defm-admin-tabs">
        <ul>
          <?php foreach ($tabs as $tab) : ?>
              <li><a href="#<?php echo $tab['id']; ?>">
                <span class="dashicons <?php echo $tab['dashicon']; ?>"></span>
                <span class="defm-tab-label"><?php echo $tab['title']; ?></span>
              </a></li>
          <?php endforeach; ?>
        </ul>
        <?php foreach($tabs as $i => $tab) : ?>
            <div id="<?php echo $tab['id']; ?>" class="defm-tab-panel <?php echo $i > 0 ? '' : 'defm-active-tab-panel'; ?>">
              <div class="defm-tab-panel-content">
                <?php include plugin_dir_path( __FILE__ ) . $tab['template']; ?>
              </div>
            </div>
        <?php endforeach; ?>
      </div>
  </div>

<?php
endif;