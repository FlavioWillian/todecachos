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

use Aws\S3\S3Client;

class Cf7_To_Any_Api_S3_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

//        $this->plugin_name = $plugin_name;
//        $this->version = $version;
        $this->plugin_name = "CF7 tp API/S3";
        $this->version = "2.1.1";

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cf7_To_Any_Api_S3_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cf7_To_Any_Api_S3_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/cf7-to-any-api-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cf7_To_Any_Api_S3_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cf7_To_Any_Api_S3_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $data = array(
            'site_url' => site_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/cf7-to-any-api-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'ajax_object', $data);

    }

    /**
     * Check Plugin Dependencies
     *
     * @since    1.0.0
     */
    public function cf7_to_any_api_verify_dependencies()
    {
        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
            echo '<div class="notice notice-warning is-dismissible">
	             <p>Contact form 7 api integrations requires CONTACT FORM 7 Plugin to be installed and active</p>
	         </div>';
        }
    }

    /**
     * Register the Custom Post Type
     *
     * @since    1.0.0
     */
    public function cf7anyapi_custom_post_type()
    {
        $supports = array(
            'SL - CF7 Data to Bucket S3', // Custom Post Type Title
        );
        $labels = array(
            'name' => _x('CF7 to API/S3', 'plural'),
            'singular_name' => _x('cf7 to api/s3', 'singular'),
            'menu_name' => _x('CF7 to API/S3', 'admin menu'),
            'name_admin_bar' => _x('CF7 to API/S3', 'admin bar'),
            'add_new' => _x('Add New CF7 API/S3', 'add new'),
            'add_new_item' => __('Add New CF7 API/S3'),
            'new_item' => __('New CF7 API/S3'),
            'edit_item' => __('Edit CF7 API/S3'),
            'view_item' => __('View CF7 API/S3'),
            'all_items' => __('All CF7 API/S3'),
            'not_found' => __('No CF7 API/S3 found.'),
            'register_meta_box_cb' => 'aps_metabox',
        );
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'hierarchical' => false,
            'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
            'publicly_queryable' => false,  // you should be able to query it
            'show_ui' => true,  // you should be able to edit it in wp-admin
            'exclude_from_search' => true,  // you should exclude it from search results
            'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
            'has_archive' => false,  // it shouldn't have archive page
            'rewrite' => false,  // it shouldn't have rewrite rules
            'menu_icon' => 'dashicons-rest-api',
        );
        register_post_type('cf7_to_any_api', $args);
        flush_rewrite_rules();
    }

    /**
     * Register the Custom Meta Boxes
     *
     * @since    1.0.0
     */
    public function cf7anyapi_metabox()
    {
        add_meta_box(
            'cf7anyapi-setting',
            __('Contact Form 7 Any Api Setting', 'cf7-to-any-api'),
            array($this, 'cf7anyapi_settings'),
            'cf7_to_any_api'
        );
    }

    /**
     * Register the Submenu
     *
     * @since    1.0.0
     */
    public function cf7anyapi_register_submenu()
    {
        add_submenu_page(
            'edit.php?post_type=cf7_to_any_api',
            __('Logs', 'cf7-to-any-api'),
            __('Logs', 'cf7-to-any-api'),
            'manage_options',
            'cf7anyapi_logs',
            array(&$this, 'cf7anyapi_submenu_callback')
        );
    }

    /**
     * Register Submenu Callback Function
     *
     * @since    1.0.0
     */
    public function cf7anyapi_submenu_callback()
    {
        $myListTable = new cf7anyapis3_List_Table();
        echo '<div class="wrap"><h2>CF7 To Any API Log Data</h2>';
        $myListTable->prepare_items();
        $myListTable->display();
        echo '</div>';
    }

    /**
     * Registered Metaboxes Fields
     *
     * @since    1.0.0
     */
    public static function cf7anyapi_settings()
    {

        include dirname(__FILE__) . '/partials/cf7-to-any-api-admin-display.php';
    }

    /**
     * Update the Metaboxes value on Post Save
     *
     * @since    1.0.0
     */
    public static function cf7anyapi_update_settings($cf7_to_any_api_id, $cf7_to_any_api)
    {
        $Cf7_To_Any_Api = new Cf7_To_Any_Api_S3;
        if ($cf7_to_any_api->post_type == 'cf7_to_any_api') {
            $status = 'false';
            if (isset($_POST['cf7_to_any_api_cpt_nonce']) && wp_verify_nonce($_POST['cf7_to_any_api_cpt_nonce'], 'cf7_to_any_api_cpt_nonce')) {

                $options['cf7anyapi_selected_form'] = (int)stripslashes($_POST['cf7anyapi_selected_form']);
                $options['cf7anyapi_base_url'] = sanitize_url($_POST['cf7anyapi_base_url']);
                $options['cf7anyapi_basic_auth'] = sanitize_text_field($_POST['cf7anyapi_basic_auth']);
                $options['cf7anyapi_bearer_auth'] = sanitize_text_field($_POST['cf7anyapi_bearer_auth']);
                $options['cf7anyapi_input_type'] = sanitize_text_field($_POST['cf7anyapi_input_type']);
                $options['cf7anyapi_method'] = sanitize_text_field($_POST['cf7anyapi_method']);
                $options['cf7anyapi_form_field'] = $Cf7_To_Any_Api->Cf7_To_Any_Api_sanitize_array($_POST['cf7anyapi_form_field']);

                foreach ($options as $options_key => $options_value) {
                    $response = update_post_meta($cf7_to_any_api_id, $options_key, $options_value);
                }
                if ($response) {
                    $status = 'true';
                }
            }
        }
    }

    /**
     * On Metabox Form Change Show that form fields
     *
     * @since    1.0.0
     */
    public static function cf7_to_any_api_get_form_field_function()
    {
        if (empty((int)stripslashes($_POST['form_id']))) {
            echo json_encode('No Fields Found for Selected Form.');
            exit();
        }
        $html = '';
        $form_ID = (int)stripslashes($_POST['form_id']); # change the 80 to your CF7 form ID
        $ContactForm = WPCF7_ContactForm::get_instance($form_ID);
        $form_fields = $ContactForm->scan_form_tags();

        foreach ($form_fields as $form_fields_key => $form_fields_value) {

            if ($form_fields_value->basetype != 'submit') {
                $html .= '<div class="cf7anyapi_field">';
                $html .= '<label for="cf7anyapi_' . $form_fields_value->raw_name . '">' . $form_fields_value->name . '</label>';
                $html .= '<input type="text" id="cf7anyapi_' . $form_fields_value->raw_name . '" name="cf7anyapi_form_field[' . $form_fields_value->name . ']" placeholder="Enter Mapping Key Field Name">';
                $html .= '</div>';
            }
        }
        echo json_encode($html);
        exit();
    }

    /**
     * On Form Submit Selected Form Data send to API
     *
     * @since    1.0.0
     */
    public static function cf7_to_any_api_send_data_to_api($WPCF7_ContactForm)
    {
        global $wpdb;
        $form_id = (int)stripslashes($_POST['_wpcf7']);
        $args = array(
            'post_type' => 'cf7_to_any_api',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'cf7anyapi_selected_form',
                    'value' => $form_id,
                    'compare' => '=',
                ),
            ),
        );

        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {

            while ($the_query->have_posts()) {
                $the_query->the_post();
                $api_post_array = array();

                $cf7anyapi_form_field = array_filter(get_post_meta(get_the_ID(), 'cf7anyapi_form_field', true));
                $cf7anyapi_base_url = get_post_meta(get_the_ID(), 'cf7anyapi_base_url', true);

                $cf7anyapi_basic_auth = get_post_meta(get_the_ID(), 'cf7anyapi_basic_auth', true);
                $cf7anyapi_bearer_auth = get_post_meta(get_the_ID(), 'cf7anyapi_bearer_auth', true);

                $cf7anyapi_input_type = get_post_meta(get_the_ID(), 'cf7anyapi_input_type', true);
                $cf7anyapi_method = get_post_meta(get_the_ID(), 'cf7anyapi_method', true);

                foreach ($cf7anyapi_form_field as $key => $value) {
                    $api_post_array[$value] = (is_array($_POST[$key]) ? implode(',', Cf7_To_Any_Api::Cf7_To_Any_Api_sanitize_array($_POST[$key])) : sanitize_text_field($_POST[$key]));
                }

                self::cf7anyapi_send_lead($api_post_array, $cf7anyapi_base_url, $cf7anyapi_input_type, $cf7anyapi_method, $form_id, get_the_ID(), $cf7anyapi_basic_auth, $cf7anyapi_bearer_auth);
            }
        }
        wp_reset_postdata();
    }

    /**
     * Child Fuction of specific form data send to the API
     *
     * @since    1.0.0
     */
    public static function cf7anyapi_send_lead($data, $url, $input_type, $method, $form_id, $post_id, $basic_auth = '', $bearer_auth = '')
    {

        if ($method == 'GET' && ($input_type == 'params' || $input_type == 'json')) {
            $args = array(
                'timeout' => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
                'blocking' => true,
                'headers' => array(),
                'cookies' => array(),
                'body' => null,
                'compress' => false,
                'decompress' => true,
                'sslverify' => true,
                'stream' => false,
                'filename' => null
            );

            if ($input_type == 'params') {
                $data_string = http_build_query($data);
                $url = strpos('?', $url) ? $url . '&' . $data_string : $url . '?' . $data_string;
            } else {
                $args['headers']['Content-Type'] = 'application/json';
                $json = self::Cf7_To_Any_Api_parse_json($data);

                if (is_wp_error($json)) {
                    return $json;
                } else {
                    $args['body'] = $json;
                }
            }

            $result = wp_remote_get($url, $args);
            self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result['body']);
        } else {
            if ($input_type == 'params' || $input_type == 'json') {
                $args = array(
                    'timeout' => 5,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
                    'blocking' => true,
                    'headers' => array(),
                    'cookies' => array(),
                    'body' => $data,
                    'compress' => false,
                    'decompress' => true,
                    'sslverify' => true,
                    'stream' => false,
                    'filename' => null
                );

                if (isset($basic_auth) && $basic_auth !== '') {
                    $args['headers']['Authorization'] = 'Basic ' . base64_encode($basic_auth);
                }

                if (isset($bearer_auth) && $bearer_auth !== '') {
                    $args['headers']['Authorization'] = 'Bearer ' . $bearer_auth;
                }

                if ($input_type == "xml") {
                    $args['headers']['Content-Type'] = 'text/xml';
                    $xml = self::Cf7_To_Any_Api_get_xml($data);

                    if (is_wp_error($xml)) {
                        return $xml;
                    }

                    $args['body'] = $xml->asXML();
                } elseif ($input_type == "json") {
                    $args['headers']['Content-Type'] = 'application/json';
                    $json = self::Cf7_To_Any_Api_parse_json($data);
                    if (is_wp_error($json)) {
                        return $json;
                    } else {
                        $args['body'] = $json;
                    }
                }

                $result = wp_remote_post($url, $args);
                self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result['body']);
            } else {
                $current = "";
                $keyBucket = 'wp-cf7-' . date("Ymd") . '-' . uniqid() . '.txt';
                $region = 'us-east-1';
                $version = 'latest';
                $contentType = 'text/plain';
                $bucket = str_replace('#', '', ltrim($url));

                foreach ($data as $key => $value) {
                    $current .= "$key: '$value', ";
                }

                $s3client = new S3Client([
                    'region' => $region,
                    'version' => $version,
                    'credentials' => [
                        'key' => $basic_auth,
                        'secret' => $bearer_auth,
                    ]
                ]);

                try {
                    $s3client->putObject([
                        'Bucket' => $bucket,
                        'Key' => $keyBucket,
                        'ContentType' => $contentType,
                        'ACL' => 'private',
                        'ServerSideEncryption' => 'AES256',
                        'Body' => $current
                    ]);

                    $result = array(
                        "message" => "OK",
                        "statusCode" => "200"
                    );
                    self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result);
                } catch (S3Exception $e) {
                    self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $e->getMessage());
                } catch (Exception $exception) {
                    self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $exception->getMessage());
                    exit("Please fix error with file upload before continuing.");
                }
            }
        }
    }

    /**
     * Form Data convert into JSON formate
     *
     * @since    1.0.0
     */
    public static function Cf7_To_Any_Api_parse_json($string)
    {
        return json_encode($string);
    }

    /**
     * Form Data convert into XML formate
     *
     * @since    1.0.0
     */
    public static function Cf7_To_Any_Api_get_xml($lead)
    {
        $xml = "";
        if (function_exists('simplexml_load_string')) {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($lead);
            if ($xml == false) {
                $xml = new WP_Error(
                    'xml',
                    __("XML Structure is incorrect", 'cf7-to-any-api')
                );
            }
        }

        return $xml;
    }

    /**
     * API response store into Database
     *
     * @since    1.0.0
     */
    public static function Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $response)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'cf7anyapi_logs';
        $data = array(
            'form_id' => $form_id,
            'post_id' => $post_id,
            'log' => maybe_serialize($response),
        );

        $wpdb->insert($table, $data);
    }
}