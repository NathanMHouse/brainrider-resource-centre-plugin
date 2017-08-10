<?php
/**
 * Template Functions
 * 
 * Functions used to set and/or alter the templates used in the front-end 
 * display of the site.
 *
**/

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Archive Template Functions
	?.? Filter Archive Template
	?.? Output BR RC Archive Template Banner Path
?.? Content Template Functions
	?.? Output BR RC Content Excerpt Template Path
	?.? Output BR RC Content None Template Path
	?.? Output BR RC Content Single Template Path
?.? Single Template Functions
	?.? Filter Single Template
	?.? Output BR RC Single Banner Template Path
	?.? Output BR RC Single Related Posts Template Path
	?.? Output BR RC Single Transcript Template Path


/*--------------------------------------------------------------
?.? Archive Template Functions
----------------------------------------------------------------*/
/**
 * ?.? Filter Archive Template
 *
 * Checks to see if is br_resource post type and if true, filters archive 
 * template and returns custom layout.
 *
 * @param   array	$archive_template 	Template file for archive
 * @return  type    $archive_template 	Filtered template file for archive
**/
function br_rc_archive_template( $archive_template ) {

	// Get our plugin options and check the set layout
	$options 	 = get_option( 'br_rc_settings_group' );
	$layout_type = $options['br_rc_layout_type'];

	// Declare global post object
	global $post;
	global $wp_query;

	// If archive is for br resource post type, filter template
	if ( is_post_type_archive( 'br_rc_resource' ) || is_tax() ) :
		$archive_template  = plugin_dir_path( dirname( __FILE__ ) );

		// Depending upon layout type adjust template path (default is sidebar)
		if ( 'full_width' == $layout_type ) :
			$archive_template .= '/templates/br-rc-archive-full-width-resource.php'; // full-width template w/o sidebar

			// Filter to alter default archive full-width template file
			$archive_template = apply_filters( 'br_rc_archive_full_width_template_filter',
											   $archive_template );
		else:
			$archive_template .= '/templates/br-rc-archive-resource.php'; // default template w/ sidebar

			// Filter to alter default archive template file
			$archive_template = apply_filters( 'br_rc_archive_template_filter',
											   $archive_template );
		endif;
	endif;

	// Return template
	return $archive_template;
}


/**
 * ?.? Output BR RC Archive Template Banner Path
 *
 * 
 * @return string $archive_banner_template 	Path to archive banner template
**/

function br_rc_archive_banner_template() {

	$archive_banner_template  = plugin_dir_path( dirname( __FILE__ ) );
	$archive_banner_template .= 'templates/br-rc-archive-resource-banner.php';

	// Filter the default path
	$archive_banner_template = apply_filters( 'br_rc_archive_banner_template_filter', 
											  $archive_banner_template );

	// Return the path
	return $archive_banner_template;
}


/*--------------------------------------------------------------
?.? Content Template Functions
----------------------------------------------------------------*/
/**
 * ?.? Output BR RC Content Excerpt Template Path
 *
 * 
 * @return string $content_excerpt_template		Path to content excerpt template
**/
function br_rc_content_excerpt_template() {

	// Get our plugin options and check the set layout
	$options 	 = get_option( 'br_rc_settings_group' );
	$layout_type = $options['br_rc_layout_type'];

	$content_excerpt_template = plugin_dir_path( dirname( __FILE__ ) );

	// Depending upon layout type adjust template path (default is sidebar)
	if ( 'full_width' == $layout_type ) :
		$content_excerpt_template .= 'templates/br-rc-content-full-width-excerpt.php'; // full-width template w/o sidebar

		// Filter to alter default full-width path
		$content_excerpt_template = apply_filters( 'br_rc_content_full_width_excerpt_template_filter',
												   $content_excerpt_template );

	else:
		$content_excerpt_template .= 'templates/br-rc-content-excerpt.php'; // default template w/ sidebar

		// Filter the default path
		$content_excerpt_template = apply_filters( 'br_rc_content_excerpt_template_filter',
												   $content_excerpt_template );
	endif;

	// Return the path
	return $content_excerpt_template;
}


