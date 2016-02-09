<?php
/**
 * Plugin Name: Coolascript Framework
 * Plugin URI: http://coolascript.com
 * Description: Coolascript Framework is a plugin which add a lot of developing features and libraris.
 * Version: 1.0.0
 * Author: ron_013
 * Author URI: http://coolascript.com
 * Text Domain: csframework
 * Domain Path: /locale/
 * Network: true
 * License: GPL2
 */
/*  Copyright 2015 ron_013 (email : coolascript@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
*/

defined( 'ABSPATH' ) or die( __( 'No script kiddies please!', 'coolabook' ) );

define( 'CSFRAMEWORK_VERSION', '1.0.0' );
define( 'CSFRAMEWORK_MINIMUM_PHP_VERSION', '5.3' );
define( 'CSFRAMEWORK_MINIMUM_WP_VERSION', '3.1' );
define( 'CSFRAMEWORK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CSFRAMEWORK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( version_compare( PHP_VERSION, CSFRAMEWORK_MINIMUM_PHP_VERSION, '>=' ) && version_compare( $GLOBALS['wp_version'], 'CSFRAMEWORK_MINIMUM_WP_VERSION', '>=' ) ) {
	require_once( CSFRAMEWORK_PLUGIN_DIR . 'csframework/csframework.php' );
}