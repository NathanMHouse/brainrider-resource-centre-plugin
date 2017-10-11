<?php
/**
 * Front-end Functions
 * 
 * Functions used to display and/or affect the front-end rendering of the site.
 *
**/

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Adjust Query
	?.? Start Session
	?.? Set Overall Resource Post Order
	?.? Adjust Resource Post Query Args
?.? Output/Build Modules
	?.? Build Pagination
	?.? Build Taxonomy Term Links
	?.? Output Custom Excerpt
	?.? Output Filter Controls
	?.? Output Resource Type Icon/Label
	?.? Output No Results Terms
	?.? Output Title with Title Case Styles


/*--------------------------------------------------------------
?.? Adjust Query
---------------------------------------------------------------*/
/**
 * ?.? Start Session
 *
 * Start session to enable $_SESSION array.
 *
**/
function br_rc_start_session() {
    if ( !session_id() ) :
        session_start();
    endif;
}


/**
 * ?.? Set Overall Resource Post Order
 *
 * Helper function used in br_rc_query.
 *
 *
 * @return  array 	    $merged_post_ids 	Reordered array of post IDs
**/
function br_rc_id_query() {

	// Get all default post ids
	$default_post_args = array(
		'post_type'         => 'br_rc_resource',
		'posts_per_page'    => -1,
		'fields'            => 'ids',
	);
	$default_post_ids = get_posts( $default_post_args );

	// Get all featured post ids
	$featured_post_args = array(
		'post_type'         => 'br_rc_resource',
		'posts_per_page'    => -1,
		'meta_key'          => '_br_rc_featured_resource_toggle',
		'meta_value'        => '1',
		'fields'            => 'ids',
	);
	$featured_post_ids = get_posts( $featured_post_args );

	// Shuffle the featured post ids
	shuffle( $featured_post_ids );

	// Merge the two arrays
    $merged_post_ids = array_merge( $featured_post_ids, $default_post_ids );

    // Return merged array
	return $merged_post_ids;
}


