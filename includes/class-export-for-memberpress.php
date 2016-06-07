<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/includes
 * @author     Your Name <email@example.com>
 */
class Export_For_MemberPress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Export_For_MemberPress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'export-for-memberpress';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		// No public hooks for now
		// $this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Export_For_MemberPress_Loader. Orchestrates the hooks of the plugin.
	 * - Export_For_MemberPress_i18n. Defines internationalization functionality.
	 * - Export_For_MemberPress_Admin. Defines all hooks for the admin area.
	 * - Export_For_MemberPress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-export-for-memberpress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-export-for-memberpress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-export-for-memberpress-admin.php';


		$this->loader = new Export_For_MemberPress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Export_For_MemberPress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Export_For_MemberPress_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Export_For_MemberPress_Admin( $this->get_plugin_name(), $this->get_version() );

		// make sure MemberPress is activated
		$this->loader->add_action( 'init', $this, 'check_for_memberpress', 0 );


		$this->loader->add_action( 'init', $plugin_admin, 'register_post_types');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 10, 1 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_page', 10 );

		// Ajax actions
		$this->loader->add_action( 'wp_ajax_defm_generate_report_start', 'Export_For_MemberPress_Admin', 'ajax_generate_report_start' );
		$this->loader->add_action( 'wp_ajax_defm_generate_report_continue', 'Export_For_MemberPress_Admin', 'ajax_generate_report_continue' );
		$this->loader->add_action( 'wp_ajax_defm_generate_report_step', 'Export_For_MemberPress_Admin', 'ajax_generate_report_step' );
		$this->loader->add_action( 'wp_ajax_defm_generate_report_finish', 'Export_For_MemberPress_Admin', 'ajax_generate_report_finish' );
		$this->loader->add_action( 'wp_ajax_defm_add_scheduled_report', 'Export_For_MemberPress_Admin', 'ajax_add_scheduled_report' );
		$this->loader->add_action( 'wp_ajax_defm_update_scheduled_report', 'Export_For_MemberPress_Admin', 'ajax_update_scheduled_report' );
		$this->loader->add_action( 'wp_ajax_defm_toggle_scheduled_report_status', 'Export_For_MemberPress_Admin', 'ajax_toggle_scheduled_report_status' );
		$this->loader->add_action( 'wp_ajax_defm_delete_scheduled_report', 'Export_For_MemberPress_Admin', 'ajax_delete_scheduled_report' );
		$this->loader->add_action( 'wp_ajax_defm_add_ftp_profile', 'Export_For_MemberPress_Admin', 'ajax_add_ftp_profile' );
		$this->loader->add_action( 'wp_ajax_defm_add_email_profile', 'Export_For_MemberPress_Admin', 'ajax_add_email_profile' );
		$this->loader->add_action( 'wp_ajax_defm_delete_profile', 'Export_For_MemberPress_Admin', 'ajax_delete_profile' );
		$this->loader->add_action( 'wp_ajax_defm_delete_report', 'Export_For_MemberPress_Admin', 'ajax_delete_report' );


		// Scheduled report actions
		$this->loader->add_action( 'future_to_publish', 'Export_For_MemberPress_Admin', 'run_job', 0, 1 );

	}


	public function check_for_memberpress()
	{
		// deactivate self if there's no MemberPress
		if (!defined('MEPR_PLUGIN_NAME')) {
			add_action( 'admin_init', array($this, 'dependency_deactivation') );
			add_action( 'admin_notices', array($this, 'dependency_deactivation_notice') );
		}
	}

	public function dependency_deactivation()
	{
		deactivate_plugins( DEFM_PLUGIN_FILE );
	}

	public function dependency_deactivation_notice()
	{
		echo '<div class="updated"><p>';
		_e('<strong>Export for MemberPress</strong> can‘t work without MemberPress; the plug-in has been <strong>deactivated</strong>', 'export-for-memberpress');
		echo '.</p></div>';
		if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );
	}



	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Export_For_MemberPress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