/**
 * ?.? Output BR RC Content None Template Path
 *
 * 
 * @return string $content_none_template 	Path to content none template
**/
function br_rc_content_none_template() {

	// Get our plugin options and check the set layout
	$options 	 = get_option( 'br_rc_settings_group' );
	$layout_type = $options['br_rc_layout_type'];

	$content_none_template  = plugin_dir_path( dirname( __FILE__ ) );

	// Depending upon layout type adjust template path (default is sidebar)
	if ( 'full_width' == $layout_type ) :
		$content_none_template .= 'templates/br-rc-content-full-width-none.php'; // full-width template w/o sidebar

		// Filter the full-width path
		$content_none_template = apply_filters( 'br_rc_content_full_width_none_template_filter',
												$content_none_template );
	else:
		$content_none_template .= 'templates/br-rc-content-none.php'; // default template w/ sidebar

		// Filter the default path
		$content_none_template = apply_filters( 'br_rc_content_none_template_filter',
												$content_none_template );
	endif;

	// Return the path
	return $content_none_template;
}


/*--------------------------------------------------------------
?.? Single Template Functions
----------------------------------------------------------------*/
/**
 * ?.? Filter Single Template
 *
 * Checks to see if is br resource post type and if true, filters single post
 * template and returns custom layout.
 *
 * @param   array	$single_template 	Template file for single post
 * @return  type    $single_template 	Filtered template file for single
**/
function br_rc_single_template( $single_template ) {

	// Declare global post object
	global $post;

	// If archive is for br resource post type, filter template
	if ( $post->post_type == 'br_rc_resource' ) :
		$single_template  = plugin_dir_path( dirname( __FILE__ ) );
		$single_template .= '/templates/br-rc-single-resource.php';

		// Filter to alter default single template file
		$single_template = apply_filters( 'br_rc_single_template_filter',
										  $single_template );
	endif;

	// return template
	return $single_template;
}


/**
 * ?.? Output BR RC Content Single Template Path
 *
 * 
 * @return string $single_content_template 	Path to content single template
**/
function br_rc_single_content_template() {

	$single_content_template  = plugin_dir_path( dirname( __FILE__ ) );
	$single_content_template .= 'templates/br-rc-content-single.php';

	// Filter the default path
	$single_content_template = apply_filters( 'br_rc_single_content_template_filter', 
											  $single_content_template );

	// Return the path
	return $single_content_template;
}


/**
 * ?.? Output BR RC Single Banner Template Path
 *
 * 
 * @return string $single_banner_template 	Path to single banner template
**/
function br_rc_single_banner_template() {

	$single_banner_template = plugin_dir_path( dirname( __FILE__ ) );
	$single_banner_template .= 'templates/br-rc-single-resource-banner.php';

	// Filter the default path
	$single_banner_template = apply_filters( 'br_rc_single_banner_template_filter',
											 $single_banner_template );

	// Return the path
	return $single_banner_template;
}


/**
 * ?.? Output BR RC Single Related Posts Template Path
 *
 * 
 * @return string $single_related_posts_template 	Path to single related posts template
**/
function br_rc_single_related_posts_template() {

	$single_related_posts_template  = plugin_dir_path( dirname( __FILE__ ) );
	$single_related_posts_template .= 'templates/br-rc-single-resource-related-posts.php';

	// Filter the default path
	$single_related_posts_template = apply_filters( 'br_rc_single_related_posts_template_filter',
						   $single_related_posts_template );

	// Return the path
	return $single_related_posts_template;
}


/**
 * ?.? Output BR RC Single Transcript Template Path
 *
 * 
 * @return string $single_transcript_template 	Path to single transcript template
**/
function br_rc_single_transcript_template() {

	$single_transcript_template = plugin_dir_path( dirname( __FILE__ ) );
	$single_transcript_template .= 'templates/br-rc-single-resource-transcript.php';

	// Filter the default path
	$single_transcript_template = apply_filters( 'br_rc_single_transcript_template_filter',
												 $single_transcript_template );

	// Return the path
	return $single_transcript_template;
}