/**
 * ?.? Adjust Resource Post Query Args
 *
 * Alters main query for br_rc_resource.
 * 
 * @param 	object	$q 		Original query object 	
**/
add_action( 'pre_get_posts', 'br_rc_query' );
function br_rc_query( $q ) {

	// Alter main query
	// General
	if ( 
	!is_admin() 
	&& $q->is_main_query() 	
	&& $q->is_post_type_archive( 'br_rc_resource' ) ) :

		// Get options and current layout
		$options 		= get_option( 'br_rc_settings_group' );
		$layout_type	= $options['br_rc_layout_type'];

		// Set number of posts per page
		// If fw and options setting is multiple of 4 (0 remainder)
		// Or if w/ sidebar, use options. Else use 8.
		$posts_per_page 	= ( 'full_width' != $layout_type 
								|| ( 'full_width' == $layout_type
									 && 0 == get_option( 'posts_per_page' ) % 4 ) )
							? get_option( 'posts_per_page' )
							: 8;
		$paged 				= ( get_query_var( 'paged' ) ) 
					  	  	? get_query_var( 'paged' ) 
					      	: 1;

		// If $_POST array exist, move values to $_SESSION array
        // Else, if it does not exist and on page 1, clear it
		if ( $_POST ) :
			foreach( $_POST as $key => $value ) :
				$_SESSION[ $key ] = $value;
			endforeach;
		elseif ( !$_POST && $paged < 2 ) :
			foreach ( $_SESSION as $key => $value ) :
				$_SESSION[ $key ] = '';
			endforeach;
		endif;

		// Vars
		$search_term	= ( isset( $_SESSION['br_rc_filter_search'] ) 
						&& !empty( $_SESSION['br_rc_filter_search'] ) )
					  ? sanitize_text_field( $_SESSION['br_rc_filter_search'] )
					  : '';

		$custom_query_array = array(
			'posts_per_page' 	=> $posts_per_page,
			'paged'				=> $paged,
			'post__in'          => br_rc_id_query(),
            'orderby'           => 'post__in',
            's'					=> $search_term,
		);
	
		// Loop through POST or GET array and add values to custom query array
		if ( isset( $_SESSION ) && !empty( $_SESSION ) ) :

			foreach ( $_SESSION as $key => $value ) :
				$custom_query_array[ $key ] = $value;
			endforeach; 

		elseif ( isset( $_GET ) && !empty( $_GET ) ) :

			foreach ( $_GET as $key => $value ) :
				$custom_query_array[ $key ] = $value;
			endforeach;

		endif;

		// Filter the custom query args
		$custom_query_array = apply_filters( 'br_rc_custom_query_array_filter',
											 $custom_query_array );

		// Set the main query according to custom arguments
		foreach ( $custom_query_array as $key => $value ) :
			$q->set( $key, $value);
		endforeach;

	// Taxonomy Page
	elseif (
	!is_admin() 
	&& $q->is_main_query() 
	&& $q->is_tax( 'br_rc_category' ) ) :

		// Get options and current layout
		$options 		= get_option( 'br_rc_settings_group' );
		$layout_type	= $options['br_rc_layout_type'];

		// Set number of posts per page
		// If fw and options setting is multiple of 4 (0 remainder)
		// Or if w/ sidebar, use options. Else use 8.
		$taxononmy_object 	= get_queried_object();
		$posts_per_page 	= ( 'full_width' != $layout_type 
								|| ( 'full_width' == $layout_type
									 && 0 == get_option( 'posts_per_page' ) % 4 ) )
							? get_option( 'posts_per_page' )
							: 8;
		$paged 				= ( get_query_var( 'paged' ) ) 
					  	  	? get_query_var( 'paged' ) 
					      	: 1;

		// If $_POST array exist, move values to $_SESSION array
        // Else, if it does not exist and on page 1, clear it
		if ( $_POST ) :
			foreach( $_POST as $key => $value ) :
				$_SESSION[ $key ] = $value;
			endforeach;
		elseif ( !$_POST && $paged < 2 ) :
			foreach ( $_SESSION as $key => $value ) :
				$_SESSION[ $key ] = '';
			endforeach;
		endif;

		// Vars
		$search_term	= ( isset( $_SESSION['br_rc_filter_search'] ) 
						&& !empty( $_SESSION['br_rc_filter_search'] ) )
					  ? sanitize_text_field( $_SESSION['br_rc_filter_search'] )
					  : '';

		$custom_query_array = array(
			'posts_per_page' 	=> $posts_per_page,
			'paged'				=> $paged,
			'post__in'          => br_rc_id_query(),
            'orderby'           => 'post__in',
            's'					=> $search_term,
            'br_rc_category'	=> $taxononmy_object->slug,
		);
	
		// Loop through POST or GET array and add values to custom query array
		if ( isset( $_SESSION ) && !empty( $_SESSION ) ) :

			foreach ( $_SESSION as $key => $value ) :
				if ( !empty( $_SESSION[ $key ] ) ) :
					$custom_query_array[ $key ] = $value;
				endif;
			endforeach; 

		elseif ( isset( $_GET ) && !empty( $_GET ) ) :

			foreach ( $_GET as $key => $value ) :
				$custom_query_array[ $key ] = $value;
			endforeach;

		endif;

		// Filter the custom query args
		$custom_query_array = apply_filters( 'br_rc_custom_query_array_filter',
											 $custom_query_array );

		// Set the main query according to custom arguments
		foreach ( $custom_query_array as $key => $value ) :
			$q->set( $key, $value);
		endforeach;
	endif;
}


