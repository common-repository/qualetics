<?php
/**
 * Plugin Name:       Qualetics
 * Description:       Qualetics No Code Analytics & AI for your wordpress website.
 * Version:           1.0.2
 * Author:			  Qualetics
 * Author URI:        https://qualetics.com/
 * Plugin URI:		  https://wordpress.org/plugins/qualetics/
 * Text Domain:       rltqualetics
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort if this file is called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Plugin constants
 */

if(!defined('RLTQUALETICS_URL'))
	define('RLTQUALETICS_URL', plugin_dir_url( __FILE__ ));
if(!defined('RLTQUALETICS_PATH'))
	define('RLTQUALETICS_PATH', plugin_dir_path( __FILE__ ));
if(!defined('RLTQUALETICS_VERSION'))
	define('RLTQUALETICS_VERSION', '1.0.2');

/*
 * Import the plugin classes
 */
include_once RLTQUALETICS_PATH . '/classes/rltqualetics-public.php';
include_once RLTQUALETICS_PATH . '/classes/rltqualetics-admin.php';

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'qlts_manage_link', 10, 1 );
function qlts_manage_link( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=qualetics_setting#rltqualetics_api_settings' ) . '">API Settings</a>',
		'<a href="' . admin_url( 'admin.php?page=qualetics_setting#rltqualetics_tracking_settings' ) . '">Analytics Settings</a>',
        '<a href="' . admin_url( 'admin.php?page=qualetics_setting#rltqualetics_about_us' ) . '">Introduction</a>'
    );
	return array_merge( $links, $mylinks );
}
function set_default_qualtics_options() {
	//install default settings
	global $wp_version;
	$qualetics_settings = get_option('qualetics_settings', array());
	$application_id = isset($qualetics_settings['APPLICATION_ID']) ? $qualetics_settings['APPLICATION_ID'] : "";
	$application_secret = isset($qualetics_settings['APPLICATION_SECRET']) ? $qualetics_settings['APPLICATION_SECRET'] : "";
	$client_id = isset($qualetics_settings['CLIENT_ID']) ? $qualetics_settings['CLIENT_ID'] : "";
	$trackUserGeoLocation = isset($qualetics_settings['trackUserGeoLocation']) ? $qualetics_settings['trackUserGeoLocation'] : "false";
	$trackPageVisibilityChanges = isset($qualetics_settings['trackPageVisibilityChanges']) ? $qualetics_settings['trackPageVisibilityChanges'] : "true";
	$appVersion = isset($qualetics_settings['appVersion']) ? $qualetics_settings['appVersion'] : $wp_version;
	$disableErrorCapturing = isset($qualetics_settings['disableErrorCapturing']) ? $qualetics_settings['disableErrorCapturing'] : "false";
	$captureClicks = isset($qualetics_settings['captureClicks']) ? $qualetics_settings['captureClicks'] : "false";
	$captureTimings = isset($qualetics_settings['captureTimings']) ? $qualetics_settings['captureTimings'] : "false";
	$qualetics_settings = array();
	$qualetics_settings['APPLICATION_ID'] = $application_id;
	$qualetics_settings['APPLICATION_SECRET'] = $application_secret;
	$qualetics_settings['CLIENT_ID'] = $client_id;
	$qualetics_settings['trackPageVisibilityChanges'] = $trackPageVisibilityChanges;
	$qualetics_settings['trackUserGeoLocation'] = $trackUserGeoLocation;
	$qualetics_settings['appVersion'] = $appVersion;
	$qualetics_settings['disableErrorCapturing'] = $disableErrorCapturing;
	$qualetics_settings['captureClicks'] = $captureClicks;
	$qualetics_settings['captureTimings'] = $captureTimings;
				
	update_option( 'qualetics_settings', $qualetics_settings );
}
register_activation_hook( __FILE__ , 'set_default_qualtics_options');

load_plugin_textdomain( 'rltqualetics', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );