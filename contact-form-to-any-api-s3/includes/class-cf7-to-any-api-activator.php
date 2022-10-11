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

class Cf7_To_Any_Api_S3_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        //Create Custom Database Table
        self::install_db();
	}

    /**
     * Created Custom Database Table
     *
     * On plugin activation time created custom database table
     *
     * @since    1.0.0
     */
    public static function install_db() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix.'cf7anyapi_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            form_id int(11) NOT NULL,
            post_id int(11) NOT NULL,
            log text NOT NULL,
            created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        dbDelta( $sql );
    }
}