/*--------------------------------------------------------------
?.? Output/Build Modules
---------------------------------------------------------------*/
/**
 * ?.? Build Pagination
 *
 * Builds pagination for use on index page 
 * Used in place of default wordpress pagination.
 *
 * @param   string 	    $pages 		
 * @param   interger 	$var 
 *
 * @return  string 	    $output 	Formatted pagination string
**/
function br_rc_pagination( $pages = '', $range = 1 ) {  
	
	// Vars
	$navigation_tokens = ( $range * 2 ) + 1; 
	
	global $paged;
	if ( empty( $paged ) ) :
		$paged = 1;
	endif;

	if ( $pages == '' ) :
		global $wp_query; 
		$pages = $wp_query->max_num_pages;
		if ( !$pages ) :
			$pages = 1;
		endif; 
	endif;   

	$output = '';

	// If more than one page
	if ( $pages != 1 ) :
		$output .= '<div class="text-center">'; 
		$output .= '<nav>';
		$output .= '<ul class="br-rc-pagination">';
		$output .= '<li class="disabled hidden-xs">';
		$output .= '<span>';
		$output .= '<span aria-hidden="true">';
		$output .= sprintf( __( "Page %1d of %2d", 'brainrider-resource-centre' ),
				   $paged, $pages );
		$output .= '</span>';
		$output .= '</span>';
		$output .= '</li>';

		// If on page greater than 2/5 and there is a sufficient number of pages,
		// show first link token
		if ( $paged > 2 && $paged > $range + 1 && $navigation_tokens < $pages ) :
			$output .= '<li>';
			$output .= '<a href="';
			$output .= get_pagenum_link( 1 );
			$output .= '" aria-label="First">';
			$output .= '&laquo;';
			$output .= '</a>';
			$output .= '</li>';
	 	endif;

	 	// If on page greater than 1 and there is a sufficient number of pages,
	 	// show previous link token
		if ( $paged > 1 && $navigation_tokens < $pages ) : 
			$output .= '<li>';
			$output .= '<a href="';
			$output .= get_pagenum_link( $paged - 1 );
		 	$output .= '" aria-label="Previous">';
		 	$output .= '&lsaquo;';
		 	$output .= '</a>';
		 	$output .= '</li>';
		endif;

		// Build numerical link tokens
		for ( $i = 1; $i <= $pages; $i++ ) :
			if ( !( $i >= $paged + $range + 1 
			|| $i <= $paged - $range - 1 ) 
			|| $pages <= $navigation_tokens ) :

	    		if ( $paged == $i ) :
	    			$output .= '<li class="active">';
	    			$output .= "<span>$i";
	    			$output .= '<span class="sr-only">(current)</span>';
	    			$output .= '</span>';
	    			$output .= '</li>';
    			else:
    				$output .= '<li>';
		    		$output .= "<a href='";
					$output .= get_pagenum_link( $i );
					$output .= "'>$i";
					$output .= '</a>';
					$output .= '</li>';
				endif;

			endif;
		endfor;

		// If not on last page of pages and there is a sufficient number of pages,
		// show next link token
		if ( $paged < $pages && $navigation_tokens < $pages ) :
			$output .= '<li>';
			$output .= '<a href="';
			$output .= get_pagenum_link( $paged + 1 );
			$output .= '"  aria-label="Next">';
			$output .= '&rsaquo;';
			$output .= '</a>';
			$output .= '</li>';  
		endif;

		// If not on last pages and there is a sufficient number of pages,
		// show last link token
		if ( $paged < $pages - 1 && $paged + $range - 1 < $pages 
		&& $navigation_tokens < $pages ) :
			$output .= '<li>';
			$output .= '<a href="';
			$output .= get_pagenum_link( $pages );
			$output .= '" aria-label="Last">';
			$output .= '&raquo;';
			$output .= '</a>';
			$output .= '</li>';
		endif;

		$output .= '</ul>';
		$output .= '</nav>';
		$output .= '</div>';

		// Filter pagination output
		$output = apply_filters( 'br_rc_pagination_filter', $output );

		// Return the output
		return $output;
	endif;
}


