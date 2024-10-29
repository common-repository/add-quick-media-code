<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

global $wpdb;

// For Single site
if ( !is_multisite() ) 
{
	foreach ( addquickmediacode_uninstall_option_names() as $option_name ) {
	    delete_option( $option_name );
	}
	// Delete log database
	$log_name = $wpdb->prefix.'addquickmediacode_log';
	$wpdb->query("DROP TABLE IF EXISTS $log_name");
} 
// For Multisite
else 
{
    // For regular options.
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
		foreach ( addquickmediacode_uninstall_option_names() as $option_name ) {
    	    delete_option( $option_name );
		}
		// Delete log database
		$log_name = $wpdb->prefix.'addquickmediacode_log';
		$wpdb->query("DROP TABLE IF EXISTS $log_name");
    }
    switch_to_blog( $original_blog_id );

    // For site options.
	foreach ( addquickmediacode_uninstall_option_names() as $option_name ) {
		delete_site_option( $option_name );
	}
}


function addquickmediacode_uninstall_option_names() {

	global $wpdb;
	$option_names = array();
	$wp_options = $wpdb->get_results("
					SELECT option_name
					FROM $wpdb->options
					WHERE option_name LIKE '%%addquickmediacode%%'
					");
	foreach ( $wp_options as $wp_option ) {
		$option_names[] = $wp_option->option_name;
	}

	return $option_names;

}

?>