
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
	// This is the shortcode that disables doing shortcodes.
	if ( isset( $atts['no'] ) ) {
		return '';
	}
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