<?php

/**
 * Outputs custom byline and link (if set), otherwise outputs author link and post date
 *
 * @param Boolean $echo Echo the string or return it (default: echo)
 * @param Boolean $exclude_date Whether to exclude the date from byline (default: false)
 * @param WP_Post|Integer $post The post object or ID to get the byline for. Defaults to current post.
 * @return String Byline as formatted html
 */
if ( ! function_exists( 'borderzine_byline' ) ) {
	function borderzine_byline( $echo = true, $exclude_date = false, $post = null ) {
		// Get the post ID
		if (!empty($post)) {
			if (is_object($post))
				$post_id = $post->ID;
			else if (is_numeric($post))
				$post_id = $post;
		} else {
			$post_id = get_the_ID();
			if ( WP_DEBUG || LARGO_DEBUG ) {
				_doing_it_wrong( 'largo_byline', 'largo_byline must be called with a post or post ID specified as the third argument. For more information, see https://github.com/INN/largo/issues/1517 .', '0.6' );
			}
		}
		// Set us up the options
		// This is an array of things to allow us to easily add options in the future
		$options = array(
			'post_id' => $post_id,
			'values' => get_post_custom( $post_id ),
			'exclude_date' => $exclude_date,
		);
		if ( isset( $options['values']['largo_byline_text'] ) && !empty( $options['values']['largo_byline_text'] ) ) {
			// Temporary placeholder for largo custom byline option
            $byline = new Largo_Custom_Byline( $options );
		} else if ( function_exists( 'get_coauthors' ) ) {
			// If Co-Authors Plus is enabled and there is not a custom byline
            $byline = new Largo_CoAuthors_Byline( $options );
		} else {
			// no custom byline, no coauthors: let's do the default
            $byline = new Borderzine_Byline( $options );
		}
		/**
		 * Filter the largo_byline output text to allow adding items at the beginning or the end of the text.
		 *
		 * @since 0.5.4
		 * @param string $partial The HTML of the output of largo_byline(), before the edit link is added.
		 * @param array $array Associative array of argument name => argument value, with the arguments passed to largo_byline(). Since https://github.com/INN/largo/issues/1656
		 * @link https://github.com/INN/Largo/issues/1070
		 */
		$byline = apply_filters(
			'largo_byline',
			$byline,
			array(
				'echo' => $echo,
				'exclude_date' => $exclude_date,
				'post' => $post
			)
		);
		if ( $echo ) {
			echo $byline;
		}
		return $byline;
	}
}