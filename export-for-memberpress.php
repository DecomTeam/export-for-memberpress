<?php

/**
 * @wordpress-plugin
 * Plugin Name: Export for MemberPress
 * Plugin URI:  http://decom.ba/plugins/export-for-memberpress
 * Description: Export custom reports of MemberPress user activity as CSV
 * Version:     1.3.1
 * Author:      Decom
 * Author URI:  http://decom.ba
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: export-for-memberpress
 *
 * Export for MemberPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Export for MemberPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Export for MemberPress. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('DEFM_PLUGIN_FILE', plugin_basename(__FILE__));
define('DEFM_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-export-for-memberpress-activator.php
 */
function activate_export_for_memberpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-export-for-memberpress-activator.php';
	Export_For_MemberPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-export-for-memberpress-deactivator.php
 */
function deactivate_export_for_memberpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-export-for-memberpress-deactivator.php';
	Export_For_MemberPress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_export_for_memberpress' );
register_deactivation_hook( __FILE__, 'deactivate_export_for_memberpress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-export-for-memberpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_export_for_memberpress() {

	$plugin = new Export_For_MemberPress();
	$plugin->run();
}
run_export_for_memberpress();