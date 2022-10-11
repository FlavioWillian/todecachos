<?php

/**
 * Plugin Name: SalonLine - CF7 Data to API/S3Bucket
 * Author: SalonLine
 * Author URI: localhost
 * Description: Get CF7 Data and send to API or S3 Bucket
 * Version: 1.1.0
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: cf7-data-to-api-or-bucket-s3
 */

class Cf7_To_Any_Api_S3 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cf7_To_Any_Api_S3_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'CF7_TO_ANY_API_S3_VERSION' ) ) {
			$this->version = CF7_TO_ANY_API_S3_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'cf7-to-any-api-s3';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cf7_To_Any_Api_S3_Loader. Orchestrates the hooks of the plugin.
	 * - Cf7_To_Any_Api_S3_i18n. Defines internationalization functionality.
	 * - Cf7_To_Any_Api_S3_Admin. Defines all hooks for the admin area.
	 * - Cf7_To_Any_Api_S3_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-to-any-api-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-to-any-api-i18n.php';

		/**
		 * The class responsible for defining Custom WP List Table For Log display
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-to-any-api-log-list-table.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cf7-to-any-api-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cf7-to-any-api-public.php';

		$this->loader = new Cf7_To_Any_Api_S3_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cf7_To_Any_Api_S3_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cf7_To_Any_Api_S3_i18n();

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

		$plugin_admin = new Cf7_To_Any_Api_S3_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_init', $plugin_admin, 'cf7_to_any_api_verify_dependencies');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('init', $plugin_admin,'cf7anyapi_custom_post_type', 10);
		$this->loader->add_action('add_meta_boxes', $plugin_admin,'cf7anyapi_metabox');
		$this->loader->add_action('save_post',$plugin_admin,'cf7anyapi_update_settings',10,2);
		$this->loader->add_action('wp_ajax_cf7_to_any_api_get_form_field',$plugin_admin,'cf7_to_any_api_get_form_field_function');
		//$this->loader->add_action('wpcf7_mail_sent',$plugin_admin,'cf7_to_any_api_send_data_to_api');
        	$this->loader->add_action('wpcf7_before_send_mail',$plugin_admin,'cf7_to_any_api_send_data_to_api');
		$this->loader->add_action('admin_menu', $plugin_admin, 'cf7anyapi_register_submenu', 90);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cf7_To_Any_Api_S3_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
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
	 * @return    Cf7_To_Any_Api_S3_Loader    Orchestrates the hooks of the plugin.
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

	/**
	 * Retrieve the all current Post API data
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public function Cf7_To_Any_Api_S3_get_options() {
		global $post;
		$options = [];
		$options['cf7anyapi_selected_form'] = get_post_meta($post->ID,'cf7anyapi_selected_form',true);
		$options['cf7anyapi_base_url'] = get_post_meta($post->ID,'cf7anyapi_base_url',true);
		$options['cf7anyapi_basic_auth'] = get_post_meta($post->ID,'cf7anyapi_basic_auth',true);
		$options['cf7anyapi_bearer_auth'] = get_post_meta($post->ID,'cf7anyapi_bearer_auth',true);
		$options['cf7anyapi_input_type'] = get_post_meta($post->ID,'cf7anyapi_input_type',true);
		$options['cf7anyapi_method'] = get_post_meta($post->ID,'cf7anyapi_method',true);
		$options['cf7anyapi_form_field'] = get_post_meta($post->ID,'cf7anyapi_form_field',true);
		return $options;
	}

	/**
	 * Sanitize Array Value
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public function Cf7_To_Any_Api_sanitize_array($array){
		$sanitize_array = array();

		foreach($array as $key => $value) {
			$sanitize_array[sanitize_text_field($key)] = sanitize_text_field($value);
		}

		return $sanitize_array;
	}

	/**
	 * Saved Form Fields show by default
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public function Cf7_To_Any_Api_default_form_field($form_id){
		$data = array();
		$form_ID     = (int)stripslashes($form_id); # change the 80 to your CF7 form ID 
		$ContactForm = WPCF7_ContactForm::get_instance( $form_ID );
		
		if($ContactForm){
			$form_fields = $ContactForm->scan_form_tags();
    		$data['status'] = 200;
    		$data['fields'] = $form_fields;
    	}
    	else{
    		$data['status'] = 404;
    		$data['fields'] = 'No form Found';
    	}
		return $data;
	}
}
