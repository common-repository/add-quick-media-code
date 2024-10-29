<?php
/**
 * Add Quick Media Code
 * 
 * @package    AddQuickMediaCode
 * @subpackage AddQuickMediaCode
    Copyright (c) 2016- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$addquickmediacode = new AddQuickMediaCode();

class AddQuickMediaCode {

	/* ==================================================
	 * Construct
	 * @since	1.08
	 */
	public function __construct() {

		add_action('media_buttons', array($this, 'add_quickcode_select'));
		add_action('admin_print_footer_scripts', array($this, 'add_quickcode_button_js'));

	}

	/* ==================================================
	 * For quick code
	 * @since	1.0
	 */
	public function add_quickcode_select(){

		global $wpdb;

		$table_name = $wpdb->prefix.'addquickmediacode_log';
		$records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY meta_id DESC");

$quickcode_add_select = <<<QUICKCODEADDSELECT1
<select id="addquickmediacode_select">
	<option value="">Add Quick Media Code</option>
QUICKCODEADDSELECT1;

		foreach ( $records as $record ) {
			$quickcode_add_select .= '<option title="'.$record->user.'" value="'.stripslashes(esc_html($record->code)).' ">'.$record->description.'</option>';
		}

$quickcode_add_select .= <<<QUICKCODEADDSELECT2
</select>
QUICKCODEADDSELECT2;
		echo $quickcode_add_select;

	}

	/* ==================================================
	 * For quick code
	 * @since	1.0
	 */
	public function add_quickcode_button_js() {
		if ($this->is_my_plugin_screen()) {

$quickcode_add_js = <<<QUICKCODEADDJS

<!-- BEGIN: AddQuickMediaCode -->
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#addquickmediacode_select").change(function() {
			send_to_editor(jQuery("#addquickmediacode_select :selected").val());
			return false;
		});
	});
</script>
<!-- END: AddQuickMediaCode -->

QUICKCODEADDJS;

			echo $quickcode_add_js;
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	1.0
	 */
	private function is_my_plugin_screen() {
		$screen = get_current_screen();
		if ( $screen->post_type === 'post' || $screen->post_type === 'page' ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

?>