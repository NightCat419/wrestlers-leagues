<?php
/*
 * Plugin Name: wrestlers-leagues
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wrestlers-leagues
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('WP_KWL_PLUGIN_DIR', WP_PLUGIN_DIR . '/wrestlers-leagues');

// Load plugin class files
require_once( 'includes/class-wrestlers-leagues.php' );
require_once( 'includes/class-wrestlers-leagues-settings.php' );

// Load plugin core files
require_once ('includes/core/class-um-customize.php');
require_once ('includes/core/class-oceanwp-customize.php');

// Load plugin libraries
require_once( 'includes/lib/class-wrestlers-leagues-admin-api.php' );
require_once( 'includes/lib/class-wrestlers-leagues-post-type.php' );
require_once( 'includes/lib/class-wrestlers-leagues-taxonomy.php' );

/**
 * Returns the main instance of wrestlers-leagues to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object wrestlers-leagues
 */
function wrestlers_leagues () {
	$instance = Wrestlers_Leagues::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Wrestlers_Leagues_Settings::instance( $instance );
	}

	return $instance;
}

$wl_instance = wrestlers_leagues();
$wl_instance->install();


