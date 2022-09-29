<?php
/**
* Plugin Name: WP Breaking News 
* Plugin URI: https://www.pankajwb.com/
* Description: This is a custom plugin built for screening process in Toptal. This plugin provides feature to set indiviual post as Breaking News 
* Version: 1.0
* Author: Pankaj Vashist
* Author URI: http://github.com/pankajwb
**/

/*
* Include classes 
*/
if(is_admin()){
	// Include metabox class file
	include( plugin_dir_path( __FILE__ ) .'bn_metabox_class.php');
	new BnMetaboxClass();	
	// Admin settings page class
	include( plugin_dir_path( __FILE__ ) .'bn_admin_class.php');
	new BnAdminClass(); 
}

// Frontend shortcode class
include( plugin_dir_path( __FILE__ ) .'bn_shortcode_class.php');
new BnShortcodeClass();


?>