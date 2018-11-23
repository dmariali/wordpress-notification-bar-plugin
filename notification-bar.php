<?php

/*
Plugin Name: Notification bar
Plugin URI:
Description: Add a notification bar to the top of websites
Version: 1.0
Author: DMariAli
Author URI: https://www.dmariali.com
License: GPL v3.0+
Text Domain: notification-bar
*/



//admin_menu is all the tabs on the backend of wordpress site
add_action('admin_menu', 'snb_general_settings_page' );
function snb_general_settings_page(){

    add_submenu_page(
      // options-general.php refers to the settings tab in the wordpress dashboard
        'options-general.php',
        // the double underscore allows for it to be translated into different languages
        __('Notification Bar', 'notification-bar'),
        // name that will be displayed under settings menu - Notifications
        __('Notifications', 'notification-bar'),
        // someone who is allowed to manage options on the site is allowed to view this page
        'manage_options',
        //custom slug for menu page - unique identifier
        'snb_notifications_bar',
        // function that we are going to add code to that will display that page
        'snb_render_settings_page'
    );

}

//Create the settings page
function snb_render_settings_page(){
  #End block of php code and start writing HTML code, then reopen a php block later on
   ?>
    <div class="wrap">
      <h2> <?php _e( 'Notification Bar Settings', 'notification-bar'); ?> </h2>

      <form action="options.php" method="POST">
          <?php
            //Get settings for the plugin to display in the form
            settings_fields( 'snb_general_settings' );
            do_settings_sections( 'snb_general_settings' );

            //Form submit button
            submit_button();
           ?>

      </form>

    </div>
    <?php

}

//Create settings for the Plugin
add_action('admin_init', 'snb_initialize_settings');
function snb_initialize_settings(){
  add_settings_section(
      'general_section',
      __('General Settings', 'notification-bar'),
      'general_settings_callback',
      'snb_general_settings'
    );

    add_settings_field(
      'notification_text',
      __('Notification Text', 'notification-bar'),
      'text_input_callback',
      'snb_general_settings',
      'general_section',
      array(
            'label_for' =>'notification_text',
            'option_group' => 'snb_general_settings',
            'option_id' => 'notification_text'
            )
      );

      add_settings_field(
        'display_location',
        __('Where will the notification bar be displayed?','notification-bar'),
        'radio_input_callback',
        'snb_general_settings',
        'general_section',
        array(
              'label_for' => 'display_location',
              'option_group' => 'snb_general_settings',
              'option_id' => 'display_location',
              'option_description' => 'Display notification bar on the bottom of the site',
              'radio_options' => array(
                                      'display_none' => 'Do not display notification bar',
                                      'display_top' => 'Display notification bar on the top of the site',
                                      'display_bottom' => 'Display notification bar on the bottom of the site'
                                      )
              )
        );

        register_setting(
            'snb_general_settings',
            'snb_general_settings'
          );
}

//Display the header of the general settings
function general_settings_callback(){
  _e ('Notification bar settings made by DMariAli', 'notification-bar');
}

//Text Input Callback
function text_input_callback($text_input){
    //Get arguments from settings
    $option_group = $text_input['option_group'];
    $option_id = $text_input['option_id'];
    $option_name = "{$option_group}[{$option_id}]";

    //Get existing option from Database
    $options = get_option ($option_group);
    $option_value = isset ( $options[$option_id]) ? $options[$option_id]: "";

    //Render the Output
    echo "<input type='text' size='50' id='{$option_id}' name='{$option_name}' value='{$option_value}' />";
}

//Radio button input call back
function radio_input_callback($radio_input){
    //Get arguments from settings
    $option_group = $radio_input['option_group'];
    $option_id = $radio_input['option_id'];
    $radio_options = $radio_input['radio_options'];
    $option_name = "{$option_group}[{$option_id}]";

    //Get existing option from database
    $options = get_option( $option_group );
    $option_value = isset ( $options[$option_id]) ? $options[$option_id]: "";

    //Render the Output
    $input = '';
    foreach ($radio_options as $radio_option_id => $radio_option_value){
        $input .= "<input type='radio' id='{$radio_option_id}' name = '{$option_name}' value = '{$radio_option_id}' />";
        $input .= "<label for='{$radio_option_id}'>{$radio_option_value}</label> <br/>";
    }

    echo $input;
}

//Display the notification bar on the frontend of the site
add_action('wp_footer', 'snb_display_notification_bar');
function snb_display_notification_bar(){

      ?>
      <div class='snb_notification_bar <?php echo get_theme_mod('display_location'); ?>' style='background-color: <?php echo get_theme_mod('notification_bar_color'); ?>;'>
        <div class="snb_notification_text" style='color:<?php echo get_theme_mod('notification_text_color'); ?>;'> <?php echo get_theme_mod('notification_text'); ?></div>
      </div>
      <?php
}

//Load plugin scripts and styles
add_action('wp_enqueue_scripts', 'snb_scripts');
function snb_scripts(){

    wp_enqueue_style(
      'notification-bar-css', //unique identifier for this script
      plugin_dir_url(__FILE__) . 'notification-bar.css', //tells it where to find this script
      array(), //array of dependencies - none right now
      '1.0.0'  //Version number
      );

}

add_filter('body_class', 'snb_body_class', 20);
function snb_body_class($classes){

        if(get_theme_mod('display_location')==='display_top' || get_theme_mod('display_location') === 'display_bottom'){
            $classes[] = 'notification-bar';
        }

    return $classes;
}

add_action('customize_register', 'snb_customize_register');
function snb_customize_register(WP_Customize_Manager $wp_customize){

    $wp_customize->add_section ('skillshare_notification_bar', array(
      'title'=> __('Notification Bar','notification-bar'),
    ));

    $wp_customize->add_setting('display_location', array(
      'capability' => 'edit_theme_options',
      'default' => 'display_none'
    ));

    $wp_customize-> add_control('display_location',array(
      'type' => 'radio',
      'section' => 'skillshare_notification_bar',  //Add a default or your own section
      'label' => __('Display Location'),
      'description' => __('Choose where the notification bar is displayed'),
      'choices' => array(
          'display_none' => __('Do not display the notification bar', 'notification-bar'),
          'display_top' => __('Display the notification bar at the top of the site', 'notification-bar'),
          'display_bottom' => __('Display the notification bar at the bottom of the site', 'notification-bar')
      ),
    ));

    $wp_customize-> add_setting('notification_text', array(
        'capability' => 'edit_theme_options',
        'default'=> ''
    ));

    $wp_customize -> add_control('notification_text', array(
        'type' => 'textarea',
        'section' => 'skillshare_notification_bar',
        'label' => __('Notification Text'),
        'description' => __('This is the text of your notification'),
    ));

    $wp_customize -> add_setting('notification_bar_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability' => 'edit_theme_options',
    ));

    $wp_customize -> add_control(new WP_Customize_Color_Control($wp_customize, 'notification_bar_color', array(
      'label' => __('Notification Bar Color', 'notification-bar'),
      'section' => 'skillshare_notification_bar',
      'settings' => 'notification_bar_color',
    )));

    $wp_customize -> add_setting('notification_text_color', array(
        'default' => '#FFFFFF',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability' => 'edit_theme_options',
    ));

    $wp_customize-> add_control(new WP_Customize_Color_Control($wp_customize, 'notification_text_color', array(
        'label' => __('Notification Text Color', 'notification-bar'),
        'section' => 'skillshare_notification_bar',
        'settings' => 'notification_text_color',
    )));
}

 ?>
