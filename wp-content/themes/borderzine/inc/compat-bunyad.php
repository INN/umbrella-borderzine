<?php
/**
 * Compatibility functions for the "bunyad" plugin that was formerly used on this site.
 */

/**
 * Filter the Largo hero DOM to display the bunyad featured video embed, if it is set
 *
 * @todo is there a better way to do this? It would be nice to use WordPress' oembed get functionality to convert the stored HTML into a known-safe iframe, but it's not clear if WordPress' oembed parser parses URLs that are actually iframes
 *    <iframe width="640" height="360" src="https://www.youtube.com/embed/EbjxpVAi-v4?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
 *
 * @filter largo_get_hero
 * @link https://github.com/INN/largo/blob/f50df16e9e06fb9402802e2f1ce76ce058d26d76/inc/featured-media.php#L157
 * @param String $ret The DOM for the featured media
 * @param WP_Post $post the present post
 * @param String $classes HTML class names to apply to the outer div.hero
 * @return String $ret The DOM for the featured media
 */
function borderzine_bunyad_hero( $ret, $post, $classes = '' ) {
	$bunyad_video = get_post_meta( $post->ID, '_bunyad_featured_video', true );
	if ( !empty ( $bunyad_video ) && is_string( $bunyad_video ) ) {
		$context = array(
			'classes' => 'hero embed ' . $classes,
			'featured_media' => array(
				'embed' => $bunyad_video,
				'type' => 'video',
			),
			'the_post' => $post,
		);

		ob_start();
		largo_render_template( 'partials/hero', 'featured-embed', $context );
		$ret = ob_get_clean();
		return $ret;
	}
	return $ret;
}
add_filter( 'largo_get_hero', 'borderzine_bunyad_hero', 10, 3 );
