<?php
/**
 * Template Name: Blocks template (legacy Bunyad)
 * Single Post Template: Blocks template (legacy Bunyad)
 * Description: Shows Bunyad custom post content.
 *
 * Basically the same as Largo's full-page.php, except there's the added function here:
 *
 * @package Largo
 * @since 0.1
 */

add_filter( 'the_content', function( $content ) {
	if ( class_exists( 'Bunyad' ) ) {
		try {
			error_log(var_export( Bunyad::posts()->the_content(), true));
			$content = Bunyad::posts()->the_content();
		} catch (Error $e ) {
			$content .= sprintf(
				"<pre><code>\n %1$s \n </code></pre>",
				$e->getMessage()
			);
		}
	}
	$content .= sprintf(
		"<!-- --> Bunyad debug content: <pre><code>\n %1$s \n </code></pre> <-- -->",
		get_post_meta( get_the_ID(), 'panels_data', true )
	);
	return $content;
}, 100, 1 );


get_header();
?>

<div id="content" class="span12" role="main">
	<?php
		while ( have_posts() ) : the_post();
			if ( is_page() ) {
				get_template_part( 'partials/content', 'page' );
			} else {
				get_template_part( 'partials/content', 'single' );
				comments_template( '', true );
			}
		endwhile; // end of the loop.
	?>
</div><!--#content-->

<?php get_footer();
