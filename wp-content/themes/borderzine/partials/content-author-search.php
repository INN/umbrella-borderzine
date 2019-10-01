<?php
/*
 * The template for displaying authors in search results.
 */
$author_link = get_author_posts_url( $author_id );
$author_name = get_the_author_meta( 'display_name', $author_id );
$author_bio = get_the_author_meta( 'description', $author_id );
$entry_classes = 'entry-content';
?>
<article id="author-<?php echo $author_id; ?>" <?php post_class('clearfix'); ?>>

	<div class="<?php echo $entry_classes; ?>">

		<h2 class="entry-title">
			<a href="<?php echo $author_link; ?>" title="Author: <?php echo $author_name; ?>" rel="bookmark">Author: <?php echo $author_name; ?></a>
		</h2>

		<p><?php echo $author_bio; ?></p>

		<small class="date-link">
			<a href="<?php echo $author_link; ?>" title="<?php echo $author_link; ?>" rel=""><?php echo $author_link; ?></a>
		</small>

	</div><!-- .entry-content -->

</article>
