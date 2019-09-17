<?php
/**
 * This file contains the Borderzine_Byline class
 */

/**
 * Generates a byline for a normal WordPress user
 *
 * @param Array $args an array with the following keys:
 *     $args = [
 *         'post_id' => (int) the ID of the post that we are creating a byline for
 *         'exclude_date ' => (bool) Whether or not to display the date
 *     ]
 */
class Borderzine_Byline {

	/** @var int The ID of the post this byline is for */
	protected $post_id;

	/** @var bool Whether or not the byline should include the date */
	private $exclude_date;

	/**
	 * @var array The post's custom fields
	 * @link https://codex.wordpress.org/Function_Reference/get_post_custom
	 */
	private $custom;

	/**
	 * Temporary variable used for the author ID;
	 * This must be public, because Largo_CoAuthors_Byline's methods incorporate methods from Largo_Byline, and parent classes cannot see private or protected members of extending classes.
	 * @var int The ID of the author for this post
	 */
	public $author_id;

	/**
	 * @var string The HTML ouput of this class
	 * @see __toString
	 */
	public $output;

	function __construct( $args ) {
		$this->populate_variables( $args );
		$this->generate_byline();
	}

	/**
	 * Set us up the vars
	 *
	 * @param array $args Associative array containing following keys:
	 *     - 'post_id': an integer post ID
	 *     - 'exclude_date': boolean whether or not to include the date in the byline
	 *
	 * @see $post_id      Sets this from $args
	 * @see $exclude_date Sets this from $args
	 * @see $custom       Fills this array with the output of get_post_custom
	 * @see $author_id    Sets this from the post meta
	 */
	function populate_variables( $args ) {
		$this->post_id = $args['post_id'];
		$this->exclude_date = $args['exclude_date'];
		$this->custom = get_post_custom( $this->post_id );
		$this->author_id = get_post_meta( $this->post_id, 'post_author', true );
	}

	/**
	 * this creates the byline text and adds it to $this->output
	 *
	 * @see $output Creates this
	 */
	function generate_byline() {
		ob_start();

		// Author-specific portion of byline
        $this->avatar();
        $this->author_link();
        $this->maybe_published_date();
        $this->excerpt();

		// The generic parts
        $this->edit_link();

		$this->output = ob_get_clean();
	}

	/**
	 * This is what turns the whole class into a string
	 *
	 * @see $output
	 * @see generate_byline()
	 */
	public function __toString() {
		return $this->output;
	}

	/**
	 * On single posts, output the avatar for the author object
	 * This supports both Largo_Byline and Largo_CoAuthors_Byline
	 */
	function avatar() {
		// only do avatars if it's a single post
		if ( ! is_single() ) {
            $output = ' ';
		} else {
            $output = '';
			$author_email = get_the_author_meta( 'email', $this->author_id );
			if ( isset( $this->author ) && $this->author->type == 'guest-author' && get_the_post_thumbnail( $this->author->ID ) ) {
				$output = get_the_post_thumbnail( $this->author->ID, array( 60,60 ) );
				$output = str_replace( 'attachment-32x32', 'avatar avatar-32 photo', $output );
				$output = str_replace( 'wp-post-image', '', $output );
			} else if ( largo_has_avatar( $author_email ) ) {
				$output = get_avatar(
					$author_email,
					32,
					'',
					get_the_author_meta( 'display_name', $this->author_id ),
					array(
						'class' => 'avatar avatar-32 photo',
					)
				);
			}
			$output .= ' '; // to reduce run-together bylines
		}
		echo $output;
	}

	/**
	 * a wrapper around largo_author_link
	 */
	function author_link() {
		$authors = largo_author_link( false, $this->post_id );
		$output = '<span class="by-author"><span class="by">' . __( 'By', 'largo' ) . '</span> <span class="author vcard" itemprop="author">' . $authors . '</span></span>';
		echo $output;
	}

	/**
	 * If job titles are enabled by Largo's theme option, display the one for this author
	 */
	function job_title() {
		$show_job_titles = of_get_option( 'show_job_titles', false );
		$output = '';
		// only do this if we're showing job titles and there is one to be shown
		if ( $show_job_titles && $job = get_the_author_meta( 'job_title' , $this->author_id ) ) {
			$output .= '<span class="job-title"><span class="comma">,</span> ' . $job . '</span>';
		}
		echo $output;
	}

	/**
	 * If this author has a twitter ID, output it as a link on an i.icon-twitter
	 */
	function twitter() {
		$twitter = get_the_author_meta( 'twitter', $this->author_id );
		$output = '';
		if ( $twitter && is_single() ) {
			$output .= ' <span class="twitter"><a href="https://twitter.com/' . largo_twitter_url_to_username( $twitter ) . '"><i class="icon-twitter"></i></a></span>';
		}
		echo $output;
	}

	/**
	 * Determine whether to display the date
	 */
	function maybe_published_date() {
		if ( ! $this->exclude_date ) {
			$this->published_date();
		}
	}

	/**
	 * A wrapper around largo_time to determine when the post was published
	 */
	function published_date() {
		echo sprintf(
			'<span class="sep"> |</span> <time class="entry-date updated dtstamp pubdate" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( 'c', $this->post_id ) ),
			largo_time( false, $this->post_id )
		);
	}

	/**
	 * Output the edit link for this post, only to admin users
	 */
	function edit_link() {
		// Add the edit link if the current user can edit the post
		if ( current_user_can( 'edit_post', $this->post_id ) ) {
			echo ' <span class="edit-link"><a href="' . get_edit_post_link( $this->post_id ) . '">' . __( 'Edit This Post', 'largo' ) . '</a></span>';
		}
    }
    
    function excerpt() {
        
        $authors = array();
		$bios = '';

		if( get_post_meta( $post->ID, 'largo_byline_text' ) ) {
			$byline_text = esc_attr( get_post_meta( $post->ID, 'largo_byline_text', true ) );
		}

		$is_series_landing = ( function_exists( 'largo_is_series_landing') ) ? largo_is_series_landing( $post ) : false;

		if( (is_singular() || is_author() || $is_series_landing) && empty($byline_text) ) {
			if ( is_singular() || $is_series_landing ) {
				if ( function_exists( 'get_coauthors' ) ) {
					$authors = get_coauthors( get_queried_object_id() );
				} else {
					$authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
				}
			} else if ( is_author() ) {
				$authors = array( get_queried_object() );
			}

			// make sure we have at least one bio before we show the widget
			foreach ( $authors as $key => $author ) {
				if ( is_object( $author ) && isset( $author->description ) ) {
					$bio = trim( $author->description );
				} else {
					$bio = '';
				}
				if ( ! is_author() && empty( $bio ) ) {
					unset( $authors[$key] );
				} else {
					$bios .= $bio;
				}
			}
		}

		if ( is_author() || ! empty( $bios ) ) {

			foreach( $authors as $author_obj ) {
				$context = array('author_obj' => $author_obj); ?>

					<div class="author-box row-fluid author vcard clearfix">
						<?php largo_render_template( 'partials/author-byline-bio', 'description', $context ); ?>
						<?php largo_render_template( 'partials/author-bio', 'social-links', $context ); ?>
					</div>
			<?php }

		}

    }
}