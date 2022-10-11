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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CF7_TO_ANY_API_S3_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf7-to-any-api-activator.php
 */
function activate_cf7_to_any_api_s3() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api-activator.php';
    Cf7_To_Any_Api_S3_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf7-to-any-api-deactivator.php
 */
function deactivate_cf7_to_any_api_s3() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api-deactivator.php';
    Cf7_To_Any_Api_S3_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cf7_to_any_api_s3' );
register_deactivation_hook( __FILE__, 'deactivate_cf7_to_any_api_s3' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-any-api.php';

/**
 * AWS SDK version 3
 */
require plugin_dir_path( __FILE__ ) . 'admin/aws/aws-autoloader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7_to_any_api_s3() {

	$plugin = new Cf7_To_Any_Api_s3();
	$plugin->run();

}
run_cf7_to_any_api_s3();