/**
 * ?.? Build Taxonomy Term Links
 *
 * Builds custom taxonomy term link output.
 * 
 *
 * @param   object	$post 				Current resource post
 * @param   array	$taxonomy_type		Specific taxonomy to return  
 *
 * @return  string 	$output 			HTML string of custom taxonomy links
**/
function br_rc_custom_taxonomies_terms_links( $post, $taxonomy_type = 'all' ) {

    // Get post type taxonomies
    $taxonomies = get_object_taxonomies( 'br_rc_resource', 'objects' );

    // Check for specific taxonomy selector (defaults to all)
    $taxonomies = ( $taxonomy_type != 'all' ) ? $taxonomies[ $taxonomy_type ] 
    										  : $taxonomies;

    $output = array();

    // Set build of output depending upon whether input is single taxonomy or all
    if ( is_array( $taxonomies ) ) :
	 	foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) :

	        // Get the terms related to post
	        $terms = get_the_terms( $post->ID, $taxonomy_slug );

	    	// Build links
	        if ( !empty( $terms ) ) :
	            foreach ( $terms as $term ) :
	                $output[] = sprintf(
				                	__( '<a href="%1$s">%2$s</a>', 
				                		'brainrider-resource-centre' ),
				                	esc_url( get_post_type_archive_link( 'br_rc_resource' ) 
				                			. $term->slug ),
				                	esc_html( $term->name ) 
			                	);
	            endforeach;
	        endif;

    	endforeach;
	else: 
		// Get the terms related to post
        $terms = get_the_terms( $post->ID, $taxonomy_type );

        if ( !empty( $terms ) ) :
            foreach ( $terms as $term ) :
                $output[] = sprintf( 
								__( '<a href="%1$s">%2$s</a>', 
									'brainrider-resource-centre' ),
								esc_url( get_post_type_archive_link( 'br_rc_resource' )
										. $term->slug ),
								esc_html( $term->name ) 
							);
            endforeach;
        endif;
	endif; 

	// Filter the output
	$output = apply_filters( 'br_rc_custom_taxonomies_terms_links_filter', $output );

	// Return the output
    return implode( ', ', $output );
}


/**
 * ?.? Output Custom Excerpt
 *
 * @param interger 	$length 	Excerpt length
 *
 * @return string 	$excerpt 	Custom post excerpt
**/
function br_rc_custom_excerpt( $length ) {

	// Get the excerpt
	$excerpt = get_the_excerpt();	

	// Format the returned excerpt
	$excerpt  		= preg_replace( '/\.{3} <a.+<\/a>/', '', $excerpt );
	$excerpt  		= substr( $excerpt, 0, $length );
	$trim_index_pos = strrpos( $excerpt, ' ' );
	$excerpt  		= substr( $excerpt, 0, $trim_index_pos );
	$excerpt 	   .= ' ...';

	// Apply filter to $excerpt output 
	$excerpt = apply_filters( 'br_rc_custom_excerpt_filter', $excerpt );

	// Echo the excerpt
	echo $excerpt;
}


