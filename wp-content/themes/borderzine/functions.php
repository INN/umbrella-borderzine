
<?php

/**
 * Include files that should be included
 */
$includes = array(
	'/inc/byline_class.php',
	'/inc/compat-bunyad.php',
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
 * Determine whether or not a user has an avatar. Fallback checks if user has a gravatar.
 *
 * @param $email string an author's email address
 * @return bool true if an avatar is available for this user
 * @since 0.1
 */
function borderzine_has_avatar( $email ) {
	$user = get_user_by( 'email', $email );
	if ( ! empty( $user ) ) {
		$result = largo_get_user_avatar_id( $user->ID );
		if ( ! empty ( $result ) ) {
			return true;
		// this checks if the user has a photo placed by the User Photo plugin
		} else if( function_exists( userphoto_the_author_photo() ) ){
			return true;
		} else {
			if ( largo_has_gravatar( $email ) ) {
				return true;
			}
		}
	}
	return false;
}

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

/**
 * Finds author ids by their display name
 * 
 * @param String $display_name The display name to search for
 * @return String $user->ID The ID of the matched author
 */
function get_author_id_by_display_name( $display_name ) {

	global $wpdb;
	
	$display_name = sanitize_text_field( $display_name );

    if( !$user = $wpdb->get_row( $wpdb->prepare( "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name ) ) ){
		return false;
	}

	return $user->ID;
	
}