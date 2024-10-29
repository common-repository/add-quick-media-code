<?php
/**
 * Add Quick Media Code
 * 
 * @package    AddQuickMediaCode
 * @subpackage AddQuickMediaCodeRegist registered in the database
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

$addquickmediacoderegist = new AddQuickMediaCodeRegist();

class AddQuickMediaCodeRegist {

	/* ==================================================
	 * Construct
	 * @since	1.08
	 */
	public function __construct() {

		register_activation_hook( plugin_dir_path( __DIR__ ).'addquickmediacode.php', array($this, 'log_settings') );

	}

	/* ==================================================
	 * Settings Log Settings
	 * @since	1.0
	 */
	function log_settings(){

		if ( !is_multisite() ) {
			$this->log_write();
		} else { // For Multisite
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			$original_blog_id = get_current_blog_id();
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->log_write();
			}
			switch_to_blog( $original_blog_id );
		}

	}

	/* ==================================================
	 * Settings Log Write
	 * @since	1.08
	 */
	private function log_write(){

	    $addquickmediacode_log_db_version = '1.01';
		$installed_ver = get_option( 'addquickmediacode_log_version' );

		if( $installed_ver != $addquickmediacode_log_db_version ) {
			global $wpdb;
			$log_name = $wpdb->prefix.'addquickmediacode_log';

			$sql = "CREATE TABLE " . $log_name . " (
			meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user text,
			code text,
			description text,
			UNIQUE KEY meta_id (meta_id)
			)
			CHARACTER SET 'utf8';";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			update_option( 'addquickmediacode_log_version', $addquickmediacode_log_db_version );
		}

	}

}

?>