/**
 * ?.? Output Filter Controls
 *
 * @param string 	$type 	  Type of filter to return (vertical/horizontal)
 *
 * @return string 	$output   HTML for form
**/
function br_rc_post_filter( $type='vertical' ) {

	// Setup
	// Vars
	$options 		= get_option( 'br_rc_settings_group' );
	$filter_title 	= $options['br_rc_filter_title'];
	$filter_cta 	= $options['br_rc_filter_cta'];
	$search_filter	= $options['br_rc_filter_search_toggle'];
	$search_term	= ( isset( $_SESSION['br_rc_filter_search'] ) 
					  && !empty( $_SESSION['br_rc_filter_search'] ) )
					  ?  sanitize_text_field( $_SESSION['br_rc_filter_search'] )
					  : '';

	// Grab br_rc_resource taxonomies
	$br_rc_taxonomies = get_object_taxonomies( 'br_rc_resource', 'object' );

	// Create empty terms array
	$br_rc_terms_array = array();

	// Loop through the taxonomies, and push their individual terms to terms array
	foreach ( $br_rc_taxonomies as $br_rc_taxonomy_item ) :

		$br_rc_terms_array[ $br_rc_taxonomy_item->label ] = get_terms( array( 
															'taxonomy'   => $br_rc_taxonomy_item->name,
															'hide_empty' => false,
														) );
	endforeach;
	
	// Output
	// Build the options
	// Create select element for each taxonomy
	$select_options = '';

	// Count inputs (with possible offset for search) to assign class
	$input_count 	= ( $search_filter )
					  ? count( $br_rc_terms_array ) + 1 
					  : count( $br_rc_terms_array );

	foreach ( $br_rc_terms_array as $key => $value ) :
		$name = $value[0]->taxonomy;
		$select_options .= "<select id='$key' name='$name' ";
		$select_options .= "class='br-rc-filter-input br-rc-filter-input-count-"; 
		$select_options .= $input_count;
		$select_options .= sprintf( __( "'><option value=''>All %s</option>",
										 'brainrider-resource-centre' ), $key );

		// Create option for each taxonomy term
		foreach ( $br_rc_terms_array[ $key ] as $term ) :

			// Set filtered_by variable for use in selected check below
		   	if ( isset ($_POST[ $name ] ) && !empty( $_POST[ $name ] ) ) :
		   		$filtered_by = $_POST[ $name ];
		   	elseif ( isset( $_GET[ $name ] ) && !empty( $_GET[ $name ] ) ) :
		   		$filtered_by = $_GET[ $name ];
		   	elseif ( isset( $_SESSION[ $name ] ) && !empty( $_SESSION[ $name ] ) ) :
		   		$filtered_by = $_SESSION[ $name ];
	   		elseif ( is_tax() ) :
	   			$filtered_by = get_queried_object()->slug;
   			else:
   				$filtered_by = '';
			endif;

			$select_options .= '<option value="';
			$select_options .= $term->slug;
			$select_options .= '"';
			$select_options .= selected( $filtered_by, $term->slug, false );
			$select_options .= '>';
			$select_options .= $term->name;
			$select_options .= '</option>';

		endforeach;

		$select_options .= '</select>';
	endforeach;

	// If activated, build the search input
	if ( $search_filter ) :
		$search_input  = '<input type="search" name="br_rc_filter_search"';
		$search_input .= 'class="br-rc-filter-input br-rc-filter-input-count-';
		$search_input .= $input_count;
		$search_input .= '" value="';
		$search_input .=  esc_attr( $search_term );
		$search_input .= '" placeholder="';
		$search_input .= __( 'Filter by keyword...', 
							'brainrider-resource-centre' );
		$search_input .= '" />';
	endif;

	// Vertical filter
	if ( $type == 'vertical' ) :
		$output  = '<div class="br-rc-filter-controls vertical">';
		$output .= '<h2>' . esc_html( $filter_title ) . '</h2>';
		$output .= '<form id="br-rc-filter" method="post" action="' 
					. get_post_type_archive_link('br_rc_resource') . '">';
		$output .= $select_options;
		if ( $search_filter ) :
			$output .= $search_input;
		endif;
		$output .= '<button type="submit">' . esc_html( $filter_cta );
		$output .= '</button>';
		$output .= '</form>';
		$output .= '</div>';

	// Horizontal filter
	else:
		$output  = '<div class="br-rc-filter-controls horizontal">';
		$output .= '<div class="container">';
		$output .= '<form id="br-rc-filter" method="post" action="' 
					. get_post_type_archive_link('br_rc_resource') . '">';
		$output .= '<div class="row">';
		$output .= '<div class="col-md-9">';
		$output .= $select_options;
		if ( $search_filter ) :
			$output .= $search_input;
		endif;
		$output .= '</div>';
		$output .= '<div class="col-md-3">';
		$output .= '<button type="submit">' . esc_html( $filter_cta );
		$output .= '</button>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';
		$output .='</div>';

	endif;

	// Filter the html output
	$output = apply_filters( 'br_rc_post_filter_filter', $output );

	// Return html.
	return $output;
}


