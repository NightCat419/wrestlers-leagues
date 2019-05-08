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

// Load plugin class files
require_once( 'includes/class-wrestlers-leagues.php' );
require_once( 'includes/class-wrestlers-leagues-settings.php' );

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
function wrestlers-leagues () {
	$instance = wrestlers-leagues::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = wrestlers-leagues_Settings::instance( $instance );
	}

	return $instance;
}

wrestlers-leagues();