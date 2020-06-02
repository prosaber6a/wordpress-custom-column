<?php

/**
 * Plugin Name: Custom Column
 * Plugin URI: http://saberhr.com
 * Author: SaberHR
 * Author URI: http://saberhr.com
 * Description: WordPress Custom Column API
 * Licence: GPLv2 or Later
 * Text Domain: custom-column
 * Domain Path: /languages/
 */


function custom_column_load_textdomain() {
	load_plugin_textdomain( 'custom-column', false, plugin_dir_url( __FILE__ ) . '/languages' );
}

add_action( 'plugins_loaded', 'custom_column_load_textdomain' );

function custom_column_post_columns( $columns ) {
	// remove column form all post list
	unset( $columns['tags'] );
	unset( $columns['comments'] );

	// change column order
	unset( $columns['author'] );
	unset( $columns['date'] );

	$columns['author'] = "Author";
	$columns['date']   = "Date";

	// Add new column
	$columns['id']        = __( 'Post ID', 'custom-column' );
	$columns['thumbnail'] = __( 'Thumbnail', 'custom-column' );

	return $columns;
}

add_filter( 'manage_posts_columns', 'custom_column_post_columns' );

// New column value
function custom_column_post_column_data( $columns, $post_id ) {
	if ( 'id' == $columns ) {
		echo $post_id;
	} elseif ('thumbnail' == $columns) {
		$thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
		echo $thumbnail;
	}

}

add_action( 'manage_posts_custom_column', 'custom_column_post_column_data', 10, 2 );
