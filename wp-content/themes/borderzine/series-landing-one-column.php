<?php
/**
 * Template Name: Single-column Series Landing Page
 * Description: A series landing page without the central loop of posts, and without sidebars as such.
 */

// enqueue the specific stylesheet
add_action( 'wp_enqueue_scripts', function() {
	$suffix = (LARGO_DEBUG) ? '.min' : '';

	wp_enqueue_style(
		'series-landing-one-column',
		get_stylesheet_directory_uri() . '/css/series-landing-one-column' . $suffix . '.css',
		array(),
		filemtime( get_stylesheet_directory() . '/css/series-landing-one-column' . $suffix . '.css' )
	);
});

// start the page
get_header();

// Load up our meta data and whatnot
the_post();

//make sure it's a landing page.
if ( 'cftl-tax-landing' == $post->post_type ) {
	$opt = get_post_custom( $post->ID );
	foreach( $opt as $key => $val ) {
		$opt[ $key ] = $val[0];
	}
	$opt['show'] = maybe_unserialize($opt['show']);	//make this friendlier
	if ( 'all' == $opt['per_page'] ) $opt['per_page'] = -1;
	/**
	 * $opt will look like this:
	 *
	 *	Array (
	 *		[header_enabled] => boolean
	 *		[show_series_byline] => boolean
	 *		[show_sharebar] => boolean
	 *		[header_style] => standard|alternate
	 *		[cftl_layout] => one-column|two-column|three-column
	 *		[per_page] => integer|all
	 *		[post_order] => ASC|DESC|top, DESC|top, ASC
	 *		[footer_enabled] => boolean
	 *		[footerhtml] => {html}
	 *		[show] => array with boolean values for keys byline|excerpt|image|tags
	 *	)
	 *
	 * The post description is stored in 'excerpt' and the custom HTML header is the post content
	 */
}

// #content span width helper
$content_span = array( 'one-column' => 12, 'two-column' => 8, 'three-column' => 5 );
?>

<?php if ( $opt['header_enabled'] ) : ?>
	<?php
		/**
		 * Use Largo's gutenberg alignfull styles to make a fullwidth hero image
		 */
		largo_hero( $post, 'alignfull' );
	?>
	<section id="series-header" class="span12 clearfix">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php
		if ( $opt['show_series_byline'] )
			echo '<h5 class="byline">' . largo_byline( false, false, get_the_ID() ) . '</h5>';
		if ( $opt['show_sharebar'] )
			largo_post_social_links();
		?>
		<div class="description">
			<?php echo apply_filters( 'the_content', $post->post_excerpt ); ?>
		</div>
		<?php
		if ( 'standard' == $opt['header_style'] ) {
			//need to set a size, make this responsive, etc
			?>
			<div class="full series-banner full-image"><?php the_post_thumbnail( 'full' ); ?></div>
		<?php
		} else {
			the_content();
		}
		?>
	</section>
<?php endif; ?>


<?php // display left rail
if ( 'three-column' == $opt['cftl_layout'] ) :
		$left_rail = $opt['left_region'];
?>
	<aside id="sidebar-left" class="span12 clearfix">
		<div class="widget-area" role="complementary">
			<?php
				dynamic_sidebar($left_rail);
			?>
		</div>
	</aside>
<?php
endif;
?>

<?php // display left rail
if ($opt['cftl_layout'] != 'one-column') :
	if (!empty($opt['right_region']) && $opt['right_region'] !== 'none') {
		$right_rail = $opt['right_region'];
	} else {
		$right_rail = 'single';
	}
	?>
	<aside id="sidebar" class="span12 clearfix">
		<?php do_action('largo_before_sidebar_content'); ?>
		<div class="widget-area" role="complementary">
			<?php
				do_action('largo_before_sidebar_widgets');
				dynamic_sidebar($right_rail);
				do_action('largo_after_sidebar_widgets');
			?>
		</div><!-- .widget-area -->
		<?php do_action('largo_after_sidebar_content'); ?>
	</aside>

	<?php
endif;

//display series footer
if ( 'none' != $opt['footer_style'] ) : ?>
	<section id="series-footer">
		<?php
			/*
			 * custom footer html
			 * If we don't reset the post meta here, then the footer HTML is from the wrong post. This doesn't mess with LMP, because it happens after LMP is enqueued in the main column.
			 */
			wp_reset_postdata();
			if ( 'custom' == $opt['footer_style']) {
				echo apply_filters( 'the_content', $opt['footerhtml'] );
			} else if ( 'widget' == $opt['footer_style'] && is_active_sidebar( $post->post_name . "_footer" ) ) { ?>
				<aside id="sidebar-bottom" class="clearfix span12">
					<?php dynamic_sidebar( $post->post_name . "_footer" ); ?>
				</aside>
			<?php }
		?>
	</section>
<?php endif; ?>

<!-- /.grid_4 -->
<?php get_footer();
