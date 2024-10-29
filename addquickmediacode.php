<?php
/*
Plugin Name: Add Quick Media Code
Plugin URI: https://wordpress.org/plugins/add-quick-media-code/
Version: 1.09
Description: This plugin makes it easy to add Media Code to the html-editor.
Author: Katsushi Kawamori
Author URI: https://riverforest-wp.info/
Text Domain: add-quick-media-code
Domain Path: /languages
*/

/*  Copyright (c) 2016- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	add_action( 'plugins_loaded', 'add_quick_media_code_load_textdomain' );
	function add_quick_media_code_load_textdomain() {
		load_plugin_textdomain('add-quick-media-code');
	}

	if(!class_exists('AddQuickMediaCodeRegist')) require_once( dirname(__FILE__).'/lib/AddQuickMediaCodeRegist.php' );
	if(!class_exists('AddQuickMediaCodeAdmin')) require_once( dirname(__FILE__).'/lib/AddQuickMediaCodeAdmin.php' );
	if(!class_exists('AddQuickMediaCode')) require_once( dirname(__FILE__).'/lib/AddQuickMediaCode.php' );

?>