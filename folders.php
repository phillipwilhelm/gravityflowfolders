<?php
/*
Plugin Name: Gravity Flow Folders
Plugin URI: http://gravityflow.io
Description: Folders Extension for Gravity Flow.
Version: 1.0-beta-1
Author: Steve Henty
Author URI: http://gravityflow.io
License: GPL-3.0+

------------------------------------------------------------------------
Copyright 2015-2016 Steven Henty

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'GRAVITY_FLOW_FOLDERS_VERSION', '1.0-beta-1' );
define( 'GRAVITY_FLOW_FOLDERS_EDD_ITEM_NAME', 'Folders Beta' );

add_action( 'gravityflow_loaded', array( 'Gravity_Flow_Folders_Bootstrap', 'load' ), 1 );

class Gravity_Flow_Folders_Bootstrap {

	public static function load() {
		require_once( 'includes/class-folder.php' );
		require_once( 'includes/class-folder-checklist.php' );
		require_once( 'includes/class-folder-list.php' );

		require_once( 'includes/class-step-folders.php' );
		Gravity_Flow_Steps::register( new Gravity_Flow_Step_Folders() );

		require_once( 'class-folders.php' );

		gravity_flow_folders();
	}
}

function gravity_flow_folders() {
	if ( class_exists( 'Gravity_Flow_Folders' ) ) {
		return Gravity_Flow_Folders::get_instance();
	}
}


add_action( 'admin_init', 'gravityflow_folders_edd_plugin_updater', 0 );

function gravityflow_folders_edd_plugin_updater() {

	if ( ! function_exists( 'gravity_flow_folders' ) ) {
		return;
	}

	$gravity_flow_folders = gravity_flow_folders();
	if ( $gravity_flow_folders ) {
		$settings = $gravity_flow_folders->get_app_settings();

		$license_key = trim( rgar( $settings, 'license_key' ) );

		$edd_updater = new Gravity_Flow_EDD_SL_Plugin_Updater( GRAVITY_FLOW_EDD_STORE_URL, __FILE__, array(
			'version'   => GRAVITY_FLOW_FOLDERS_VERSION,
			'license'   => $license_key,
			'item_name' => GRAVITY_FLOW_FOLDERS_EDD_ITEM_NAME,
			'author'    => 'Steven Henty',
		) );
	}

}
