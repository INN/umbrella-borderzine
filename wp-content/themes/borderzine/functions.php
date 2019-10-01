
<?php
/**
 * Child theme for Borderzine
 *
 */

/**
 * Include files that should be included
 */
$includes = array(
	'/inc/byline_class.php',
	'/inc/compat-bunyad.php',
	'/inc/post-tags.php',
	'/inc/widgets/class-borderzine-3-col-widget.php',
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
		'fields' => 'ID',
		'has_published_posts' => true,
	);
	$matching_users = get_users( $args );

	// don't modify the query if there aren't any matching users
	if ( empty( $matching_users ) ) {
		return $posts_search;
	}

	// count matching users
	$matching_users_count = count( $matching_users );

	// prepare array with correct amount of placeholders that match the user count
	$matching_users_placeholders = array_fill( 0, $matching_users_count, '%s' );

	// put all matching user ids into one string
	$matching_users_str = implode( ', ', $matching_users_placeholders );

	// use wpdb to prepare our partial query
	$posts_search_author_query = 
		$wpdb->prepare(
			"OR ( ".$wpdb->posts.".post_author IN ( $matching_users_str )))",
			$matching_users
		);

	// modify our posts search query to also search for posts with matching user
	$posts_search = str_replace( ')))', ")) ".$posts_search_author_query, $posts_search );

	return $posts_search;

}
add_filter( 'posts_search', 'borderzine_search_posts_by_author', 10, 2 );

/**
 * Finds author ids by their display name
 * 
 * @param String $display_name The display name to search for
 * @return String $user_ids The IDs of matched users from the display name
 */
function get_author_id_by_display_name( $display_name ) {
	global $wpdb;

	$display_name = sanitize_text_field( $display_name );

	if( !$users = $wpdb->get_results( $wpdb->prepare( "SELECT `ID` FROM $wpdb->users WHERE `display_name` LIKE '%%%s%%%'", $wpdb->esc_like( $display_name ) ) ) ){
		return false;
	}

	$user_ids = array();

	foreach( $users as $user ){
		array_push( $user_ids, $user->ID );
	}

	return $user_ids;
}
