<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Export_For_MemberPress
 * @subpackage Export_For_MemberPress/admin
 *
 */
class Export_For_MemberPress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Admin screen tabs
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	static $tabs;

	/**
	 * MemberPress memberships
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	static $memberships = array();

	/**
	 * Schedule options
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	static $schedules = array();

	/**
	 * Weekday options
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	static $weekdays = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		self::$tabs = array(
			array(
				'id' => 'defm_manual_report',
				'title' => __('Export now', 'export-for-memberpress'),
				'template' => 'manual-export.php',
				'dashicon' => 'dashicons-download',
			),
			array(
				'id' => 'defm_schedule_report',
				'title' => __('Scheduled reports', 'export-for-memberpress'),
				'template' => 'scheduled-reports.php',
				'dashicon' => 'dashicons-controls-repeat',
			),
			array(
				'id' => 'defm_report_history',
				'title' => __('History', 'export-for-memberpress'),
				'template' => 'history.php',
				'dashicon' => 'dashicons-portfolio',
			),
			array(
				'id' => 'defm_settings_ftp',
				'title' => __('FTP Settings', 'export-for-memberpress'),
				'template' => 'settings-ftp.php',
				'dashicon' => 'dashicons-migrate',
			),
			array(
				'id' => 'defm_settings_email',
				'title' => __('Email Settings', 'export-for-memberpress'),
				'template' => 'settings-email.php',
				'dashicon' => 'dashicons-email-alt',
			),
		);

		$memberships = get_posts(array( 'numberposts' => -1, 'post_type' => 'memberpressproduct', 'post_status' => 'publish'));

		self::$memberships[0] = __('All memberships', 'export-for-memberpress');
		foreach ($memberships as $membership) {
			self::$memberships[$membership->ID] = $membership->post_title;
		}

		self::$schedules = array(
			'day' => __('Daily', 'export-for-memberpress'),
			'week' => __('Weekly', 'export-for-memberpress'),
			'month' => __('Monthly', 'export-for-memberpress'),
			'year' => __('Yearly', 'export-for-memberpress'),
		);

		self::$weekdays = array('Sundays', 'Mondays', 'Tuesdays', 'Wednesdays', 'Thursdays', 'Fridays', 'Saturdays');
	}

	/**
	 *
	 * Register custom post type to store history of generated reports
	 *
	 * @since 1.2.0
	 */

	public function register_post_types()
	{
		$post_types = array(
		    'decom_reports' => array(
		        'labels' => array(
		            'name'               => _x( 'Reports', 'post type general name', 'export-for-memberpress' ),
		            'singular_name'      => _x( 'Report', 'post type singular name', 'export-for-memberpress' ),
		            'menu_name'          => _x( 'Reports', 'admin menu', 'export-for-memberpress' ),
		            'name_admin_bar'     => _x( 'Report', 'add new on admin bar', 'export-for-memberpress' ),
		            'add_new'            => _x( 'Add New', 'book', 'export-for-memberpress' ),
		            'add_new_item'       => __( 'Add New Report', 'export-for-memberpress' ),
		            'new_item'           => __( 'New Report', 'export-for-memberpress' ),
		            'edit_item'          => __( 'Edit Report', 'export-for-memberpress' ),
		            'view_item'          => __( 'View Report', 'export-for-memberpress' ),
		            'all_items'          => __( 'All Reports', 'export-for-memberpress' ),
		            'search_items'       => __( 'Search Reports', 'export-for-memberpress' ),
		            'parent_item_colon'  => __( 'Parent Reports:', 'export-for-memberpress' ),
		            'not_found'          => __( 'No Reports found.', 'export-for-memberpress' ),
		            'not_found_in_trash' => __( 'No Reports found in Trash.', 'export-for-memberpress' )
		            ),
		        'description'        => __( 'Description.', 'export-for-memberpress' ),
		        'public'             => false,
		        'publicly_queryable' => false,
		        'show_ui'            => false,
		        'show_in_menu'       => false,
		        'query_var'          => true,
		        // 'rewrite'            => array( 'slug' => __('report', 'export-for-memberpress') ),
		        'capability_type'    => 'post',
		        'has_archive'        => false,
		        'hierarchical'       => false,
		        'menu_position'      => null,
		        'supports'           => array( 'title', 'author'),
		        'decom_taxonomies'         => array(
		            'decom_report_categories' => array(
		                'labels' => array(
		                    'name'              => _x( 'Report categories', 'taxonomy general name', 'export-for-memberpress' ),
		                    'singular_name'     => _x( 'Report category', 'taxonomy singular name', 'export-for-memberpress' ),
		                    'search_items'      => __( 'Search Report categories', 'export-for-memberpress' ),
		                    'all_items'         => __( 'All Report categories', 'export-for-memberpress' ),
		                    'parent_item'       => __( 'Parent Report category', 'export-for-memberpress' ),
		                    'parent_item_colon' => __( 'Parent Report category:', 'export-for-memberpress' ),
		                    'edit_item'         => __( 'Edit Report category', 'export-for-memberpress' ),
		                    'update_item'       => __( 'Update Report category', 'export-for-memberpress' ),
		                    'add_new_item'      => __( 'Add New Report category', 'export-for-memberpress' ),
		                    'new_item_name'     => __( 'New Report category Name', 'export-for-memberpress' ),
		                    'menu_name'         => __( 'Report category', 'export-for-memberpress' ),
		                ),
		                'hierarchical'      => false,
		                'public'            => false,
		                'show_ui'           => false,
		                'show_admin_column' => false,
		                'query_var'         => true,
		                // 'rewrite'           => array( 'slug' => __('reports', 'export-for-memberpress') ),
		            )
		        )
		    ),
		    'decom_jobs' => array(
		        'labels' => array(
		            'name'               => _x( 'Jobs', 'post type general name', 'export-for-memberpress' ),
		            'singular_name'      => _x( 'Job', 'post type singular name', 'export-for-memberpress' ),
		            'menu_name'          => _x( 'Jobs', 'admin menu', 'export-for-memberpress' ),
		            'name_admin_bar'     => _x( 'Job', 'add new on admin bar', 'export-for-memberpress' ),
		            'add_new'            => _x( 'Add New', 'book', 'export-for-memberpress' ),
		            'add_new_item'       => __( 'Add New Job', 'export-for-memberpress' ),
		            'new_item'           => __( 'New Job', 'export-for-memberpress' ),
		            'edit_item'          => __( 'Edit Job', 'export-for-memberpress' ),
		            'view_item'          => __( 'View Job', 'export-for-memberpress' ),
		            'all_items'          => __( 'All Jobs', 'export-for-memberpress' ),
		            'search_items'       => __( 'Search Jobs', 'export-for-memberpress' ),
		            'parent_item_colon'  => __( 'Parent Jobs:', 'export-for-memberpress' ),
		            'not_found'          => __( 'No Jobs found.', 'export-for-memberpress' ),
		            'not_found_in_trash' => __( 'No Jobs found in Trash.', 'export-for-memberpress' )
		            ),
		        'description'        => __( 'Description.', 'export-for-memberpress' ),
		        'public'             => false,
		        'publicly_queryable' => false,
		        'show_ui'            => false,
		        'show_in_menu'       => false,
		        'query_var'          => true,
		        // 'rewrite'            => array( 'slug' => __('job', 'export-for-memberpress') ),
		        'capability_type'    => 'post',
		        'has_archive'        => false,
		        'hierarchical'       => false,
		        'menu_position'      => null,
		        'supports'           => array( 'title', 'author'),
		        'decom_taxonomies'         => array(
		            'decom_job_categories' => array(
		                'labels' => array(
		                    'name'              => _x( 'Job categories', 'taxonomy general name', 'export-for-memberpress' ),
		                    'singular_name'     => _x( 'Job category', 'taxonomy singular name', 'export-for-memberpress' ),
		                    'search_items'      => __( 'Search Job categories', 'export-for-memberpress' ),
		                    'all_items'         => __( 'All Job categories', 'export-for-memberpress' ),
		                    'parent_item'       => __( 'Parent Job category', 'export-for-memberpress' ),
		                    'parent_item_colon' => __( 'Parent Job category:', 'export-for-memberpress' ),
		                    'edit_item'         => __( 'Edit Job category', 'export-for-memberpress' ),
		                    'update_item'       => __( 'Update Job category', 'export-for-memberpress' ),
		                    'add_new_item'      => __( 'Add New Job category', 'export-for-memberpress' ),
		                    'new_item_name'     => __( 'New Job category Name', 'export-for-memberpress' ),
		                    'menu_name'         => __( 'Job category', 'export-for-memberpress' ),
		                ),
		                'hierarchical'      => false,
		                'public'            => false,
		                'show_ui'           => false,
		                'show_admin_column' => false,
		                'query_var'         => true,
		                // 'rewrite'           => array( 'slug' => __('jobs', 'export-for-memberpress') ),
		            )
		        )
		    ),
		    'decom_profiles' => array(
		        'labels' => array(
		            'name'               => _x( 'Profiles', 'post type general name', 'export-for-memberpress' ),
		            'singular_name'      => _x( 'Profile', 'post type singular name', 'export-for-memberpress' ),
		            'menu_name'          => _x( 'Profiles', 'admin menu', 'export-for-memberpress' ),
		            'name_admin_bar'     => _x( 'Profile', 'add new on admin bar', 'export-for-memberpress' ),
		            'add_new'            => _x( 'Add New', 'book', 'export-for-memberpress' ),
		            'add_new_item'       => __( 'Add New Profile', 'export-for-memberpress' ),
		            'new_item'           => __( 'New Profile', 'export-for-memberpress' ),
		            'edit_item'          => __( 'Edit Profile', 'export-for-memberpress' ),
		            'view_item'          => __( 'View Profile', 'export-for-memberpress' ),
		            'all_items'          => __( 'All Profiles', 'export-for-memberpress' ),
		            'search_items'       => __( 'Search Profiles', 'export-for-memberpress' ),
		            'parent_item_colon'  => __( 'Parent Profiles:', 'export-for-memberpress' ),
		            'not_found'          => __( 'No Profiles found.', 'export-for-memberpress' ),
		            'not_found_in_trash' => __( 'No Profiles found in Trash.', 'export-for-memberpress' )
		            ),
		        'description'        => __( 'Description.', 'export-for-memberpress' ),
		        'public'             => false,
		        'publicly_queryable' => false,
		        'show_ui'            => false,
		        'show_in_menu'       => false,
		        'query_var'          => true,
		        // 'rewrite'            => array( 'slug' => __('profile', 'export-for-memberpress') ),
		        'capability_type'    => 'post',
		        'has_archive'        => false,
		        'hierarchical'       => false,
		        'menu_position'      => null,
		        'supports'           => array( 'title', 'author'),
		        'decom_taxonomies'         => array(
		            'decom_profile_categories' => array(
		                'labels' => array(
		                    'name'              => _x( 'Profile categories', 'taxonomy general name', 'export-for-memberpress' ),
		                    'singular_name'     => _x( 'Profile category', 'taxonomy singular name', 'export-for-memberpress' ),
		                    'search_items'      => __( 'Search Profile categories', 'export-for-memberpress' ),
		                    'all_items'         => __( 'All Profile categories', 'export-for-memberpress' ),
		                    'parent_item'       => __( 'Parent Profile category', 'export-for-memberpress' ),
		                    'parent_item_colon' => __( 'Parent Profile category:', 'export-for-memberpress' ),
		                    'edit_item'         => __( 'Edit Profile category', 'export-for-memberpress' ),
		                    'update_item'       => __( 'Update Profile category', 'export-for-memberpress' ),
		                    'add_new_item'      => __( 'Add New Profile category', 'export-for-memberpress' ),
		                    'new_item_name'     => __( 'New Profile category Name', 'export-for-memberpress' ),
		                    'menu_name'         => __( 'Profile category', 'export-for-memberpress' ),
		                ),
		                'hierarchical'      => false,
		                'public'            => false,
		                'show_ui'           => false,
		                'show_admin_column' => false,
		                'query_var'         => true,
		                // 'rewrite'           => array( 'slug' => __('profiles', 'export-for-memberpress') ),
		            )
		        )
		    ),
		);

		foreach ($post_types as $post_type_name => $post_type_args) {
			if (isset($post_type_args['decom_taxonomies'])) {
			    foreach ($post_type_args['decom_taxonomies'] as $taxonomy_name => $taxonomy_args) {
			        register_taxonomy($taxonomy_name, $post_type_name, $taxonomy_args);
			    }
			}
		    register_post_type( $post_type_name, $post_type_args );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		if ( $hook != 'tools_page_export-for-memberpress' )
			return;

		// Styles
		wp_enqueue_style($this->plugin_name . '_jquery_ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
		wp_enqueue_style($this->plugin_name . '_jquery_ui_tabs', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-tabs.css', array(), $this->version, 'all' );
		wp_enqueue_style($this->plugin_name . '_datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/export-for-memberpress-admin.css', array(), $this->version, 'all' );


		// Scripts
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/export-for-memberpress-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'decomExport',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'export_for_memberpress_secure' )
					)
				);
		wp_enqueue_script('jquery-ui-datepicker', '', array('jquery'));
		wp_enqueue_script('jquery-ui-tabs', '', array('jquery'));
		wp_enqueue_script('jquery-ui-progressbar', '', array('jquery'));
	}

	/**
	 *
	 * Add Sub-Menu page to the Tools Menu
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_admin_page()
	{
		add_management_page(
			__('Export for MemberPress', 'export-for-memberpress'),
			__('Export for MemberPress', 'export-for-memberpress'),
			'manage_options',
			'export-for-memberpress',
			array($this, 'display_admin_page')
		);

	}

	/**
	 *
	 * Display admin menu page
	 *
	 * @since 1.3.0
	 *
	 */
	public function display_admin_page()
	{
		require DEFM_PLUGIN_DIR . 'admin/partials/admin-main.php';
	}
	/**
	 *
	 * Get tab definitions
	 *
	 * @since 1.2.0
	 *
	 */
	public static function admin_tabs()
	{
		return self::$tabs;
	}

	/**
	 *
	 * Display membership options dropdown
	 *
	 * @since 1.0.0
	 *
	 */

	public static function membership_options($id = 'defm_product')
	{

	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-membership-select" name="product">
	      <?php
	      	$i = 0;
	        foreach(self::$memberships as $value => $label)
	        {
	          ?>
	          <option value="<?php echo $value; ?>" <?php selected(0, $i++); ?>><?php echo esc_html($label); ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}


	/**
	 *
	 * Display schedule options dropdown
	 *
	 * @since 1.2.0
	 *
	 */

	public static function schedule_options($id = 'defm_schedule')
	{
	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-schedule-select" name="schedule">
	      <?php
	        foreach(self::$schedules as $value => $label)
	        {
	          ?>
	          <option value="<?php echo $value; ?>" <?php selected('day', $value); ?>><?php echo esc_html($label); ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}

	/**
	 *
	 * Display weekday options dropdown
	 *
	 * @since 1.2.0
	 *
	 */

	public static function weekday_options($id = 'defm_schedule_weekday')
	{
	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-weekday-select" name="weekday">
	      <?php
		    $i = 0;
	        foreach(self::$weekdays as $value => $label)
	        {
	          ?>
	          <option value="<?php echo $value; ?>" <?php selected(0, $i++); ?>><?php echo esc_html($label); ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}

	/**
	 *
	 * Display ftp profile options dropdown
	 *
	 * @since 1.2.0
	 *
	 */

	public static function ftp_options($id = 'defm_schedule_ftp_profile')
	{
	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-ftp-profile-select" name="ftp_profile">
		      <option value="0" selected="selected"><?php _e('Disabled', 'export-for-memberpress'); ?></option>
	      <?php
		    $i = 0;
	        foreach(self::get_profiles('ftp') as $profile)
	        {
	          ?>
	          <option value="<?php echo $profile->ID; ?>"><?php echo esc_html($profile->post_title); ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}

	/**
	 *
	 * Display email profile options dropdown
	 *
	 * @since 1.2.0
	 *
	 */

	public static function email_options($id = 'defm_schedule_email_profile')
	{
	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-email-profile-select" name="email_profile">
		      <option value="0" selected="selected"><?php _e('Disabled', 'export-for-memberpress'); ?></option>
	      <?php
		    $i = 0;
	        foreach(self::get_profiles('email') as $profile)
	        {
	          ?>
	          <option value="<?php echo $profile->ID; ?>"><?php echo esc_html(Export_For_MemberPress_Admin::filter_email_text($profile->post_title)) ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}


	/**
	 *
	 * Display generated by scheduled options dropdown
	 *
	 * @since 1.2.0
	 *
	 */

	public static function generated_by_options($id = 'defm_generated_by')
	{
	    ?>
	      <select id="<?php echo esc_attr($id); ?>" class="defm-generated-by-select" name="<?php echo esc_attr($id); ?>">
		      <option value=".defm-auto-report" selected="selected"><?php _e('All', 'export-for-memberpress'); ?></option>
	      <?php
		    $i = 0;
	        foreach(self::get_jobs() as $job)
	        {
	          ?>
	          <option value=".defm-generated-by-<?php echo $job->ID; ?>"><?php echo esc_html($job->post_title); ?></option>
	          <?php
	        }
	      ?>
	      </select>
	    <?php
	}

	/**
	 *
	 * Get memberpress memberships
	 *
	 * @since 1.2.0
	 */
	public static function get_memberships()
	{
		return self::$memberships;
	}



	/**
	 *
	 * Generate report: start
	 *
	 * @since 1.0.0.
	 *
	 */

	public static function ajax_generate_report_start()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
		$week = isset($_POST['form_data']['week']) ? (int)$_POST['form_data']['week'] : false;
		$start_date = isset($_POST['form_data']['start_date']) ? $_POST['form_data']['start_date'] : false;
		$end_date = isset($_POST['form_data']['end_date']) ? $_POST['form_data']['end_date'] : false;

		$user_count = (int)self::get_user_count($product, $week, $start_date, $end_date);
		$report_data['userCount'] = $user_count;

		$report_data['security'] = wp_create_nonce( 'export_for_memberpress_start' );
		$report_data['status'] = 'success';

		wp_send_json($report_data);
	}

	/**
	 *
	 * Generate report: continue
	 *
	 */

	public static function ajax_generate_report_continue()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_start', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
		$week = isset($_POST['form_data']['week']) ? (int)$_POST['form_data']['week'] : false;
		$start_date = isset($_POST['form_data']['start_date']) ? $_POST['form_data']['start_date'] : false;
		$end_date = isset($_POST['form_data']['end_date']) ? $_POST['form_data']['end_date'] : false;

		$report_data['entriesCount'] = (int)self::get_login_entries_count($product, $week, $start_date, $end_date);

		$report_data['security'] = wp_create_nonce( 'export_for_memberpress_continue' );
		$report_data['status'] = 'success';

		wp_send_json($report_data);
	}

	/**
	 *
	 * Generate report: step
	 *
	 */

	public static function ajax_generate_report_step()
	{
		if ( ! (check_ajax_referer( 'export_for_memberpress_continue', 'security', false )
			|| check_ajax_referer( 'export_for_memberpress_step', 'security', false )) ) {
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$user_count = (int)$_POST['user_count'];
		$start = isset($_POST['start']) ? (int)$_POST['start'] : false;

		if ( $user_count && $start !== false && $start < $user_count ) {

			$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
			$week = isset($_POST['form_data']['week']) ? (int)$_POST['form_data']['week'] : false;
			$start_date = isset($_POST['form_data']['start_date']) ? $_POST['form_data']['start_date'] : false;
			$end_date = isset($_POST['form_data']['end_date']) ? $_POST['form_data']['end_date'] : false;
			$filename = isset($_POST['form_data']['filename']) ? sanitize_text_field(sanitize_title_with_dashes($_POST['form_data']['filename'])) : false;
			$hash = isset($_POST['file']) ? $_POST['file'] : md5(time());
			$limit = $user_count - $start < 750 ? $user_count - $start : 750;
			$offset = $start;

			$file = self::generate_report($product, $week, $start_date, $end_date, $filename, $limit, $offset, $hash);

			if ( $file ) {
				$report_data['status'] = 'progress';
				$report_data['file'] = $hash;
				$report_data['progress'] = $limit + $offset;
			} else {
				$report_data['status'] = 'error';
				$report_data['error'] = __('Database error', 'export-for-memberpress');
			}

		} else {
			$report_data['status'] = 'success';
			$hash = isset($_POST['file']) ? $_POST['file'] : md5(time());
			$report_data['file'] = $hash;
		}

		$report_data['security'] = wp_create_nonce( 'export_for_memberpress_step' );

		wp_send_json($report_data);
	}

	/**
	 *
	 * Generate report: continue
	 *
	 */

	public static function ajax_generate_report_finish()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_step', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
		$week = isset($_POST['form_data']['week']) ? (int)$_POST['form_data']['week'] : false;
		$start_date = isset($_POST['form_data']['start_date']) ? $_POST['form_data']['start_date'] : false;
		$end_date = isset($_POST['form_data']['end_date']) ? $_POST['form_data']['end_date'] : false;
		$filename = isset($_POST['form_data']['filename']) ? sanitize_text_field(sanitize_title_with_dashes($_POST['form_data']['filename'])) : false;
		$hash = isset($_POST['file']) ? $_POST['file'] : false;

		list($report, $report_url, $error) = self::save_report($product, $week, $start_date, $end_date, $filename, $hash);

		if ( $report && $report_url ) {
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'partials/single-history-report.php';
			$report_data['reportElement'] = ob_get_clean();

			$report_data['reportID'] = $report->ID;
			$report_data['status'] = 'success';
			$report_data['reportUrl'] = $report_url;
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = $error;
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Add scheduled report
	 *
	 * @since 1.2.0
	 */

	public static function ajax_add_scheduled_report()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
		$schedule = isset($_POST['form_data']['schedule']) ? stripslashes($_POST['form_data']['schedule']) : false;
		$weekday = isset($_POST['form_data']['weekday']) ? intval($_POST['form_data']['weekday']) : false;
		$filename = isset($_POST['form_data']['filename']) ? sanitize_text_field(sanitize_title_with_dashes($_POST['form_data']['filename'])) : false;
		$report_name = isset($_POST['form_data']['report_name']) ? sanitize_text_field($_POST['form_data']['report_name']) : false;
		$ftp_profile = isset($_POST['form_data']['ftp_profile']) ? intval($_POST['form_data']['ftp_profile']) : false;
		$email_profile = isset($_POST['form_data']['email_profile']) ? intval($_POST['form_data']['email_profile']) : false;

		$job = self::add_job($product, $schedule, $weekday, $filename, $report_name, $ftp_profile, $email_profile);

		if ($job) {
			ob_start();

			include plugin_dir_path( __FILE__ ) . 'partials/single-job.php';

			$report_data['element'] = ob_get_clean();
			$report_data['jobTitle'] = esc_html($job->post_title);
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem creating new scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Update scheduled report
	 *
	 * @since 1.2.0
	 */

	public static function ajax_update_scheduled_report()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$job_id = isset($_POST['form_data']['job_id']) ? (int)$_POST['form_data']['job_id'] : false;
		$product = isset($_POST['form_data']['product']) ? (int)$_POST['form_data']['product'] : false;
		$schedule = isset($_POST['form_data']['schedule']) ? stripslashes($_POST['form_data']['schedule']) : false;
		$weekday = isset($_POST['form_data']['weekday']) ? intval($_POST['form_data']['weekday']) : false;
		$filename = isset($_POST['form_data']['filename']) ? sanitize_text_field(sanitize_title_with_dashes($_POST['form_data']['filename'])) : false;
		$report_name = isset($_POST['form_data']['report_name']) ? sanitize_text_field($_POST['form_data']['report_name']) : false;
		$ftp_profile = isset($_POST['form_data']['ftp_profile']) ? intval($_POST['form_data']['ftp_profile']) : false;
		$email_profile = isset($_POST['form_data']['email_profile']) ? intval($_POST['form_data']['email_profile']) : false;

		$job = self::add_job($product, $schedule, $weekday, $filename, $report_name, $ftp_profile, $email_profile, $job_id);

		if ($job) {
			ob_start();

			include plugin_dir_path( __FILE__ ) . 'partials/single-job.php';

			$report_data['element'] = ob_get_clean();
			$report_data['jobTitle'] = esc_html($job->post_title);
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem creating new scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Toggle scheduled report status
	 *
	 * @since 1.2.0
	 */

	public static function ajax_toggle_scheduled_report_status()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$job_id = isset($_POST['form_data']['job_id']) ? (int)$_POST['form_data']['job_id'] : false;

		$job = get_post($job_id);

		if ($job && $toggled_status = self::toggle_job_status($job->ID)) {
			$report_data['status'] = 'success';
			$report_data['jobStatus'] = $toggled_status;
			$report_data['jobSchedule'] = self::job_schedule(get_post($job_id));
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem changing status of scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Delete scheduled report
	 *
	 * @since 1.2.0
	 */

	public static function ajax_delete_scheduled_report()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$job_id = isset($_POST['form_data']['job_id']) ? (int)$_POST['form_data']['job_id'] : false;

		$deleted = self::delete_post($job_id);

		if ($deleted) {
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem deleting scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Add ftp profile
	 *
	 * @since 1.2.0
	 */

	public static function ajax_add_ftp_profile()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$profile_id = isset($_POST['form_data']['profile_id']) ? (int)$_POST['form_data']['profile_id'] : false;
		$profile_name = isset($_POST['form_data']['profile_name']) ? sanitize_text_field($_POST['form_data']['profile_name']) : false;
		$server = isset($_POST['form_data']['server']) ? sanitize_text_field($_POST['form_data']['server']) : false;
		$username = isset($_POST['form_data']['username']) ? sanitize_text_field($_POST['form_data']['username']) : false;
		$password = isset($_POST['form_data']['password']) ? sanitize_text_field($_POST['form_data']['password']) : false;

		list($profile, $error) = self::add_ftp_profile($server, $username, $password, $profile_name, $profile_id);

		if ($profile) {
			ob_start();

			include plugin_dir_path( __FILE__ ) . 'partials/single-ftp-profile.php';

			$report_data['element'] = ob_get_clean();
			$report_data['profileTitle'] = esc_html($profile->post_title);
			$report_data['profileID'] = $profile->ID;
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = $error;
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Add email profile
	 *
	 * @since 1.2.0
	 */

	public static function ajax_add_email_profile()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();
		$profile = false;
		$error = false;

		$profile_id = isset($_POST['form_data']['profile_id']) ? (int)$_POST['form_data']['profile_id'] : false;
		$profile_name = isset($_POST['form_data']['profile_name']) ? sanitize_text_field($_POST['form_data']['profile_name']) : false;
		$from = isset($_POST['form_data']['from']) ? sanitize_text_field($_POST['form_data']['from']) : false;
		$to = isset($_POST['form_data']['to']) ? sanitize_text_field($_POST['form_data']['to']) : false;
		$subject = isset($_POST['form_data']['subject']) ? sanitize_text_field($_POST['form_data']['subject']) : false;
		$cc = isset($_POST['form_data']['cc']) ? sanitize_text_field($_POST['form_data']['cc']) : false;
		$bcc = isset($_POST['form_data']['bcc']) ? sanitize_text_field($_POST['form_data']['bcc']) : false;
		$text = isset($_POST['form_data']['text']) ? wp_kses_post($_POST['form_data']['text']) : false;
		$attachment = isset($_POST['form_data']['attachment']) ? (bool)$_POST['form_data']['attachment'] : false;


		$emails = array(
			'From' => array(
				'value' => $from,
				'allow_empty' => false
			),
			'To' => array(
				'value' => $to,
				'allow_empty' => false
			),
			'Cc' => array(
				'value' => $cc,
				'allow_empty' => true
			),
			'Bcc' => array(
				'value' => $bcc,
				'allow_empty' => true
			),
		);

		foreach ($emails as $email_field_name => $email_field) {
			if (!self::validate_email_field($email_field['value'], $email_field['allow_empty'])) {
				$error = sprintf(__('Please enter a valid email address (or a comma-sepparated list of emails) as %s field.', 'export-for-memberpress'), $email_field_name);
				if ($email_field['allow_empty']) {
					$error .= ' ' . __('This field can be empty.', 'export-for-memberpress');
				} else {
					$error .= ' ' . __('This field cannot be empty.', 'export-for-memberpress');
				}
			}
		}


		if (!$error) {
			list($profile, $error) = self::add_email_profile($from, $to, $subject, $cc, $bcc, $text, $attachment, $profile_name, $profile_id);
		}

		if ($profile) {
			ob_start();

			include plugin_dir_path( __FILE__ ) . 'partials/single-email-profile.php';

			$report_data['element'] = ob_get_clean();
			$report_data['profileTitle'] = esc_html($profile->post_title);
			$report_data['profileID'] = $profile->ID;
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = $error;
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Validate email field
	 *
	 * @since 1.2.0
	 *
	 */
	public static function validate_email_field($value, $allow_empty = false)
	{
		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		$valid = true;

		foreach ($value as $email) {
			$email_addr = trim($email);
			if (!$email_addr) {
				$valid = $allow_empty;
			} else {
				$valid = $email_addr === '{admin_email}' || is_email($email_addr);
			}

			if (!$valid) break;
		}

		return $valid;
	}


	/**
	 *
	 * Delete scheduled report
	 *
	 * @since 1.2.0
	 */

	public static function ajax_delete_profile()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$profile_id = isset($_POST['form_data']['profile_id']) ? (int)$_POST['form_data']['profile_id'] : false;

		$deleted = self::delete_post($profile_id);

		if ($deleted) {
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem deleting scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Delete history report
	 *
	 * @since 1.2.0
	 */

	public static function ajax_delete_report()
	{
		if ( ! check_ajax_referer( 'export_for_memberpress_secure', 'security', false )){
			exit( __('Security check', 'export_for_memberpress') );
		}

		if ( ! current_user_can('manage_options') ){
			exit( __('Security check', 'export_for_memberpress') );
		}

		$report_data = array();

		$report_id = isset($_POST['form_data']['report_id']) ? (int)$_POST['form_data']['report_id'] : false;

		$report_details = get_post_meta($report_id, '_decom_report_details', true);

		$report_file = $report_details['report_file'];
		$report_dir = $report_details['report_dir'];

		$file_deleted = @unlink($report_file) && @rmdir($report_dir);

		$deleted = self::delete_post($report_id);

		if ($deleted) {
			$report_data['status'] = 'success';
		} else {
			$report_data['status'] = 'error';
			$report_data['error'] = __('There was a problem deleting scheduled report.', 'export-for-memberpress');
		}


		wp_send_json($report_data);
	}

	/**
	 *
	 * Generate csv report
	 *
	 * @since 1.0.0
	 *
	 */

	public static function generate_report($product = false, $week = false, $start_date = false, $end_date = false, $filename = false,
		$limit = false, $offset = false, $hash = false)
	{
		if ($product === false)
			$product = isset($_POST['product']) ? (int)$_POST['product'] : false;
		if ($week === false)
			$week = isset($_POST['week']) ? (int)$_POST['week'] : false;
		if ($start_date === false)
			$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : false;
		if ($end_date === false)
			$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : false;
		if ($filename === false)
			$filename = isset($_POST['filename']) ? sanitize_text_field(sanitize_title_with_dashes($_POST['filename'])) : false;

		if (($product !== false) && ($week !== false)) {

			// limit week to -2 to 0 weeks or 1 for date range
			$week = ($week <= 1 && $week > -3 ) ? $week : 0;

			// Generate CSV, based on MeprExportCrtl.php

			  global $wpdb;
			  $mepr_db = new MeprDb();


			$mysql_lifetime = $wpdb->prepare('%s',MeprUtils::mysql_lifetime());
			$mysql_membership = $wpdb->prepare('%d', $product);

			// custom date range
			if ($week == 1 && $start_date && $end_date) {
				$week_start = date('Y-m-d 00:00:00', strtotime($start_date));
				$week_end = date('Y-m-d 23:59:59', strtotime($end_date));
				$year_start = date('Y-01-01 00:00:00', strtotime($end_date));
			} else {
			    $week = $week < 0 ? strtotime($week . ' weeks') : time();
			    $day = date('w', $week);
				$week_start = date('Y-m-d 00:00:00', strtotime('-'.$day.' days', $week ));
				$week_end = date('Y-m-d 00:00:00', strtotime('+'.(7-$day).' days', $week ));
			    $year_start = date('Y-01-01 00:00:00', strtotime(date('Y')));
			}

			$mysql_startweek = $wpdb->prepare('%s', $week_start);
			$mysql_endweek = $wpdb->prepare('%s', $week_end);
			$mysql_startyear = $wpdb->prepare('%s', $year_start);
			$mysql_now = $wpdb->prepare('%s',MeprUtils::mysql_now());

			if ($limit !== false && $offset !== false) {
				$mysql_user_limit = $wpdb->prepare('%d', $limit);
				$mysql_user_offset = $wpdb->prepare('%d', $offset);
				$q = "SELECT u.user_login AS 'Agent ID', u.user_email AS 'Email', f.meta_value AS 'First Name', l.meta_value AS 'Last Name', DATE_FORMAT(u.user_registered, '%m/%d/%Y') AS 'Start Date', u.post_title AS 'Membership',
				    DATE_FORMAT(e1.max_created, '%m/%d/%Y') AS 'Login Last',
				    e2.last_week as 'Logins',
				    e3.YTD as 'Logins YTD'
				    FROM
				       	(SELECT u1.ID as ID, u1.user_login as user_login, u1.user_email as user_email, u1.user_registered as user_registered, p1.post_title as post_title
					      FROM {$wpdb->users} AS u1
					        INNER JOIN {$mepr_db->transactions} AS t1
					          ON u1.ID = t1.user_id AND (t1.status = 'complete' OR t1.status = 'confirmed') AND (t1.expires_at IS NULL OR t1.expires_at = 0)
					        INNER JOIN {$wpdb->posts} AS p1
					          ON t1.product_id = p1.ID ";
						if ($mysql_membership) {
							$q .= " AND p1.ID = {$mysql_membership} ";
						}
						$q .= " ORDER BY u1.user_login ASC
						 		LIMIT {$mysql_user_limit}
						 		OFFSET {$mysql_user_offset}) AS u
					LEFT JOIN {$wpdb->usermeta} AS f
			          ON u.ID = f.user_id AND f.meta_key = 'first_name'
			        LEFT JOIN {$wpdb->usermeta} AS l
			          ON u.ID = l.user_id AND l.meta_key = 'last_name' ";
			} else {
				$q = "SELECT u.user_login AS 'Agent ID', u.user_email AS 'Email', f.meta_value AS 'First Name', l.meta_value AS 'Last Name', DATE_FORMAT(u.user_registered, '%m/%d/%Y') AS 'Start Date', p.post_title AS 'Membership',
				    e1.max_created AS 'Login Last',
				    e2.last_week as 'Logins',
				    e3.YTD as 'Logins YTD'
				    FROM  {$wpdb->users} AS u
				    LEFT JOIN {$wpdb->usermeta} AS f
			          ON u.ID = f.user_id AND f.meta_key = 'first_name'
			        LEFT JOIN {$wpdb->usermeta} AS l
			          ON u.ID = l.user_id AND l.meta_key = 'last_name'
			        INNER JOIN {$mepr_db->transactions} AS t
			          ON u.ID = t.user_id AND (t.status = 'complete' OR t.status = 'confirmed') AND (t.expires_at IS NULL OR t.expires_at = 0)
			        INNER JOIN {$wpdb->posts} AS p
			          ON t.product_id = p.ID";

				if ($mysql_membership) {
					$q .= " AND p.ID = {$mysql_membership} ";
				}
			}
			$q .= " LEFT JOIN (SELECT evt_id, MAX(created_at) AS max_created FROM {$mepr_db->events} WHERE event = 'login' GROUP BY evt_id) as e1
			            ON u.ID = e1.evt_id
			        LEFT JOIN (SELECT evt_id, COUNT(*) AS last_week FROM {$mepr_db->events} WHERE event = 'login' AND (created_at BETWEEN {$mysql_startweek} and {$mysql_endweek}) GROUP BY evt_id) as e2
			            ON u.ID = e2.evt_id
			        LEFT JOIN (SELECT evt_id, COUNT(*) AS YTD FROM {$mepr_db->events} WHERE event = 'login' AND (created_at BETWEEN {$mysql_startyear} and {$mysql_now}) GROUP BY evt_id) as e3
			            ON u.ID = e3.evt_id";

			$upload_dir = wp_upload_dir();
			$plugin_dir = $upload_dir['basedir'].'/export-for-memberpress';
			if ($hash) {
				$plugin_dir .= '/' . $hash;
			}
			if ( !file_exists($plugin_dir) )
				wp_mkdir_p($plugin_dir);

			$date_start = date('Y-m-d', strtotime($week_start));
			$date_end = date('Y-m-d', strtotime($week_end));

			$file_name_base = $filename ? $filename . '_' . sanitize_title_with_dashes(self::$memberships[$mysql_membership]) . '_' : '';
			$newfile_name = $file_name_base
							. $date_start
							. '_' . $date_end . '.csv';
			$newfile = $plugin_dir . '/' . $newfile_name;

			if ( !file_exists($newfile) ) {
				$output = fopen($newfile, 'w');

				// output the column headings
				fputcsv($output, array( 'Agent ID',
				                        'Email',
				                        'First Name',
				                        'Last Name',
				                        'Start Date',
				                        'Membership',
				                        'Login Last',
				                        'Logins',
				                        'Dates covered',
				                        'Logins YTD' ) );
			} else {
				$output = fopen($newfile, 'a');
			}

			// fetch the data
			$wpdb->query("SET SQL_BIG_SELECTS=1");

			$rows = $wpdb->get_results($q, ARRAY_A);

			$date_start = date('m d Y', strtotime($week_start));
			$date_end = date('m d Y', strtotime($week_end));
			// loop over the rows, outputting them
			foreach($rows as $row) {
				foreach ($row as $label => $field)
					if (is_null($field))
						$row[$label] = '0';
				array_splice($row, 8, 0, array($date_start . ' - ' . $date_end));
				fputcsv($output, $row);
			}


			// close the file and exit
			fclose($output);

			return esc_url($upload_dir['baseurl'] . '/export-for-memberpress/' . $hash . '/' . $newfile_name);

		}


		return false;
	}


	/**
	 *
	 * Save report
	 *
	 * @since 1.2.0
	 */

	public static function save_report($product, $week, $start_date, $end_date, $filename, $hash, $job = false)
	{
		$error = false;
		$report_url = false;
		$report = false;
		$upload_dir = wp_upload_dir();
		$plugin_dir = $upload_dir['basedir'].'/export-for-memberpress' . '/' . $hash;

		// custom date range
		if ($week == 1 && $start_date && $end_date) {
			$week_start = date('Y-m-d 00:00:00', strtotime($start_date));
			$week_end = date('Y-m-d 23:59:59', strtotime($end_date));
			$year_start = date('Y-01-01 00:00:00', strtotime($end_date));
		} else {
		    $week = $week < 0 ? strtotime($week . ' weeks') : time();
		    $day = date('w', $week);
			$week_start = date('Y-m-d 00:00:00', strtotime('-'.$day.' days', $week ));
			$week_end = date('Y-m-d 00:00:00', strtotime('+'.(7-$day).' days', $week ));
		    $year_start = date('Y-01-01 00:00:00', strtotime(date('Y')));
		}

		$date_start = date('Y-m-d', strtotime($week_start));
		$date_end = date('Y-m-d', strtotime($week_end));


			$file_name_base = $filename ? $filename . '_' . sanitize_title_with_dashes(self::$memberships[$product]) . '_' : '';
			$newfile_name = $file_name_base
							. $date_start
							. '_' . $date_end . '.csv';
		$newfile = $plugin_dir . '/' . $newfile_name;


		if ( !file_exists($newfile) ) {
			$error = __('Error reading file.', 'export-for-memberpress');
		} else {
			$report_url = esc_url($upload_dir['baseurl'] . '/export-for-memberpress/' . $hash . '/' . $newfile_name);

			if ($job) {
				$job_details = get_post_meta($job->ID, '_decom_job_details', true);
				$job_reports = get_post_meta($job->ID, '_decom_job_reports', true);


				$report_title = $job->post_title;
				$report_content = sprintf(__('This report was generated by %s scheduled report.', 'export-for-memberpress'), '<strong>' . $job->post_title . '</strong>');
				$report_category = 'scheduled';
			} else {
				global $current_user;
				$report_title = __('Manual report', 'export-for-memberpress');

				$report_content = sprintf(__('This report was generated manually, by user %s.', 'export-for-memberpress'), '<strong>' . $current_user->data->user_login . '</strong>');
				$report_category = 'manual';
			}

			$post = array(
			  'post_content'   => $report_content, // The full text of the post.
			  'post_title'     => $report_title, // The title of your post.
			  'post_status'    => 'publish', // Default 'draft'.
			  'post_type'      => 'decom_reports', // Default 'post'.
			  'ping_status'    => 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
			  // 'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
			  // 'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
			  // 'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
			  // 'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
			  // 'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
			  // 'guid'           => // Skip this and let Wordpress handle it, usually.
			  // 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
			  // 'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
			  // 'post_date'      => [ Y-m-d H:i:s ] // The time post was made.
			  // 'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
			  'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
			  // 'post_category'  => [ array(<category id>, ...) ] // Default empty.
			  // 'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
			  // 'tax_input'      => [ array( <taxonomy> => <array | string>, <taxonomy_other> => <array | string> ) ] // For custom taxonomies. Default empty.
			  // 'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
			);

			$report_id = wp_insert_post($post);

			if ($report_id) {
				$report = get_post($report_id);

				// if term doesn't exist create it
				if (!$term = term_exists($report_category, 'decom_report_categories')) {
					$term = wp_insert_term($report_category, 'decom_report_categories');
				}

				wp_set_object_terms($report_id, intval($term['term_id']), 'decom_report_categories');

				$report_details = array(
					'product' => $product,
					'date_start' => $date_start,
					'date_end' => $date_end,
					'report_url' => $report_url,
					'report_filename' => $newfile_name,
					'report_file' => $newfile,
					'report_dir' => $plugin_dir
				);

				if ($job) {
					$job_reports = get_post_meta($job->ID, '_decom_job_reports', true);
					$job_reports[$report_id] = $report_id;
					update_post_meta($job->ID, '_decom_job_reports', $job_reports);
					$report_details['job_id'] = $job->ID;
				}

				add_post_meta($report_id, '_decom_report_details', $report_details, true);
			} else {
				$error = __('Error saving report.', 'export-for-memberpress');
			}
		}

		return array($report, $report_url, $error);
	}


	/**
	 *
	 * Get user login entries count
	 *
	 * @since 1.0.0
	 *
	 */

	public static function get_login_entries_count($product, $week, $start_date, $end_date)
	{
		global $wpdb;
		$mepr_db = new MeprDb();
		$query = "SELECT COUNT(*) FROM {$mepr_db->events} WHERE event = 'login'";
		return $wpdb->get_var($query);
	}



	/**
	 *
	 * Get user count
	 *
	 * @since 1.0.0
	 *
	 */

	public static function get_user_count($product, $week, $start_date, $end_date)
	{
		global $wpdb;
		$mepr_db = new MeprDb();
		// limit week to -2 to 0 weeks or 1 for date range
		$week = ($week <= 1 && $week > -3 ) ? $week : 0;

		  if(!MeprUtils::is_mepr_admin()) { //Make sure we're an admin
		    return;
		  }

		$mysql_lifetime = $wpdb->prepare('%s',MeprUtils::mysql_lifetime());
		$mysql_membership = $wpdb->prepare('%d', $product);

		// custom date range
		if ($week == 1 && $start_date && $end_date) {
			$week_start = date('Y-m-d 00:00:00', strtotime($start_date));
			$week_end = date('Y-m-d 23:59:59', strtotime($end_date));
			$year_start = date('Y-01-01 00:00:00', strtotime($end_date));
		} else {
		    $week = $week < 0 ? strtotime($week . ' weeks') : time();
		    $day = date('w', $week);
			$week_start = date('Y-m-d 00:00:00', strtotime('-'.$day.' days', $week ));
			$week_end = date('Y-m-d 00:00:00', strtotime('+'.(7-$day).' days', $week ));
		    $year_start = date('Y-01-01 00:00:00', strtotime(date('Y')));
		}

		$mysql_startweek = $wpdb->prepare('%s', $week_start);
		$mysql_endweek = $wpdb->prepare('%s', $week_end);
		$mysql_startyear = $wpdb->prepare('%s', $year_start);
		$mysql_now = $wpdb->prepare('%s',MeprUtils::mysql_now());

		$q = "SELECT COUNT(*)
		      FROM {$wpdb->users} AS u
		        INNER JOIN {$mepr_db->transactions} AS t
		          ON u.ID = t.user_id AND (t.status = 'complete' OR t.status = 'confirmed') AND (t.expires_at IS NULL OR t.expires_at = 0)
		        INNER JOIN {$wpdb->posts} AS p
		          ON t.product_id = p.ID";
		if ($mysql_membership) {
			$q .= " AND p.ID = {$mysql_membership} ";
		}

		return $wpdb->get_var($q);
	}


	/**
	 *
	 * Retrun history reports
	 *
	 * @since 1.2.0
	 */
	public static function get_history_reports($report_cat = false)
	{
		$args = array(
			'post_type' => 'decom_reports',
			'posts_per_page' => -1,
			'odrerby' => 'modified',
			'order' => 'ASC'
		);

		if ($report_cat) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'decom_report_categories',
					'field'    => 'name',
					'terms'    => array($report_cat),
				),
			);

		}

		return get_posts($args);
	}

	/**
	 *
	 * Retrun count of history reports
	 *
	 * @since 1.2.0
	 */
	public static function get_report_count($report_cat = false)
	{
		return count(self::get_history_reports($report_cat));
	}

	/**
	 *
	 * Retrun jobs
	 *
	 * @since 1.2.0
	 */
	public static function get_jobs()
	{
		$args = array(
			'post_type' => 'decom_jobs',
			'post_status' => 'any',
			'posts_per_page' => -1,
			'odrerby' => 'modified',
			'order' => 'ASC'
		);

		return get_posts($args);
	}

	/**
	 *
	 * Retrun setging profiles
	 *
	 * @since 1.2.0
	 */
	public static function get_profiles($profile_cat = 'ftp')
	{
		$args = array(
			'post_type' => 'decom_profiles',
			'tax_query' => array(
					array(
						'taxonomy' => 'decom_profile_categories',
						'field'    => 'name',
						'terms'    => array($profile_cat),
					),
				),
			'posts_per_page' => -1,
			'odrerby' => 'modified',
			'order' => 'ASC'
		);

		return get_posts($args);
	}

	/**
	 *
	 * Get profile title
	 *
	 * @since 1.2.0
	 */
	public static function get_profile_title($profile_id = 0)
	{
		if ($profile_id && $profile = get_post($profile_id)) {
			return $profile->post_title;
		}

		return false;
	}

	/**
	 *
	 * Create a new job
	 *
	 * @since 1.2.0
	 */
	public static function add_job($product, $schedule, $weekday, $filename, $report_name, $ftp_profile, $email_profile, $job_id = null)
	{
		$content_schedule = $schedule === 'week' ? $schedule . ', on ' . self::$weekdays[$weekday] : $schedule;

		$post_date = self::next_job_time($schedule, $weekday);
		$post_date_gmt = self::next_job_time($schedule, $weekday, true);

		$post = array(
		  'post_content'   => sprintf(__('This report will be generated each %s.', 'export-for-memberpress'), $content_schedule), // The full text of the post.
		  'post_title'     => $report_name ? $report_name : 'Scheduled report: each ' . $content_schedule, // The title of your post.
		  'post_status'    => 'future', // Default 'draft'.
		  'post_type'      => 'decom_jobs', // Default 'post'.
		  'ping_status'    => 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
		  // 'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
		  // 'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
		  // 'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
		  // 'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
		  // 'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
		  // 'guid'           => // Skip this and let Wordpress handle it, usually.
		  // 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
		  // 'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
		  'post_date'      => $post_date, // [ Y-m-d H:i:s ] // The time post was made.
		  'post_date_gmt'  => $post_date_gmt, // The time post was made, in GMT.
		  'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
		  // 'post_category'  => [ array(<category id>, ...) ] // Default empty.
		  // 'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
		  // 'tax_input'      => [ array( <taxonomy> => <array | string>, <taxonomy_other> => <array | string> ) ] // For custom taxonomies. Default empty.
		  // 'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
		);
		$edit = false;

		if ($job_id) {
			$post['ID'] = $job_id;
			$edit = true;
		}
		$job_id = wp_insert_post($post);

		if ($job_id) {
			$job = get_post($job_id);
			$job_details = array(
				'product' => $product,
				'schedule' => $schedule,
				'filename' => $filename
			);
			if ($schedule === 'week') {
				$job_details['weekday'] = intval($weekday);
			}
			if ($ftp_profile) {
				$job_details['ftp_profile'] = $ftp_profile;
			}
			if ($email_profile) {
				$job_details['email_profile'] = $email_profile;
			}
			if ($edit) {
				delete_post_meta($job_id, '_decom_job_details');
			}
			add_post_meta($job_id, '_decom_job_details', $job_details, true);

			return $job;
		}

		return false;
	}

	/**
	 *
	 * Create a new ftp profile
	 *
	 * @since 1.2.0
	 */
	public static function add_ftp_profile($server, $username, $password, $profile_name, $profile_id = false)
	{
		$profile = false;
		$error = false;
		$test_time = date(get_option('date_format') . ' ' .  get_option('time_format'));

		// if updating and password left same
		if ($profile_id && $password === '********' && $old_profile = get_post($profile_id)) {
			$profile_details = get_post_meta($profile_id, '_decom_profile_details', true);
			$password = isset($profile_details['password']) ? $profile_details['password'] : '';
		}
		// set up basic connection (supress warnings)
		$conn_id = @ftp_connect($server);

		if ($conn_id) {
			$login_result = @ftp_login($conn_id, $username, $password);
			$test_time = date(get_option('date_format') . ' ' .  get_option('time_format'));
		} else {
			$error = __('FTP connection has failed! Please enter a valid FTP server address.', 'export-for-memberpress');

			return array($profile, $error);
		}

		// check connection
		if ((!$conn_id) || (!$login_result)) {
		    $error = sprintf(__('FTP connection has failed! (Attempted to connect to %s for user %s). Make sure your FTP credentials are valid.', 'export-for-memberpress'), $server, $username);
		} else {
			$post = array(
			  'post_content'   => sprintf(__('Successfully connected to %s as %s (%s).', 'export-for-memberpress'), '<strong>' . $server . '</strong>', '<strong>' . $username . '</strong>', $test_time), // The full text of the post.
			  'post_title' 	   => $profile_name, // The title of your post.
			  'post_status'    => 'publish', // Default 'draft'.
			  'post_type'      => 'decom_profiles', // Default 'post'.
			  'ping_status'    => 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
			  // 'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
			  // 'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
			  // 'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
			  // 'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
			  // 'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
			  // 'guid'           => // Skip this and let Wordpress handle it, usually.
			  // 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
			  // 'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
			  // 'post_date'      => [ Y-m-d H:i:s ] // The time post was made.
			  // 'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
			  'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
			  // 'post_category'  => [ array(<category id>, ...) ] // Default empty.
			  // 'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
			  // 'tax_input'      => array( 'decom_profile_categories', 'ftp'), // For custom taxonomies. Default empty.
			  // 'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
			);

			$edit = false;

			if ($profile_id) {
				$post['ID'] = $profile_id;
				$edit = true;
			}

			$profile_id = wp_insert_post($post);

			if ($profile_id) {
				$profile = get_post($profile_id);

				// if ftp term doesn't exist create it
				if (!$term = term_exists('ftp', 'decom_profile_categories')) {
					$term = wp_insert_term('ftp', 'decom_profile_categories');
				}

				wp_set_object_terms($profile_id, intval($term['term_id']), 'decom_profile_categories');

				$profile_details = array(
					'server' => $server,
					'username' => $username,
					'password' => $password
				);
				if ($edit) {
					delete_post_meta($profile_id, '_decom_profile_details');
				}
				add_post_meta($profile_id, '_decom_profile_details', $profile_details, true);

			} else {
				$error = __('Error creating new ftp profile.', 'export-for-memberpress');
			}
		}


		return array($profile, $error);
	}

	/**
	 *
	 * Create a new email profile
	 *
	 * @since 1.2.0
	 */
	public static function add_email_profile($from, $to, $subject, $cc, $bcc, $text, $attachment, $profile_name, $profile_id)
	{
		$profile = false;
		$error = false;
		$subject = $subject ? $subject : __('New MemberPress report generated on {site_name}', 'export_for_memberpress');

		$profile_name = $profile_name ? $profile_name : sprintf(__('Email profile (From: %s, To: %s, Subject: %s)', 'export-for-memberpress'), $from, $to, $subject);

		$post = array(
		  'post_content'   => $text, // The full text of the post.
		  'post_title' 	   => $profile_name, // The title of your post.
		  'post_status'    => 'publish', // Default 'draft'.
		  'post_type'      => 'decom_profiles', // Default 'post'.
		  'ping_status'    => 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
		  // 'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
		  // 'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
		  // 'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
		  // 'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
		  // 'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
		  // 'guid'           => // Skip this and let Wordpress handle it, usually.
		  // 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
		  // 'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
		  // 'post_date'      => [ Y-m-d H:i:s ] // The time post was made.
		  // 'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
		  'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
		  // 'post_category'  => [ array(<category id>, ...) ] // Default empty.
		  // 'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
		  // 'tax_input'      => array( 'decom_profile_categories', 'ftp'), // For custom taxonomies. Default empty.
		  // 'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
		);

		$edit = false;

		if ($profile_id) {
			$post['ID'] = $profile_id;
			$edit = true;
		}

		$profile_id = wp_insert_post($post);

		if ($profile_id) {
			$profile = get_post($profile_id);

			// if ftp term doesn't exist create it
			if (!$term = term_exists('email', 'decom_profile_categories')) {
				$term = wp_insert_term('email', 'decom_profile_categories');
			}

			wp_set_object_terms($profile_id, intval($term['term_id']), 'decom_profile_categories');

			$profile_details = array(
				'from' => $from,
				'to' => $to,
				'subject' => $subject,
				'cc' => $cc,
				'bcc' => $bcc,
				'attachment' => $attachment,
			);
			if ($edit) {
				delete_post_meta($profile_id, '_decom_profile_details');
			}

			add_post_meta($profile_id, '_decom_profile_details', $profile_details, true);

		} else {
			$error = __('Error creating new email profile.', 'export-for-memberpress');
		}


		return array($profile, $error);
	}

	/**
	 *
	 * Delete job
	 *
	 * @since 1.2.0
	 */
	public static function delete_post($post_id)
	{
		return wp_delete_post($post_id, true);
	}

	/**
	 *
	 * Toggle job status (future/draft)
	 *
	 * @since 1.2.0
	 */
	public static function toggle_job_status($job_id)
	{
		$job = get_post($job_id);
		$new_job_status = false;
		if ($job && $job->post_type === 'decom_jobs') {
			$new_post_status = $job->post_status === 'future' ? 'draft' : 'future';
			$new_post_args = array(
				'ID' => $job_id,
				'post_status' => $new_post_status,
			);

			if ($new_post_status === 'future') {
				$new_job_status = __('enabled', 'export-for-memberpress');
				$job_details = get_post_meta($job_id, '_decom_job_details', true);
				$schedule = $job_details['schedule'];
				$weekday = $schedule === 'week' ? $job_details['weekday'] : false;
				$new_post_args['post_date'] = self::next_job_time($schedule, $weekday);
				$new_post_args['post_date_gmt'] = self::next_job_time($schedule, $weekday, true);
			} else {
				$new_job_status = __('disabled', 'export-for-memberpress');
			}
			if ($toggled = wp_update_post($new_post_args)) {
				return $new_job_status;
			}
		}

		return false;
	}

	/**
	 *
	 * Default email text
	 *
	 * @since 1.2.0
	 *
	 */
	public static function default_email_text()
	{
		return __('Your scheduled memberpress report has been generated successfully.', 'export-for-memberpress');
	}


	/**
	 *
	 * Filter email values for {admin_email}
	 *
	 * @since 1.2.0
	 *
	 */
	public static function filter_email($email)
	{
		return str_replace('{admin_email}', get_option('admin_email'), $email);
	}

	/**
	 *
	 * Filter email values for {admin_email}
	 *
	 * @since 1.2.0
	 *
	 */
	public static function filter_email_text($text)
	{
		return str_replace('{site_name}', get_option('blogname'), self::filter_email($text));
	}

	/**
	 *
	 * Next job time based on job schedule
	 *
	 * @since 1.2.0
	 */
	public static function next_job_time($schedule, $weekday = false, $gmt = false)
	{
		$time = time();
		$post_date = false;

		$date_func = $gmt ? 'gmdate' : 'date';

		// debug
		// return $date_func('Y-m-d H:i:s', strtotime('+ 1 minutes', $time ));

		switch ($schedule) {
			case 'day':
				$post_date = $date_func('Y-m-d 00:00:00', strtotime('+ 1 days', $time ));
				break;
			case 'week':
				$weekday = $weekday ? $weekday : 0;
			    $day = $date_func('w', $time);
			    if ($day >= $weekday) {
			    	$add_days = 7 - ($day - $weekday);
			    } else {
			    	$add_days = $weekday - $day;
			    }
				$post_date = $date_func('Y-m-d 00:00:00', strtotime('+ ' . $add_days . ' days', $time ));
				break;
			case 'month':
				$post_date = $date_func('Y-m-01 00:00:00', strtotime('+ 1 months', $time));
				break;
			case 'year':
				$post_date = $date_func('Y-01-01 00:00:00', strtotime('+ 1 years', $time));
				break;
			default:
				$post_date = false;
		}

		return $post_date;
	}

	/**
	 *
	 * Print job schedule
	 *
	 * @since 1.2.0
	 */
	public static function job_schedule($job)
	{
		$schedule = '';

		if ($job->post_status === 'future') {
			$job_details = get_post_meta($job->ID, '_decom_job_details', true);
			$schedule = $job_details['schedule'];
			$weekday = $schedule === 'week' ? $job_details['weekday'] : false;
			$next_job_time = date('l, F j, Y', strtotime(self::next_job_time($schedule, $weekday)));
			$schedule = '<span class="dashicons dashicons-info"></span> ';
			$schedule .= $job->post_content . ' ';
			$schedule .= sprintf(__('Next time this report will be generated is: %s.', 'export-for-memberpress'), '<strong>' . $next_job_time . '</strong>');
		} else {
			$schedule = '<span class="dashicons dashicons-warning"></span> ';
			$schedule .= '<strong>' . __('This scheduled report is disabled and will not be generating reports until it is enabled again.', 'export-for-memberpress') . '</strong>';
		}

		return $schedule;
	}

	/**
	 *
	 * Run job and reschedule for next time
	 *
	 * @since 1.2.0
	 */
	public static function run_job($post)
	{
		if ($post->post_type == 'decom_jobs') {
			$job = $post;

			$job_details = get_post_meta($job->ID, '_decom_job_details', true);
			$schedule = $job_details['schedule'];
			$weekday = $schedule === 'week' ? $job_details['weekday'] : false;

			// Reschedule report for next time (wp_update_post and wp_insert_post don't do the job...)
			$new_post_args['ID'] = $job->ID;
			$new_post_args['post_status'] = 'future';
			$new_post_args['post_date'] = self::next_job_time($schedule, $weekday);
			$new_post_args['post_date_gmt'] = self::next_job_time($schedule, $weekday, true);
			wp_update_post($new_post_args);

			// Generate report
			$product = intval($job_details['product']);
			$week = 1;
			$filename = $job_details['filename'];
			$time = time();
			$hash = md5($time);
			$start_date = self::get_job_report_start_date($schedule, $weekday);
			$end_date = self::get_job_report_end_date($schedule, $weekday);
			$limit = false;
			$offset = false;

			$file = self::generate_report($product, $week, $start_date, $end_date, $filename, $limit, $offset, $hash);

			list($report, $report_url, $error) = self::save_report($product, $week, $start_date, $end_date, $filename, $hash, $job);

			if (!$error && $report) {
				if (isset($job_details['ftp_profile'])) {
					$error = self::report_ftp($job_details['ftp_profile'], $report);
				}
				if (!$error && isset($job_details['email_profile'])) {
					$error = self::report_email($job_details['email_profile'], $report);
				}
			}

			if ($error) {
				self::handle_failed_job($job, $report, $error);
			}

		}

	}

	/**
	 *
	 * Handle job faliure
	 *
	 * @since 1.2.0
	 */
	public static function handle_failed_job($job, $report, $error)
	{
		// get new post data to check if it is rescheduled or canceled etc...
		$job = get_post($job->ID);

		$admin_email = get_option('admin_email');
		$to = $admin_email;
		$subject = 'Export for MemberPress ' . __('Scheduled Report Failed', 'export-for-memberpress');

		$message = "There was an error generating a scheduled report on " . get_option('blogname') . ".\r\n\r\n";
		$message .= "Scheduled report name: {$job->post_title}.\r\n\r\n";
		$message .= "Error: {$error}.\r\n\r\n";

		if ($report) {
			$message .= "Report file was generated and saved to report history before the error happened ({$report->post_title} #{$report->ID}).\r\n\r\n";
		} else {
			$message .= "Report file was not generated or saved to report history before the errror happened.\r\n\r\n";
		}

		if ($job->post_status === 'future') {
			$next_time = date(get_option('date_format'), strtotime($job->post_date));
			$message .= "Report was rescheduled to: {$next_time}.\r\n\r\n";
		} else {
			$message .= "This scheduled report is disabled and will not be generating reports until it is enabled again.\r\n\r\n";
		}

		$message .= "Please contact plugin authors if you are getting this error often.\r\n\r\n";


		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=iso-8859-1";
		$headers[] = "From: " . $admin_email;
		$headers[] = "Subject: {$subject}";

		wp_mail($to, $subject, $message, implode("\r\n", $headers));

	}


	/**
	 *
	 * Determine report start date based on job schedule
	 *
	 * @since 1.2.0
	 */
	public static function get_job_report_start_date($schedule, $weekday = false)
	{
		$time = time();
		$start_date = false;
		switch ($schedule) {
			case 'day':
				$start_date = date('Y-m-d 00:00:00', strtotime('- 1 days', $time ));
				break;
			case 'week':
				$start_date = date('Y-m-d 00:00:00', strtotime('- 7 days', $time ));
				break;
			case 'month':
				$start_date = date('Y-m-01 00:00:00', strtotime('-1 months', $time));
				break;
			case 'year':
				$start_date = date('Y-01-01 00:00:00', strtotime('-1 years', $time));
				break;
			default:
				$start_date = false;
		}

		return $start_date;
	}

	/**
	 *
	 * Determine report end date based on job schedule
	 *
	 * @since 1.2.0
	 */
	public static function get_job_report_end_date($schedule, $weekday = false)
	{
		$time = time();
		$end_date = false;
		switch ($schedule) {
			case 'day':
				$end_date = date('Y-m-d 23:59:59', strtotime('- 1 days', $time ));
				break;
			case 'week':
				$end_date = date('Y-m-d 23:59:59', strtotime('-1 days', $time ));
				break;
			case 'month':
				$last_month = date('n', strtotime('-1 months', $time));
				$last_month_year = date('Y', strtotime('-1 months', $time));
				$last_month_last_day = cal_days_in_month(CAL_GREGORIAN, $last_month, $last_month_year);
				$end_date = date('Y-m-' . $last_month_last_day . ' 23:59:59', strtotime('-1 months', $time));
				break;
			case 'year':
				$end_date = date('Y-12-31 23:59:59', strtotime('-1 years', $time));
				break;
			default:
				$end_date = false;
		}

		return $end_date;
	}

	/**
	 *
	 * Send report via ftp
	 *
	 * @since 1.2.0
	 */
	public static function report_ftp($profile_id, $report)
	{
		$ftp_profile = get_post($profile_id);

		if($ftp_profile) {
			$profile_details = get_post_meta($ftp_profile->ID, '_decom_profile_details', true);

			$conn_id = @ftp_connect($profile_details['server']);

			if ($conn_id) {
				$login_result = @ftp_login($conn_id, $profile_details['username'], $profile_details['password']);
			} else {
				return  __('FTP connection has failed! Please enter a valid FTP server address.', 'export-for-memberpress');

			}

			// check connection
			if ((!$conn_id) || (!$login_result)) {
			    return sprintf(__('FTP connection has failed! (Attempted to connect to %s for user %s). Make sure your FTP credentials are valid.', 'export-for-memberpress'), $profile_details['server'], $profile_details['username']);
			} else {
				// connection works, upload file
				// upload the file
				ftp_pasv($conn_id, true);
				$report_details = get_post_meta($report->ID, '_decom_report_details', true);
				$upload_dir = 'decom-memberpress-reports/';
				// Try to make the directory, silent error if it already exists
				@ftp_mkdir($conn_id, $upload_dir);
				if (!$upload = ftp_put($conn_id, $upload_dir . '/' . $report_details['report_filename'], $report_details['report_file'], FTP_ASCII)) {
					return __('Could not upload generated FTP file. Make sure your FTP credentials are valid.');
				}

				ftp_close($conn_id);
				return false;
			}
		}

		return __('Could not upload generated FTP file (FTP profile error).');
	}

	/**
	 *
	 * Send report email notification
	 *
	 * @since 1.2.0
	 */
	public static function report_email($profile_id, $report)
	{
		$email_profile = get_post($profile_id);

		if($email_profile) {
			$profile_details = get_post_meta($email_profile->ID, '_decom_profile_details', true);

			if ($profile_details['attachment']) {
				$report_details = get_post_meta($report->ID, '_decom_report_details', true);
				$attachments = array($report_details['report_file']);
			} else {
				$attachments = array();
			}
			$to      = self::filter_email($profile_details['to']);
			$subject = self::filter_email_text($profile_details['subject']);
			$message = self::filter_email_text(self::filter_email($email_profile->post_content));

			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: " . self::filter_email($profile_details['from']);
			if ($profile_details['cc']) {
				$headers[] = "Cc: " . self::filter_email($profile_details['cc']);
			}
			if ($profile_details['bcc']) {
				$headers[] = "Bcc: " . self::filter_email($profile_details['bcc']);
			}
			$headers[] = "Reply-To: " . self::filter_email($profile_details['from']);
			$headers[] = "Subject: {$subject}";

			if (!wp_mail($to, $subject, $message, implode("\r\n", $headers), $attachments)) {
				return __('Could not send email notifications (wp_mail error).');
			} else {
				return false;
			}
		}

		return __('Could not send email notifications (Email profile error).');
	}


}
