<?php
/*
Plugin Name: Fitness Club
Plugin URI: http://ultimatemember.com/
Description: The easiest way to create powerful online communities and beautiful user profiles with WordPress
Version: 2.0.25
Author: Fitness Club
Author URI: http://ultimatemember.com/
Text Domain: fitness-club
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$plugin_data = get_plugin_data( __FILE__ );

/**
 * Textdomain constant backward compatibility will be removed in future releases
 *
 * @todo remove in future releases
 */
define( 'UM_TEXTDOMAIN', 'fitness-club' );

define( 'um_url', plugin_dir_url( __FILE__ ) );
define( 'um_path', plugin_dir_path( __FILE__ ) );
define( 'um_plugin', plugin_basename( __FILE__ ) );
define( 'ultimatemember_version', $plugin_data['Version'] );
define( 'ultimatemember_plugin_name', $plugin_data['Name'] );

require_once 'includes/class-functions.php';
require_once 'includes/class-init.php';
require "includes/class-table.php";
add_action('plugins_loaded', 'func_table_tracker');