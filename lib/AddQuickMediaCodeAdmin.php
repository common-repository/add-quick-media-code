<?php
/**
 * Add Quick Media Code
 * 
 * @package    AddQuickMediaCode
 * @subpackage AddQuickMediaCodeAdmin Management screen
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

$addquickmediacodeadmin = new AddQuickMediaCodeAdmin();

class AddQuickMediaCodeAdmin {

	/* ==================================================
	 * Construct
	 * @since	1.08
	 */
	public function __construct() {

		add_filter( 'plugin_action_links', array($this, 'settings_link'), 10, 2 );
		add_action( 'admin_menu', array($this, 'plugin_menu') );
		add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );

	}

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	public function settings_link($links, $file) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = 'add-quick-media-code/addquickmediacode.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=AddQuickMediaCode').'">'.__( 'Settings').'</a>';
		}
		return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	public function plugin_menu() {
		add_options_page( 'AddQuickMediaCode Options', 'Add Quick Media Code', 'upload_files', 'AddQuickMediaCode', array($this, 'plugin_options') );
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	1.0
	 */
	public function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen()) {
			wp_enqueue_style( 'jquery-responsiveTabs', plugin_dir_url( __DIR__ ).'css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', plugin_dir_url( __DIR__ ).'css/style.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-responsiveTabs', plugin_dir_url( __DIR__ ).'js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'addquickmediacode-js', plugin_dir_url( __DIR__ ).'js/jquery.addquickmediacode.js', array('jquery') );
			wp_enqueue_media();
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	1.0
	 */
	private function is_my_plugin_screen() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'settings_page_AddQuickMediaCode') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	public function plugin_options() {

		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( isset($_POST['aqmc_add_code']) && $_POST['aqmc_add_code'] ) {
			if ( check_admin_referer( 'add_code', 'aqmc_add_code' ) ) {
				if( !empty($_POST['addquickmediacode_register']) && intval($_POST['addquickmediacode_register']) == 1 ) {
					$this->db_append();
				}
			}
		}
		if ( isset($_POST['aqmc_delete_code']) && $_POST['aqmc_delete_code'] ) {
			if ( check_admin_referer( 'delete_code', 'aqmc_delete_code' ) ) {
				if( !empty($_POST['addquickmediacode_delete']) && intval($_POST['addquickmediacode_delete']) == 1 ) {
					$this->db_delete();
				}
			}
		}

		$scriptname = admin_url('options-general.php?page=AddQuickMediaCode');

		$users = wp_get_current_user();
		$user = $users->display_name;

		?>
		<div class="wrap">
			<h2>Add Quick Media Code</h2>
			<div id="addquickmediacode-tabs">
				<ul>
				<li><a href="#addquickmediacode-tabs-1"><?php _e('Settings'); ?></a></li>
				<li><a href="#addquickmediacode-tabs-2"><?php _e('Donate to this plugin &#187;'); ?></a></li>
				<!--
				<li><a href="#addquickmediacode-tabs-3">FAQ</a></li>
				 -->
				</ul>
				<div id="addquickmediacode-tabs-1">
				<div class="wrap">
					<h3><?php _e('Registration of the code', 'add-quick-media-code'); ?></h3>
					<form method="post" action="<?php echo $scriptname; ?>" />
					<?php wp_nonce_field('add_code', 'aqmc_add_code'); ?>
					<div><?php _e('Description'); ?><input type="text" name="description"></div>
					<div><button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="content"><span class="dashicons dashicons-admin-media"></span> <?php _e('Add Media Code', 'add-quick-media-code'); ?></button>
					<textarea class="wp-editor-area" style="width: 100%" autocomplete="off" cols="40" name="content" id="content"></textarea></div>
					<input type="hidden" name="addquickmediacode_register" value="1" />
					<div style="padding: 10px;">
					<input type="submit" class="button-primary button-large" value="<?php _e('Register'); ?>" />
					</div>
					</form>

					<div style="border-bottom: 3px solid; padding-top: 5px; padding-bottom: 5px;"></div>

 					<?php
					global $wpdb;
					$table_name = $wpdb->prefix.'addquickmediacode_log';
					$records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY meta_id DESC");
					?>
					<h3><?php _e('Registered code', 'add-quick-media-code'); ?></h3>
					<?php
					if (!empty($records)) {
						?>
						<form method="post" action="<?php echo $scriptname; ?>" />
						<?php wp_nonce_field('delete_code', 'aqmc_delete_code'); ?>
						<input type="checkbox" id="group_addquickmediacode" class="addquickmediacode-checkAll"><?php _e('Select all'); ?>
						<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;"></div>
						<?php
						foreach ( $records as $record ) {
							?>
							<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
								<?php
								if ( $record->user === $user || current_user_can('manage_options') ) {
									?>
									<input name="addquickmediacode-deletes[]" value="<?php echo $record->meta_id; ?>" type="checkbox" class="group_addquickmediacode" style="float: left; margin: 5px;">
									<?php
								} else {
									?>
									<input type="checkbox" disabled="disabled" style="float: left; margin: 5px;">
									<?php
								}
								?>
								<div style="font-weight: bold;">
								<?php echo __('Description').': '.$record->description; ?>
								<?php echo sprintf(__('Username: %s'), $record->user); ?>
								</div>
								<div style="overflow: hidden;">
								<div><?php echo do_shortcode(stripslashes(htmlspecialchars_decode($record->code))); ?></div>
								</div>
							</div>
							<?php
						}
						?>
						<input type="hidden" name="addquickmediacode_delete" value="1" />
						<div style="padding: 10px;">
						<input type="submit" class="button-primary button-large" value="<?php _e('Delete Selected'); ?>" />
						</div>
						</form>
					<?php
					} else{
					?>
					<div style="padding-top: 5px; padding-bottom: 5px;">
					<?php _e('No items'); ?>
					</div>
					<?php
					}
					?>
			  	</div>
		  		</div>

				<div id="addquickmediacode-tabs-2">
				<div class="wrap">
				<?php $this->credit(); ?>
				</div>
				</div>

				<!--
				<div id="addquickmediacode-tabs-3">
					<div class="wrap">
					<h2>FAQ</h2>
					</div>
				</div>
				-->

			</div>
		</div>
		<?php
	}

	/* ==================================================
	 * Credit
	 */
	private function credit() {

		$plugin_name = NULL;
		$plugin_ver_num = NULL;
		$plugin_path = plugin_dir_path( __DIR__ );
		$plugin_dir = untrailingslashit($plugin_path);
		$slugs = explode('/', $plugin_dir);
		$slug = end($slugs);
		$files = scandir($plugin_dir);
		foreach ($files as $file) {
			if($file == '.' || $file == '..' || is_dir($plugin_path.$file)){
				continue;
			} else {
				$exts = explode('.', $file);
				$ext = strtolower(end($exts));
				if ( $ext === 'php' ) {
					$plugin_datas = get_file_data( $plugin_path.$file, array('name'=>'Plugin Name', 'version' => 'Version') );
					if ( array_key_exists( "name", $plugin_datas ) && !empty($plugin_datas['name']) && array_key_exists( "version", $plugin_datas ) && !empty($plugin_datas['version']) ) {
						$plugin_name = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __('Version:').' '.$plugin_ver_num;
		$faq = __('https://wordpress.org/plugins/'.$slug.'/faq', $slug);
		$support = 'https://wordpress.org/support/plugin/'.$slug;
		$review = 'https://wordpress.org/support/view/plugin-reviews/'.$slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/'.$slug;
		$facebook = 'https://www.facebook.com/katsushikawamori/';
		$twitter = 'https://twitter.com/dodesyo312';
		$youtube = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate = __('https://riverforest-wp.info/donate/', $slug);

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo $plugin_version; ?> | 
		<a style="text-decoration: none;" href="<?php echo $faq; ?>" target="_blank"><?php _e('FAQ'); ?></a> | <a style="text-decoration: none;" href="<?php echo $support; ?>" target="_blank"><?php _e('Support Forums'); ?></a> | <a style="text-decoration: none;" href="<?php echo $review; ?>" target="_blank"><?php _e('Reviews', 'media-from-ftp'); ?></a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo $translate; ?>" target="_blank"><?php echo sprintf(__('Translations for %s'), $plugin_name); ?></a> | <a style="text-decoration: none;" href="<?php echo $facebook; ?>" target="_blank"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo $twitter; ?>" target="_blank"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo $youtube; ?>" target="_blank"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', $slug); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo $donate; ?>')"><?php _e('Donate to this plugin &#187;'); ?></button>
		</div>

		<?php

	}

	/* ==================================================
	 * Append db table.
	 * @since	1.0
	 */
	private function db_append(){

		if(isset($_POST['description'])){
			if (empty($_POST['description'])){
				echo '<div class="error"><ul><li>'.__('Description is blank.', 'add-quick-media-code').'</li></ul></div>';
				return;
			}
			$description = sanitize_text_field($_POST['description']);
		} else {
			return;
		}
		if(isset($_POST['content'])){
			if (empty($_POST['content'])){
				echo '<div class="error"><ul><li>'.__('Code is blank.', 'add-quick-media-code').'</li></ul></div>';
				return;
			}
			$content = htmlspecialchars($_POST['content']);
		} else {
			return;
		}

		$users = wp_get_current_user();
		$user = $users->display_name;

		$set_arr = array(
						'user' => $user,
						'code' => $content,
						'description' => $description
						);

		global $wpdb;
		$log_name = $wpdb->prefix.'addquickmediacode_log';

		$get_description = NULL;
		$get_description = $wpdb->get_var( $wpdb->prepare("
												SELECT description FROM $log_name
												WHERE description = %s
													", $description
												));

		if ( !empty($get_description) ) {
			echo '<div class="error"><ul><li>'.__('The description is already in use.', 'add-quick-media-code').'</li></ul></div>';
		} else {
			$wpdb->insert($log_name, $set_arr);
		}
		$wpdb->show_errors();

	}

	/* ==================================================
	 * Delete db table.
	 * @since	1.0
	 */
	private function db_delete(){

		if(isset($_POST['addquickmediacode-deletes'])){
			$addquickmediacode_deletes = $this->sanitize_array($_POST['addquickmediacode-deletes']);
		} else {
			return;
		}

		global $wpdb;
		$log_name = $wpdb->prefix.'addquickmediacode_log';
		foreach ( $addquickmediacode_deletes as $addquickmediacode_delete ) {
		    $wpdb->query( $wpdb->prepare("
								DELETE FROM $log_name
								WHERE meta_id = %d
									", intval($addquickmediacode_delete)
								));
			$wpdb->show_errors();
		}

	}

	/* ==================================================
	* Sanitize Array
	* @param	array	$a
	* @return	string	$_a
	* @since	1.06
	*/
	private function sanitize_array($a) {

		$_a = array();
		foreach($a as $key=>$value) {
			if ( is_array($value) ) {
				$_a[$key] = $this->sanitize_array($value);
			} else {
				$_a[$key] = htmlspecialchars($value);
			}
		}

		return $_a;

	}

}

?>