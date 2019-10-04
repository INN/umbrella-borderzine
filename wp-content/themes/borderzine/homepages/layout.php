<?php
include_once get_template_directory() . '/homepages/homepage-class.php';

class Borderzine extends Homepage {
	var $name = 'Borderzine';
	var $type = 'borderzine';
    var $description = 'The homepage for Borderzine.';
    var $sidebars = array(
		'Homepage Sidebar',
		'Homepage Middle 1st 1x3',
		'Homepage Middle 2nd 1x3',
		'Homepage Middle 3rd 1x3',
		'Homepage Middle 4th 1x3',
		'Homepage Middle 1x1x1',
        'Homepage Bottom'
	);
	var $rightRail = false;

	public function __construct( $options = array() ) {
		$defaults = array(
			'template' => get_stylesheet_directory() . '/homepages/template.php',
			'assets' => array(
				array(
					'homepage',
					get_stylesheet_directory_uri() . '/homepages/assets/css/homepage.css',
					array(),
					filemtime( get_stylesheet_directory() . '/homepages/assets/css/homepage.css' ),
                ),
                array(
					'homepage-top-stories',
					get_template_directory_uri() . '/homepages/assets/css/top-stories.css',
					array(),
					filemtime( get_template_directory() . '/homepages/assets/css/top-stories.css' ),
				),
			),
			'prominenceTerms' => array(
				array(
					'name' => __('Homepage Featured', 'largo'),
					'description' => __('If you are using the Newspaper or Carousel optional homepage layout, add this label to posts to display them in the featured area on the homepage.', 'largo'),
					'slug' => 'homepage-featured'
				),
				array(
					'name' => __('Homepage Top Story', 'largo'),
					'description' => __('If you are using a "Big story" homepage layout, add this label to a post to make it the top story on the homepage', 'largo'),
					'slug' => 'top-story'
				),
			),
			'sidebars' => array(
                'Homepage Sidebar (Appears to the right of the homepage content under the Featured area)',
				'Homepage Middle 1st 1x3 (The middle area of the homepage that displays 1 big post to the left with 3 smaller posts more to the right.)',
				'Homepage Middle 2nd 1x3 (The middle area of the homepage that displays 1 big post to the left with 3 smaller posts more to the right.)',
				'Homepage Middle 3rd 1x3 (The middle area of the homepage that displays 1 big post to the left with 3 smaller posts more to the right.)',
				'Homepage Middle 4th 1x3 (The middle area of the homepage that displays 1 big post to the left with 3 smaller posts more to the right.)',
				'Homepage Middle 1x1x1 (The middle area of the homepage that displays 3 posts from separate categories.)',
				'Homepage Bottom (The bottom area of the homepage)',
			),
		);
		$options = array_merge( $defaults, $options );
		$this->load( $options );
	}

	/**
	 * Register our sidebars
	 */
	public function register_sidebars() {
		$sidebars = array(
		);
	}

}

/**
 * Register this layout with Largo
 */
function borderzine_homepage_layout() {
	register_homepage_layout( 'Borderzine' );
}
add_action( 'init', 'borderzine_homepage_layout' );