/**
 * ?.? Output Resource Type Icon/Label
 *
 * @param  object		 $post 			Current post object
 * @param  string 		 $return_value 	'all', 'icon', 'label', or 'slug'
 * 
 * @return string 		 $resource_type_callout_label	Label
 *		   string		 $resource_type_callout_icon	Icon
 *		   string 		 $resource_type_slug 			Slug
 *		   array	     $resource_type_array			Label, icon, and slug
 *
**/
function br_rc_callout_output( $post, $return_value = 'all' ) {
	$format_taxonomy_term = wp_get_post_terms( 
								$post->ID, 
								'br_rc_format',
								array(
							  		'fields' => 'slugs'
						  		) 
					  		);
	$resource_type_slug = ( !empty( $format_taxonomy_term ) ) 
							? $format_taxonomy_term[0] 
							: false;

	// Switch case to set resource type callout icon/label
	switch( $resource_type_slug ) :
		case 'case-studies':
			$resource_type_callout_icon  = 'fa-id-card';
			$resource_type_callout_label = __( 'Case Studies', 
											   'brainrider-resource-centre' );
			break;
		case 'examples':
			$resource_type_callout_icon  = 'fa-bookmark';
			$resource_type_callout_label = __( 'Examples', 
											   'brainrider-resource-centre' );
			break;
		case 'how-to-guides':
			$resource_type_callout_icon  = 'fa-book';
			$resource_type_callout_label = __( 'How-to Guides', 
											   'brainrider-resource-centre' );
			break;
		case 'infographics':
			$resource_type_callout_icon  = 'fa-pie-chart';
			$resource_type_callout_label = __( 'Infographics', 
											   'brainrider-resource-centre' );
			break;
		case 'videos':
			$resource_type_callout_icon  = 'fa-video-camera';
			$resource_type_callout_label = __( 'Videos', 
										   	   'brainrider-resource-centre' );
			break;
		case 'slideshares':
			$resource_type_callout_icon  = 'fa-slideshare';
			$resource_type_callout_label = __( 'Slideshares', 
										       'brainrider-resource-centre' );
			break;
		case 'tips-best-practices':
			$resource_type_callout_icon  = 'fa-trophy';
			$resource_type_callout_label = __( 'Tips & Best Practices', 
											   'brainrider-resource-centre' );
			break;
		case 'tools-templates':
			$resource_type_callout_icon  = 'fa-wrench';
			$resource_type_callout_label = __( 'Tools & Templates', 
											   'brainrider-resource-centre' );
			break;
		case 'webinars':
			$resource_type_callout_icon  = 'fa-calendar';
			$resource_type_callout_label = __( 'Webinars', 
											   'brainrider-resource-centre' );
			break;
		case 'whitepapers':
			$resource_type_callout_icon  = 'fa-file-text';
			$resource_type_callout_label = __( 'Whitepapers', 
											   'brainrider-resource-centre' );
			break;
		default: 
			$resource_type_callout_icon  = 'fa-file';
			$resource_type_callout_label = __( 'Other', 
											   'brainrider-resource-centre' );
	endswitch;

	if ( $return_value == 'all' ) :
		$resource_type_array = array(
			'label' => $resource_type_callout_label, 
			'icon'  => $resource_type_callout_icon,
			'slug'  => $resource_type_slug,
		);
		return $resource_type_object;
	elseif ( $return_value == 'icon' ) :
		return $resource_type_callout_icon;
	elseif ( $return_value == 'label' ) :
		return $resource_type_callout_label;
	elseif ( $return_value == 'slug' ) :
		return $resource_type_slug;
	endif;
}


