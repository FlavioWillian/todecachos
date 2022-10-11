<?php

$cf7anyapi_object = new Cf7_To_Any_Api_S3();
$cf7anyapi_options = $cf7anyapi_object->Cf7_To_Any_Api_S3_get_options();

$selected_form = (empty($cf7anyapi_options['cf7anyapi_selected_form']) ? '' : $cf7anyapi_options['cf7anyapi_selected_form']);
$cf7anyapi_base_url = (empty($cf7anyapi_options['cf7anyapi_base_url']) ? '' : $cf7anyapi_options['cf7anyapi_base_url']);
$cf7anyapi_basic_auth = (empty($cf7anyapi_options['cf7anyapi_basic_auth']) ? '' : $cf7anyapi_options['cf7anyapi_basic_auth']);
$cf7anyapi_bearer_auth = (empty($cf7anyapi_options['cf7anyapi_bearer_auth']) ? '' : $cf7anyapi_options['cf7anyapi_bearer_auth']);
$cf7anyapi_input_type = (empty($cf7anyapi_options['cf7anyapi_input_type']) ? '' : $cf7anyapi_options['cf7anyapi_input_type']);
$cf7anyapi_method = (empty($cf7anyapi_options['cf7anyapi_method']) ? '' : $cf7anyapi_options['cf7anyapi_method']);
$cf7anyapi_form_field = (empty($cf7anyapi_options['cf7anyapi_form_field']) ? '' : $cf7anyapi_options['cf7anyapi_form_field']);

if(!class_exists('WPCF7_ContactForm')){
?>
<div id="cf7anyapi_admin" class="cf7anyapi_wrap">
    <p>Contact form 7 api s3 integrations requires CONTACT FORM 7 Plugin to be installed and active</p>
</div>
<?php
}
else{

    if(!empty($selected_form)){
        $form_field = $cf7anyapi_object->Cf7_To_Any_Api_default_form_field($selected_form);
        if($form_field['status'] == 404){
            ?>
                <div id="cf7anyapi_admin" class="cf7anyapi_wrap">
                    <p>Your Selected Contact Form was not found Please try to add new data in this API</p>
                </div>
            <?php
            $selected_form = '';
            $cf7anyapi_base_url = '';
            $cf7anyapi_basic_auth = '';
            $cf7anyapi_bearer_auth = '';
            $cf7anyapi_input_type = '';
            $cf7anyapi_method = '';
            $cf7anyapi_form_field = '';
        }
    }
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="cf7anyapi_admin" class="cf7anyapi_wrap">

    <div class="cf7anyapi_full_width">
        <p>IN JSON format we are support below format</p>
        <code>
            {"Your API Key":"Your Form Field Data","Your API Key":"Your Form Field Data"}
        </code>
        <code>
            {"name":"your-name", "email":"your-email"}
        </code>

        <p>S3 Bucket region: <code>"us-east-1"</code></p>
    </div>

    <div class="cf7anyapi_field">
        <?php wp_nonce_field('cf7_to_any_api_cpt_nonce','cf7_to_any_api_cpt_nonce' ); ?>
        <label for="cf7anyapi_selected_form">Select Contact Form</label>
        <select name="cf7anyapi_selected_form" id="cf7anyapi_selected_form"> 
            <option value="">Select Form</option>
            <?php
                $posts = get_posts(
                    array(
                        'post_type'     => 'wpcf7_contact_form',
                        'numberposts'   => -1
                    )
                );
                foreach($posts as $post){
                    ?>
                    <option value="<?php echo esc_html($post->ID); ?>" <?php echo ($post->ID == $selected_form ? esc_html('selected="selected"') : ''); ?> ><?php echo esc_html($post->post_title.'('.$post->ID.')'); ?> </option>
                    <?php
                }
            ?>
        </select>
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_base_url">Base URL / S3 Bucket (<code>/example-path</code>)</label>
        <input type="text" id="cf7anyapi_base_url" name="cf7anyapi_base_url" value="<?php echo esc_url($cf7anyapi_base_url); ?>" placeholder="Enter Your API URL">
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_basic_auth">Basic auth / AWS Aaccess Key</label>
        <input type="password" id="cf7anyapi_basic_auth" name="cf7anyapi_basic_auth" value="<?php echo esc_html($cf7anyapi_basic_auth); ?>" placeholder="e.g. user:secret">
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_bearer_auth">Bearer auth key / AWS Secret Aaccess Key</label>
        <input type="password" id="cf7anyapi_bearer_auth" name="cf7anyapi_bearer_auth" value="<?php echo esc_html($cf7anyapi_bearer_auth); ?>" placeholder="e.g. a94a8fe5ccb19ba61c4c0873d391e987982fbbd3">
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_input_type">Input type (For S3 choice txt)</label>
        <select id="cf7anyapi_input_type" name="cf7anyapi_input_type">
            <option value="params" <?php echo ($cf7anyapi_input_type == 'params' ? esc_html('selected="selected"') : ''); ?>>Parameters - GET/POST</option>
            <option value="json" <?php echo ($cf7anyapi_input_type == 'json' ? esc_html('selected="selected"') : ''); ?>>json</option>
            <option value="s3" <?php echo ($cf7anyapi_input_type == 's3' ? esc_html('selected="selected"') : ''); ?>>txt (S3 Bucket)</option>
        </select>
    </div>

    <div class="cf7anyapi_field">
        <label for="cf7anyapi_method">Method</label>
        <select id="cf7anyapi_method" name="cf7anyapi_method">
            <option value="">Select Method</option>
            <option value="GET" <?php echo ($cf7anyapi_method == 'GET' ? esc_html('selected="selected"') : ''); ?>>GET</option>
            <option value="POST" <?php echo ($cf7anyapi_method == 'POST' ? esc_html('selected="selected"') : ''); ?>>POST</option>
        </select>
    </div>

    <div id="cf7anyapi-form-fields" class="form-fields">
        <?php
            if($cf7anyapi_form_field){
                foreach($cf7anyapi_form_field as $cf7anyapi_form_field_key => $cf7anyapi_form_field_value){
        ?>
                    <div class="cf7anyapi_field">
                        <label for="cf7anyapi_<?php echo esc_html($cf7anyapi_form_field_key); ?>"><?php echo esc_html($cf7anyapi_form_field_key); ?></label>
                        <input type="text" id="cf7anyapi_<?php echo esc_html($cf7anyapi_form_field_key); ?>" name="cf7anyapi_form_field[<?php echo esc_html($cf7anyapi_form_field_key); ?>]" value="<?php echo esc_html($cf7anyapi_form_field_value); ?>" placeholder="Enter Mapping Key Field Name"> 
                    </div>
        <?php
                }
            }
        ?>
    </div>
</div>
<?php } ?>