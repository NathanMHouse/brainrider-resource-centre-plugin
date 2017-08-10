<?php
/*
Plugin Name: Brainrider Resource Centre
Plugin URI: 
Description: The BR RC plugin is a turnkey resource centre solution that incorporates a number of standard features and industry best practices.
Version: 0.1
Author: Nathan House
Author URI: 
Text Domain: brainrider-resource-centre
Copyright: Brainrider
License: 
*/

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Define Plugin Path Constant
?.? Include Necessary Files
?.? Run Plugin Setup
?.? Run Plugin Activation
?.? Run Plugin Dectivation
*/


/**
 * ?.? Define Plugin Path Constant
 *
 * Define global constant for plugin path.
 *
**/
define( 'BR_RC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * ?.? Include Necessary Files
 *
 * Includes necessary function files
 *
**/
include plugin_dir_path( __FILE__ ) . 'includes/back-end-functions.php';
include plugin_dir_path( __FILE__ ) . 'includes/front-end-functions.php';
include plugin_dir_path( __FILE__ ) . 'includes/template-functions.php';
include plugin_dir_path( __FILE__ ) . 'includes/pardot-functions.php';


/**
 * ?.? Run Plugin Setup
 *
 * Initial setup of for br resource centre plugin. Includes setup of
 * post type, custom archive/single templates etc.
 *
*/
add_action( 'plugins_loaded', 'br_rc_setup', 0 );
function br_rc_setup() {

	// Execute function hook
	do_action( 'br_rc_setup_action' );

	// Register br resource post type
	add_action( 'init', 'br_rc_register_post_type' );

	// Register custom taxonomies
	add_action( 'init', 'br_rc_register_taxonomies', 0 );

	// Register daily Pardot sync
	add_action( 'init', 'br_rc_register_pardot_daily_sync' );

	// Add plugin stylsheet to TinyMCE Editor
	add_action( 'init', 'br_rc_add_editor_styles' );

	// Register new WYSIWYG buttons
	add_action( 'admin_init', 'br_rc_custom_buttons' );

	// Register and define settings
	add_action( 'admin_init', 'br_rc_settings_init' );

	// Add settings page
	add_action( 'admin_menu', 'br_rc_create_settings_page' );

	// Remove extraneous admin metaboxes
	add_action( 'admin_menu', 'br_rc_metabox_removal' );

	// Create additional metaboxes (and save values)
	add_action( 'add_meta_boxes', 'br_rc_metabox_creation' );
	add_action( 'save_post', 'br_rc_save_meta', 999 );

	// Enqueue br resource stylesheets/scripts
	add_action( 'wp_enqueue_scripts', 'br_rc_enqueue' );
	add_action( 'admin_enqueue_scripts', 'br_rc_enqueue' );

	// Set up br resource archive page
	add_filter( 'archive_template', 'br_rc_archive_template' );

	// Set up br resource single page
	add_filter( 'single_template', 'br_rc_single_template' );

	// Start session 
	add_action('init', 'br_rc_start_session', 1);
}


/**
 * ?.? Plugin Activation
 *
 * Functions to run on initial activation of plugin.
 * 
 *
**/

// Flush the permalinks
register_activation_hook( __FILE__, 'br_rc_flush_rewrite' );

// Set default options
register_activation_hook( __FILE__, 'br_rc_set_default_options' );



/**
 * ?.? Plugin Dectivation
 *
 * Functions to run on deactivation of plugin.
 * 
 *
**/

// Flush the permalinks
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

// Remove options (disabled)
//register_deactivation_hook( __FILE__, 'br_rc_remove_options' );