/**
 * ?.? Output No Results Terms
 *
 * 
 * @return string 	$output 	No content message containing filter term 
 * 								removal links
 * 				    
**/
function br_rc_no_results_terms() {
	
	// Create empty array
	$filter_terms_array = array();

	// Grab the query params (GET or POST)
	if ( $_POST ) :
		$query_array = $_POST; 

		// Remove search query from array
		unset( $query_array['br_rc_filter_search'] );

	elseif ( $_GET ) :

		// Grab the queried object and add to get array
		// to accomodate category urls with params
		$queried_object 	= get_queried_object();
		$taxonomy 			= $queried_object->taxonomy;
		$term 	  			= $queried_object->slug;
		$_GET[ $taxonomy ] 	= $term;

		$query_array 		= $_GET;
	else: 
		$output = __( 'No terms to clear.', 'brainrider-resource-centre' );
		return $output;
	endif;

	// If search term exists in post array, grab it
	if ( isset( $_POST['br_rc_filter_search'] ) 
	&& !empty($_POST['br_rc_filter_search'] ) ) :
		$search_term = sanitize_text_field( $_POST['br_rc_filter_search'] );
	else:
		$search_term = false;
	endif;

	// Get term object and push values onto array
	foreach( $query_array as $key => $value ) :
		$term_object = get_term_by( 'slug', $value, $key );

		if ( is_object( $term_object ) ) :
			$filter_terms_array[ $term_object->slug ] = $term_object;
		endif;

	endforeach;

	// Build the output
	$output  = '<h2>';
	$output .= __( 'Sorry, No Content Found For:', 'brainrider-resource-centre' );
	$output .= '</h2>';
	$output .= '<div class="br-rc-searched-terms-terms">';

	// Search query button/form
	if ( $search_term ) :
		$output .= '<form action="';
		$output .= get_post_type_archive_link( 'br_rc_resource' );
		if ( isset( $_POST[ 'br_rc_category' ] ) ) : 
			$output .= esc_attr( $_POST['br_rc_category'] );
		endif;
		$output .= '" method ="POST">';

		foreach( $filter_terms_array as $field_slug => $field_term_object ) :
			$output .= '<input type="hidden" name="';
			$output .= esc_attr( $field_term_object->taxonomy );
			$output .= '" value="';
			$output .= esc_attr( $field_slug );
			$output .= '" />';
		endforeach;

		$output .= '<input type="hidden" name="br_rc_filter_search" value="" />';

		$output .= '<button>';
		$output .= __( 'Search Term: ', 'brainrider-resource-centre' );
		$output .=  '\'' . esc_html( $search_term ) . '\'';
		$output .= '<span class="fa fa-times"></span>';
		$output .= '</button>';
		$output .= '</form>';
	endif;

	foreach ( $filter_terms_array as $slug => $term_object ) :

		// Build the individual forms (to submit to $_POST array)
		$output .= '<form action="';
		$output .= get_post_type_archive_link( 'br_rc_resource' );

		// If current item is not category and category value exist  
		// in $_POST array, append value to action
		if ( $term_object->taxonomy != 'br_rc_category' 
		&& isset( $_POST[ 'br_rc_category' ] ) ) :

			$output .= $_POST['br_rc_category'];

		endif;
		$output .= '" method="POST">';

		// Loop though available taxonomies and build hidden fields for form
		// Only include field if is not the current button 
		// (i.e. to prevent submission to $_POST)
		foreach( $filter_terms_array as $field_slug => $field_term_object ) :
			if ( $field_slug != $slug ) :
				$output .= '<input type="hidden" name="';
				$output .= esc_attr( $field_term_object->taxonomy );
				$output .= '" value="';
				$output .= esc_attr( $field_slug );
				$output .= '" />';
			else:
				$output .= '<input type="hidden" name="';
				$output .= esc_attr( $field_term_object->taxonomy );
				$output .= '" value="" />'; 
			endif;
		endforeach;

		// If search terms set, include hidden field
		if ( $search_term ) :
			$output .= '<input type="hidden" name="br_rc_filter_search" value="';
			$output .= esc_attr( $search_term );
			$output .= '" />';
		endif;
		$output .= '<button>';

		// Include button label
		$output .= $term_object->name;
		$output .= '<span class="fa fa-times"></span>';
		$output .= '</button>';
		$output .= '</form>';
	endforeach;

	$output .= '</div>';
	$output .= '<h3>';
	$output .= __( 'Try removing a filter or taking a look at some of our more
					recent resources below.','brainrider-resource-centre' );
	$output .= '</h3>';
	$output .= '<h3 id="clear-all">';
	$output .= 'Alternatively,';
	$output .= '<form action="';
	$output .= get_post_type_archive_link( 'br_rc_resource' );
	$output .= '" method="POST">';
	foreach( $filter_terms_array as $field_slug => $field_term_object ) :
		$output .= '<input type="hidden" name="';
		$output .= esc_attr( $field_term_object->taxonomy );
		$output .= '" value="" />';
	endforeach;
	if ( $search_term ) :
		$output .= '<input type="hidden" name="br_rc_filter_search" value="" />';
	endif;
	$output .= '<button>';
	$output .= 'Clear All Filters';
	$output .= '</button>';
	$output .= '</form>';
	$output .= 'to return to all available resources.';


	// Filter the output
	$output = apply_filters( 'br_rc_no_results_terms_filter', $output );

	// Return output
	return $output;
}


/**
 * ?.? Output Title with Title Case Styles
 *
 * Output $title in title case.
 *
 * @param  string 	$title		The title to be restyled.
 *
 * @return string 	$output 	The restyled title.
 * 								
 * 				    
**/
function br_rc_string_to_title( $title ) {

	// Build array containing exception words
	$smallwordsarray = array(
		__( 'of', 'brainrider-resource-centre' ),
		__( 'a', 'brainrider-resource-centre' ),
		__( 'the', 'brainrider-resource-centre' ),
		__( 'and', 'brainrider-resource-centre' ),
		__( 'an', 'brainrider-resource-centre' ),
		__( 'or', 'brainrider-resource-centre' ),
		__( 'nor', 'brainrider-resource-centre' ),
		__( 'is', 'brainrider-resource-centre' ),
		__( 'if', 'brainrider-resource-centre' ),
		__( 'then', 'brainrider-resource-centre' ),
		__( 'else', 'brainrider-resource-centre' ),
		__( 'when', 'brainrider-resource-centre' ),
		__( 'at', 'brainrider-resource-centre' ),
		__( 'from', 'brainrider-resource-centre' ),
		__( 'by', 'brainrider-resource-centre' ),
		__( 'on', 'brainrider-resource-centre' ),
		__( 'off', 'brainrider-resource-centre' ),
		__( 'for', 'brainrider-resource-centre' ),
		__( 'in', 'brainrider-resource-centre' ),
		__( 'out', 'brainrider-resource-centre' ),
		__( 'over', 'brainrider-resource-centre' ),
		__( 'to', 'brainrider-resource-centre' ),
		__( 'into', 'brainrider-resource-centre' ),
		__( 'with', 'brainrider-resource-centre' ),
		__( 'but', 'brainrider-resource-centre' ),
		__( 'that', 'brainrider-resource-centre' ),
	);

	// Turn $title into array
	$words = explode( ' ', $title );

	// Loop through title array
	// If words aren't start of title and are in array, make sure lower case
	// If words aren't in exception or array or are at start of title, capatalize
	foreach ( $words as $key => $word ) :
		if ( $key != 0 && in_array( lcfirst( $word ) , $smallwordsarray) ) :
			$words[ $key ] = lcfirst( $word );
		elseif ( $key == 0 || !in_array( $word, $smallwordsarray ) ) :
			$words[ $key ] = ucwords( $word );
		endif;
	endforeach;

	// Rebuild string
	$output = implode( ' ', $words );

	// Return the new title
	return $output;
}