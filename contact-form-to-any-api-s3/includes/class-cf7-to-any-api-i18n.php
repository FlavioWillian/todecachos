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

class Cf7_To_Any_Api_S3_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cf7-to-any-api-s3',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
