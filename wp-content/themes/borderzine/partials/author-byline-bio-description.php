<?php

// Job!
$show_job_titles = of_get_option('show_job_titles');
if ( $job = $author_obj->job_title && $show_job_titles ) {
	echo '<p class="job-title">' . esc_attr( $author_obj->job_title ) . '</p>';
}
// Description
if ( $author_obj->description ) {
	echo '<p>' . esc_attr( $author_obj->description ) . '</p>';
}
