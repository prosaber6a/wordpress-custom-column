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
	$columns['id']         = __( 'Post ID', 'custom-column' );
	$columns['thumbnail']  = __( 'Thumbnail', 'custom-column' );
	$columns['word_count'] = __( 'Word Count', 'custom-column' );

	return $columns;
}

add_filter( 'manage_posts_columns', 'custom_column_post_columns' );

// New column value
function custom_column_post_column_data( $columns, $post_id ) {
	if ( 'id' == $columns ) {
		echo $post_id;
	} elseif ( 'thumbnail' == $columns ) {
		$thumbnail = get_the_post_thumbnail( $post_id, array( 50, 50 ) );
		echo $thumbnail;
	} elseif ( 'word_count' == $columns ) {
//		$_post    = get_post( $post_id );
//		$content  = $_post->post_content;
//		$word_num = str_word_count( strip_tags( $content ) );
		$word_num = get_post_meta( $post_id, 'word_num', true );
		echo $word_num;
	}

}

add_action( 'manage_posts_custom_column', 'custom_column_post_column_data', 10, 2 );

// add custom column in pages
//add_filter( 'manage_pages_columns', 'custom_column_post_columns' );
//add_action( 'manage_pages_custom_column', 'custom_column_post_column_data', 10, 2 );


/*
function custom_column_set_word_count_meta() {
	$_posts = get_posts( array(
		'posts_per_page' => - 1,
		'post_type'      => 'post'
	) );
	foreach ($_posts as $p) {
		$content  = $p->post_content;
		$word_num = str_word_count( strip_tags( $content ) );
		update_post_meta($p->ID, 'word_num', $word_num);
	}
}

add_action( 'init', 'custom_column_set_word_count_meta' );
*/
function custom_column_word_count_meta_save( $post_id ) {
	$p        = get_post( $post_id );
	$content  = $p->post_content;
	$word_num = str_word_count( strip_tags( $content ) );
	update_post_meta( $p->ID, 'word_num', $word_num );

}

add_action( 'save_post', 'custom_column_word_count_meta_save' );


function custom_column_sortable_column( $columns ) {
	$columns['word_count'] = 'wordNum';

	return $columns;
}

add_action( 'manage_edit-post_sortable_columns', 'custom_column_sortable_column' );

function custom_column_sort_column_data( $wpquery ) {
	if ( ! is_admin() ) {
		return;
	}
	$orderby = $wpquery->get( 'orderby' );
	if ( 'wordNum' == $orderby ) {
		$wpquery->set( 'meta_key', 'word_num' );
		$wpquery->set( 'orderby', 'meta_value_num' );
	}
}

add_action( 'pre_get_posts', 'custom_column_sort_column_data' );

// Filter

function custom_column_filter() {
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {
		return;
	}

	$values_arr = array(
		'0' => __( 'Select a option', 'custom-column' ),
		'1' => __( 'Some Posts', 'custom-column' ),
		'2' => __( 'Some Posts++', 'custom-column' ),
	);

	$filter_value = isset( $_GET['demo_filter'] ) ? $_GET['demo_filter'] : '0';

	$value = " ";
	foreach ( $values_arr as $key => $value ) {
		$selected = "";
		if ( $key == $filter_value ) {
			$selected = "selected";
		}

		$value .= sprintf( '<option %s value="%s">%s</option>', $selected, $key, $value );

	}
	?>
    <select name="demo_filter">
		<?php
		foreach ( $values_arr as $key => $value ) {
			$selected = "";
			if ( $key == $filter_value ) {
				$selected = "selected";
			}

			printf( '<option %s value="%s">%s</option>', $selected, $key, $value );

		}

		?>
    </select>
	<?php


}

add_action( 'restrict_manage_posts', 'custom_column_filter' );

function custom_column_filter_data( $wpquery ) {
	if ( ! is_admin() ) {
		return;
	}
	$filter_value = isset( $_GET['demo_filter'] ) ? $_GET['demo_filter'] : '0';
	if ( '1' == $filter_value ) {
		$wpquery->set( 'post__in', array( 1, 37, 41 ) );
	} elseif ( '2' == $filter_value ) {
		$wpquery->set( 'post__in', array( 87, 165, 91 ) );
	}
}

add_action( 'pre_get_posts', 'custom_column_filter_data' );


function custom_column_thumbnail_filter() {
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != 'post' ) {
		return;
	}

	$values_arr = array(
		'0' => __( 'Select a option', 'custom-column' ),
		'1' => __( 'Has Thumbnail', 'custom-column' ),
		'2' => __( 'No Thumbnail', 'custom-column' ),
	);

	$filter_value = isset( $_GET['thumb_filter'] ) ? $_GET['thumb_filter'] : '0';

	$value = " ";
	foreach ( $values_arr as $key => $value ) {
		$selected = "";
		if ( $key == $filter_value ) {
			$selected = "selected";
		}

		$value .= sprintf( '<option %s value="%s">%s</option>', $selected, $key, $value );

	}
	?>
    <select name="thumb_filter">
		<?php
		foreach ( $values_arr as $key => $value ) {
			$selected = "";
			if ( $key == $filter_value ) {
				$selected = "selected";
			}

			printf( '<option %s value="%s">%s</option>', $selected, $key, $value );

		}

		?>
    </select>
	<?php

}


add_action( 'restrict_manage_posts', 'custom_column_thumbnail_filter' );

function custom_column_thumbnail_filter_data( $wpquery ) {
	if ( ! is_admin() ) {
		return;
	}
	$filter_value = isset( $_GET['thumb_filter'] ) ? $_GET['thumb_filter'] : '';
	if ( '1' == $filter_value ) {
		$wpquery->set( 'meta_query', array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS'
			)
		) );
	} elseif ( '2' == $filter_value ) {
		$wpquery->set( 'meta_query', array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS'
			)
		) );
	}
}

add_action( 'pre_get_posts', 'custom_column_thumbnail_filter_data' );

