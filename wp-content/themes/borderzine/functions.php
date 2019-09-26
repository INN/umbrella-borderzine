
<?php

/**
 * Include files that should be included
 */
$includes = array(
	'/inc/byline_class.php',
	'/inc/post-tags.php',
);
foreach ( $includes as $include ) {
	if ( 0 === validate_file( get_stylesheet_directory() . $include ) ) {
		require_once( get_stylesheet_directory() . $include );
	}
}

/**
 * Child theme for Borderzine
 *
 */

 /**
 * Include files that should be included
 */
$includes = array(
	'/homepages/layout.php',
);
foreach ( $includes as $include ) {
	if ( 0 === validate_file( get_stylesheet_directory() . $include ) ) {
		require_once( get_stylesheet_directory() . $include );
	}
}

function borderzine_stylesheets() {

	wp_dequeue_style( 'largo-child-styles' );
	wp_deregister_style( 'largo-child-styles' );
	
	$suffix = (LARGO_DEBUG) ? '.min' : '';
	wp_enqueue_style(
		'largo-child-styles',
		get_stylesheet_directory_uri() . '/css/style' . $suffix . '.css',
		array( 'largo-stylesheet' ),
		filemtime( get_stylesheet_directory() . '/css/style' . $suffix . '.css' )
	);

}
add_action( 'wp_enqueue_scripts', 'borderzine_stylesheets', 20 );

/**
 * The Borderzine sidebar shortcode function
 *
 * @param Array $atts Shortcode attributes or block properties.
 * @param String $content Shortcode wrapped text; not used in this shortcode.
 * @param String $tag The complete shortcode tag; not used in this shortcode.
 * @return HTML
 * @since 0.1
 */
function borderzine_sidebar_shortcode( $atts = array(), $content = '', $tag = '' ) {
	ob_start();
	do_action( 'borderzine_shortcode_sidebar', $atts );
	$ret = ob_get_clean();
	return $ret;
}
add_shortcode( 'borderzine_sidebar', 'borderzine_sidebar_shortcode' );

/**
 * Outputs the sidebar that is passed into the `sidebar` arg
 *
 * @param Array $args Shortcode attributes or block properties.
 * @since 0.1
 */
function borderzine_do_sidebar( $args ) {
	if ( isset( $args['sidebar'] ) ) {
		dynamic_sidebar( esc_attr( $args['sidebar'] ) );
	}
}
add_action( 'borderzine_shortcode_sidebar', 'borderzine_do_sidebar' );

/**
 * Modifies the default WordPress search query to see if the 
 * search terms match any author names and return any posts found by said author
 * 
 * @param String $posts_search SQL query for the search
 * @param Object $wp_query_obj The current wp_query object
 * @return String $posts_search SQL query for the search
 * @since 0.1
 */
function borderzine_search_posts_by_author( $posts_search, $wp_query_obj ) {

	if ( !is_search() || empty( $posts_search ) ) {
		return $posts_search;
	}

	global $wpdb;

	// search all authors to see if any having a name that matches the search term
	$search = sanitize_text_field( get_query_var( 's' ) );
	$args = array(
		'count_total' => false,
		'search' => sprintf( '*%s*', $search ),
		'search_fields' => array(
			'display_name',
		),
		'fields' => 'ID',
	);
	$matching_users = get_users( $args );

	// don't modify the query if there aren't any matching users
	if ( empty( $matching_users ) ) {
		return $posts_search;
	}

	// modify our posts search query to also search for posts with matching user
	$posts_search = str_replace( ')))', ")) OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))", $posts_search );

	return $posts_search;

}
add_filter( 'posts_search', 'borderzine_search_posts_by_author', 10, 2 );