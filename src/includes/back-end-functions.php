<?php
/**
 * Back-end Functions
 * 
 * Functions used in the back-end/admin area of Wordpress.
 *
**/

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Test Area Setup
    ?.? Set up Test Area
?.? Post Setup
    ?.? Register Resource Centre Post Type
    ?.? Register Custom Taxonomies
    ?.? Set Taxonomy Slug Rewrites
    ?.? Flush Rewrite Rules
    ?.? Add Custom Columns and Filtering (Featured Resource)
    ?.? Add Custom Field to Term Edit Screen
    ?.? Add Custom Field to New Term Screen
    ?.? Save Custom Term Field Values
    ?.? Inialize Custom Term Field Hooks
?.? Metabox Setup
    ?.? Remove Extraneous Metaboxes
    ?.? Create Additional Required Metaboxes
    ?.? Create Pardot Metabox Output
    ?.? Create Transcript Metabox Output
    ?.? Create Featured Resource Toggle
    ?.? Update Postmeta Values
?.? Setings Page Setup
    ?.? Add BR RC Settings Page
    ?.? Validate Plugin Settings Values
    ?.? Create Plugin Setting's Page
    ?.? Output Plugin Setting's Page
    ?.? Set Default Plugin Options
    ?.? Remove Plugin Database Options (Disabled)
?.? Styles/Scripts Setup
    ?.? Enqueue Plugin Stylsheets
?.? Chron Jobs
    ?.? Register Daily Pardot Sync


/*--------------------------------------------------------------
?.? Test Area Setup
--------------------------------------------------------------*/
/**
 * ?.?. Set up Test Area
 *
 * Echoes output in test metabox module.
 *
**/
function br_rc_test_metabox_output() {
    
    // Content here
    echo "Test content appears below:";

    // Test validatio bug
    $pardot_options     = get_option( 'br_rc_pardot_settings_group' );
    $options            = get_option( 'br_rc_settings_group' ); 

    $user_key           = $options['br_rc_pardot_user_key'];
    $api_array          = $pardot_options['br_rc_pardot_api_key'];  
    $expiry_time        = 'API key will expire at: ' 
                          . date( "Y-m-d H:i:s", 
                                  $pardot_options['br_rc_pardot_api_key']['expiry_timestamp'] );
    $current_time       = 'Current time is: ' . date( "Y-m-d H:i:s", time() );
    
    var_dump( $expiry_time );
    var_dump( $current_time );
    var_dump( $current_time < $expiry_time );
}


/*--------------------------------------------------------------
?.? Post Setup
--------------------------------------------------------------*/
/**
 * ?.?. Register Resource Centre Post Type
 *
 * Registers a custom post type with labels and rewrites permalink.
 *
 * @param   array   $labels     Various classifying titles/names
 * @param   array   $rewrite    Data detailing permalink rewrite structure
 * @param   array   $args       Options used to create custom post type
**/
function br_rc_register_post_type() {

    $labels = array(
        'name'                  => _x( 'Resources', 'Post Type General Name', 
                                       'brainrider-resource-centre' ),
        'singular_name'         => _x( 'Resource', 'Post Type Singular Name',
                                       'brainrider-resource-centre' ),
        'menu_name'             => __( 'Resources', 
                                       'brainrider-resource-centre' ),
        'name_admin_bar'        => __( 'Resource', 
                                       'brainrider-resource-centre' ),
        'archives'              => __( 'Resource Archives', 
                                       'brainrider-resource-centre' ),
        'parent_item_colon'     => __( 'Parent Resource:', 
                                       'brainrider-resource-centre' ),
        'all_items'             => __( 'All Resources', 
                                       'brainrider-resource-centre' ),
        'add_new_item'          => __( 'Add New Resource', 
                                       'brainrider-resource-centre' ),
        'add_new'               => __( 'Add New', 
                                       'brainrider-resource-centre' ),
        'new_item'              => __( 'New Resource', 
                                       'brainrider-resource-centre' ),
        'edit_item'             => __( 'Edit Resource', 
                                       'brainrider-resource-centre' ),
        'update_item'           => __( 'Update Resource', 
                                       'brainrider-resource-centre' ),
        'view_item'             => __( 'View Resource', 
                                       'brainrider-resource-centre' ),
        'search_items'          => __( 'Search Resource', 
                                       'brainrider-resource-centre' ),
        'not_found'             => __( 'Not found',
                                       'brainrider-resource-centre' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 
                                       'brainrider-resource-centre' ),
        'featured_image'        => __( 'Featured Image', 
                                       'brainrider-resource-centre' ),
        'set_featured_image'    => __( 'Set featured image', 
                                       'brainrider-resource-centre' ),
        'remove_featured_image' => __( 'Remove featured image', 
                                       'brainrider-resource-centre' ),
        'use_featured_image'    => __( 'Use as featured image', 
                                       'brainrider-resource-centre' ),
        'insert_into_item'      => __( 'Insert into resource', 
                                       'brainrider-resource-centre' ),
        'uploaded_to_this_item' => __( 'Uploaded to this resource', 
                                       'brainrider-resource-centre' ),
        'items_list'            => __( 'Resource list', 
                                       'brainrider-resource-centre' ),
        'items_list_navigation' => __( 'Resources list navigation', 
                                       'brainrider-resource-centre' ),
        'filter_items_list'     => __( 'Filter resources list', 
                                       'brainrider-resource-centre' ),
    );
    $rewrite = array(
        'slug'                  => 'resources',
        'with_front'            => false,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Resources', 
                                       'brainrider-resource-centre' ),
        'description'           => __( 'An informative, useful, and educational
                                       resource material.',
                                       'brainrider-resource-centre' ),
        'labels'                => $labels,
        'supports'              => array(
                                    'title',
                                    'editor',
                                    'excerpt',
                                    'thumbnail',
                                    'revisions',
                                    'page-attributes',
                                 ),
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-archive',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'resources',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'post',
    );

    // Filter the arguments
    $args = apply_filters( 'br_rc_register_post_type_args_filter', $args );

    // Register the post type
    register_post_type( 'br_rc_resource', $args );
}


/**
 * ?.? Register Custom Taxonomies
 *
 * Sets up 'Categories' and 'Format' taxonomies and loads default terms for
 * the latter.
 * 
**/
function br_rc_register_taxonomies() {

    $taxonomies = array(

        // Categories (Custom)
        array(
            'slug'          => 'br_rc_category',
            'single_name'   => 'Category',
            'plural_name'   => 'Categories',
            'post_type'     => 'br_rc_resource',
            'rewrite'       => array( 'slug' => 'categories'),
        ),
        
        // Format
        array(
            'slug'          => 'br_rc_format',
            'single_name'   => 'Format',
            'plural_name'   => 'Formats',
            'post_type'     => 'br_rc_resource',
            'rewrite'       => array( 'slug' => 'formats' ),
        ),
    );

    // Filter the $taxonomies array
    $taxonomies = apply_filters( 'br_rc_taxonomies_array_filter', $taxonomies );

    foreach ( $taxonomies as $taxonomy ) :
        $labels = array(
            'name'              => $taxonomy['plural_name'],
            'singular_name'     => $taxonomy['single_name'],
            'search_items'      => 'Search ' . $taxonomy['plural_name'],
            'all_items'         => 'All' . $taxonomy['single_name'],
            'parent_item'       => 'Parent ' . $taxonomy['single_name'],
            'parent_item_colon' => 'Parent ' . $taxonomy['single_name'] . ':',
            'edit_item'         => 'Edit ' . $taxonomy['single_name'],
            'update_item'       => 'Update ' . $taxonomy['single_name'],
            'add_new_item'      => 'Add New ' . $taxonomy['single_name'],
            'new_item_name'     => 'New ' . $taxonomy['single_name'] . 'Name',
            'menu_name'         => $taxonomy['plural_name'],
        );

        $rewrite = isset( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : 
                    array( 'slug' => $taxonomy['slug'] );
        $hierarchical = isset( $taxonomy['hierarchical'] ) 
                             ? $taxonomy['hierarchical']
                             : true;

        register_taxonomy( $taxonomy['slug'], $taxonomy['post_type'], array(
            'hierarchical'  => $hierarchical,
            'labels'        => $labels,
            'show_ui'       => true,
            'query_var'     => true,
            'rewrite'       => $rewrite,
        ) );
    endforeach;

    // Setup default taxonomy terms
    $default_format_terms_array = array(
                                    __( 'Videos', 
                                        'brainrider-resource-centre' ),
                                    __( 'Whitepapers', 
                                        'brainrider-resource-centre' ),
                                    __( 'Infographics', 
                                        'brainrider-resource-centre' ),
                                    __( 'Webinars', 
                                        'brainrider-resource-centre' ),
                                    __( 'Case Studies', 
                                        'brainrider-resource-centre' ),
                                    __( 'Examples', 
                                        'brainrider-resource-centre' ),
                                    __( 'How-to Guides', 
                                        'brainrider-resource-centre' ),
                                    __( 'Slideshares', 
                                        'brainrider-resource-centre' ),
                                    __( 'Tips & Best Practices', 
                                        'brainrider-resource-centre' ),
                                    __( 'Tools & Templates', 
                                        'brainrider-resource-centre' ),
                                 );

    // Filter the default format terms
    $default_format_terms_array = apply_filters( 
                                    'br_rc_default_format_terms_filter', 
                                    $default_format_terms_array 
                                );

    foreach ( $default_format_terms_array as $format_term ) :

        // Insert the term and store the return value (term ID and taxonomy ID)
        $term_id_array = wp_insert_term( $format_term, 'br_rc_format' );

        // If it return our ID array (and not WP_Error)
        if ( is_array( $term_id_array ) ) :
        
            // Grab the ID
            $term_id = $term_id_array['term_id'];

            // Get the options for the item
            $term_meta  = get_option( "taxonomy_$term_id" );

            // Initialize it's Pardot tracking as true
            $term_meta['pardot_tracking'] = 1;

            // Update the options
            update_option( "taxonomy_$term_id", $term_meta );
        endif;
    endforeach;
}


/**
 * ?.? Set Taxonomy Slug Rewrites
 * 
 * Rewrites taxonomy archive pages slugs (i.e. '/resources/taxonomy-term'),
 * including paginated pages.
 * 
 * @param array     $wp_rewrite     Default Wordpress rewrite rules.
 *
**/
add_filter( 'generate_rewrite_rules', 'br_rc_slug_rewrite' );
function br_rc_slug_rewrite( $wp_rewrite ) {

    // Create empty arrays for new taxonomy terms and rules
    $rules = array();
    $taxononmy_term_objects = array();

    // Get the taxonomy objects
    $custom_taxonomies = get_object_taxonomies( 'br_rc_resource', 'objects' );

    // Loop through each object
    foreach ( $custom_taxonomies as $custom_taxonomy ) :

        // Get the associated terms
        $terms = get_terms( array(
            'taxonomy'  => $custom_taxonomy->name,
        ) );

        // add each term object to array
        foreach ( $terms as $term ) :
            $taxononmy_term_objects[] = $term;
        endforeach;

    endforeach;

    // Create rewrite rules for each term (including its pagination)
    foreach ( $taxononmy_term_objects as $taxononmy_term_object ) :
        
        // Base URL
        $rules['resources' 
               . '/' 
               . $taxononmy_term_object->slug 
               . '/?$'] = 'index.php?' 
                        . $taxononmy_term_object->taxonomy 
                        . '=' 
                        . $taxononmy_term_object->slug;

        // Paginated
        $rules['resources' 
                . '/' 
                . $taxononmy_term_object->slug 
                . '/page/([0-9]+)/?'] = 'index.php?' 
                                      . $taxononmy_term_object->taxonomy 
                                      . '=' 
                                      . $taxononmy_term_object->slug 
                                      . '&paged=$matches[1]';
    endforeach;

    // Filter the rewrite rules
    $rules = apply_filters( 'br_rc_rewrite_rules_filter', $rules );

    // Merge new rewrite rules with global object
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}


/**
 * ?.? Flush Rewrite Rules
 *
 * Flushes permalinks after creation of custom post type.
 * Runs on activation and deactivation.
*/
function br_rc_flush_rewrite() {

    br_rc_register_post_type();
    br_rc_register_taxonomies();
    flush_rewrite_rules();

    // Execute function hook
    do_action( 'br_rc_flush_rewrite_action' );
}


/**
 * ?.? Add Custom Columns and Filtering
 *
 * Adss custom column to admin dashboard.
 * Allows for filtering by featured resource and/or pardot form.
*/

// Add column headers
add_filter( 'manage_br_rc_resource_posts_columns', 
            'br_rc_custom_columns_head', 10);
function br_rc_custom_columns_head( $defaults ) {
    $defaults['featured_resource']  = 'Featured Resource';
    $defaults['pardot_form']        = 'Pardot Form';
    return $defaults;
}

// Add column content
add_action( 'manage_br_rc_resource_posts_custom_column', 
            'br_rc_custom_columns_content', 
            10,
            2);
function br_rc_custom_columns_content( $column_name, $post_ID ) {

    // Get the post meta
    $post_meta = get_post_meta( $post_ID );

    // Featured Resource
    if ( $column_name == 'featured_resource' ) :

        $featured_resource_toggle = isset( $post_meta['_br_rc_featured_resource_toggle'] ) ?: '';
        if ( $featured_resource_toggle ) :
            echo '<i class="fa fa-lg fa-star"></i>';
        endif;

    // Pardot Form
    elseif ( $column_name == 'pardot_form' ) :

        $pardot_form = isset( $post_meta['_br_rc_pardot_form_url'] )
                        && ! in_array( '', $post_meta['_br_rc_pardot_form_url'] );
                        
        if ( $pardot_form ) :

            // Decode JSON data and get values from returned associative array
            $pardot_form_url_data = json_decode( 
                                        $post_meta['_br_rc_pardot_form_url'][0],
                                        true );
            $pardot_form_id     = $pardot_form_url_data['id'];
            $pardot_form_label  = $pardot_form_url_data['label'];
            $pardot_form_url    = $pardot_form_url_data['url'];            

            // Pardot form edit link
            $pardot_form_edit_link  = 'https://pi.pardot.com/form/read/id/';
            $pardot_form_edit_link .= $pardot_form_id;

            // Pardot form report link
            $pardot_form_report_link  = 'https://pi.pardot.com/form/readReport/id/';
            $pardot_form_report_link .= $pardot_form_id;

            // Build output
            $output  = '<span class="pardot-column-label">';
            $output .= $pardot_form_label;
            $output .= '</span>';
            $output .= '<span class="pardot-column-actions">';
            $output .= '<a href="';
            $output .= $pardot_form_url;
            $output .= '" target="_blank">View Online</a>';
            $output .= ' | ';
            $output .= '<a href="';
            $output .= $pardot_form_edit_link;
            $output .= '" target="_blank">Edit Form</a>';
            $output .= ' | ';
            $output .= '<a href="';
            $output .= $pardot_form_report_link;
            $output .= '" target="_blank">View Report</a>';
            $output .= '</span>';

            // Echo output
            echo $output;
        endif;
    endif;
}

// Featured Resource
// Add dropdown filter
add_action( 'restrict_manage_posts', 'br_rc_add_featured_resource_filter' );
function br_rc_add_featured_resource_filter( $post_type ) {

    // Access the $wpdb object
    global $wpdb;

    // If we're not dealing w/ a resource, return
    if ( $post_type !== 'br_rc_resource' ) :
        return;
    endif;

    // Create new query
    $query = $wpdb->prepare('
        SELECT DISTINCT pm.meta_value FROM %1$s pm
        LEFT JOIN %2$s p ON p.ID = pm.post_id
        WHERE pm.meta_key = "%3$s"
        AND p.post_status = "%4$s"
        AND p.post_type = "%5$s"
        ORDER BY "%3$s"',
        $wpdb->postmeta,
        $wpdb->posts,
        '_br_rc_featured_resource_toggle',
        'publish',
        $post_type
    );
    $results = $wpdb->get_col( $query );

    // If we've got no results, return
    if ( empty( $results ) ) :
        return;
    endif;

    $featured_resource_value = isset( $_GET['featured-resource-toggle'] ) 
                               ? $_GET['featured-resource-toggle'] 
                               : '';

    // Build the options
    $options[] = sprintf( '<option value="">%1$s</option>', 
                          __( 'All Resources', 
                          'brainrider-resource-centre' ) 
                        );

    foreach( $results as $result ) :
        $options[] = sprintf( '<option value="%1$s"' 
                              . selected( $result, 
                                          $featured_resource_value, 
                                          false ) 
                              . '>%2$s</option>',
                              esc_attr( $result ),
                              'Featured Resources' );
    endforeach;

    // Build the output
    $output  = '<select class=""';
    $output .= 'id="featured-resource-toggle"';
    $output .= ' name="featured-resource-toggle">';
    $output .= join( "\n", $options );
    $output .= '</select>';

    // Echo the output
    echo $output;
}

// Pardot Form
// Add dropdown filter
add_action( 'restrict_manage_posts', 'br_rc_add_pardot_form_filter' );
function br_rc_add_pardot_form_filter( $post_type ) {

    // Access the $wpdb object
    global $wpdb;

    // If we're not dealing w/ a resource, return
    if ( $post_type !== 'br_rc_resource' ) :
        return;
    endif;

    // Create new query
    $query = $wpdb->prepare('
        SELECT DISTINCT pm.meta_value FROM %1$s pm
        LEFT JOIN %2$s p ON p.ID = pm.post_id
        WHERE pm.meta_key = "%3$s"
        AND p.post_status = "%4$s"
        AND p.post_type = "%5$s"
        ORDER BY "%3$s"',
        $wpdb->postmeta,
        $wpdb->posts,
        '_br_rc_pardot_form_url',
        'publish',
        $post_type
    );
    $results = $wpdb->get_col( $query );

    // If we've got no results, return
    if ( empty( $results ) ) :
        return;
    endif;

    $pardot_form_value = isset( $_GET['pardot-form-toggle'] ) 
                         && '' != $_GET['pardot-form-toggle'] 
                         ? 1 
                         : '';

    // Build the options
    $options[] = sprintf( '<option value="">%1$s</option>', 
                          __( 'All Form Types', 
                          'brainrider-resource-centre' ) 
                        );

    $options[] = sprintf( '<option value="1"'
                            . selected( 1,
                                        $pardot_form_value,
                                        false ) 
                            . '>%1$s</option>',
                           __( 'Pardot-enabled Resources' ) );
    
    // Build the output
    $output  = '<select class=""';
    $output .= 'id="pardot-form-toggle"';
    $output .= ' name="pardot-form-toggle">';
    $output .= join( "\n", $options );
    $output .= '</select>';

    // Echo the output
    echo $output;
}

// Filter resource posts results based on filter value
add_filter( 'parse_query', 'br_rc_parse_filters' );
function br_rc_parse_filters( $query ) {
    global $pagenow;
    $current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

    // If we're in the admin, and the current page is the resource post page
    // and the toggle(s) are set...
    if ( is_admin()
        && 'br_rc_resource' == $current_page
        && 'edit.php' == $pagenow
        && ( isset( $_GET['pardot-form-toggle'] ) 
            || isset( $_GET['featured-resource-toggle'] ) ) ) :

        // If filtering by featured resource only...
        if ( $_GET['featured-resource-toggle'] != ''
            && $_GET['pardot-form-toggle'] == '' ) :

            // Adjust query accordingly
            $featured_resource_value = $_GET['featured-resource-toggle'];
            $query->query_vars['meta_query'] = array(
                                                    array(
                                                        'key'       => '_br_rc_featured_resource_toggle',
                                                        'compare'   => '=',
                                                        'value'     => $featured_resource_value
                                                    )
                                                );

        // If filtering by pardot form only...
        elseif ( $_GET['pardot-form-toggle'] != '' 
            && $_GET['featured-resource-toggle'] == '' ) :

            // Adjust query accordingly
            $query->query_vars['meta_query'] = array(
                                                    array(
                                                        'key'       => '_br_rc_pardot_form_url',
                                                        'compare'   => 'EXISTS'
                                                    ),
                                                    array(
                                                        'key'       => '_br_rc_pardot_form_url',
                                                        'compare'   => '!=',
                                                        'value'     => ''
                                                    )
                                                );
        
        // If filtering by both featured resource and pardot form...
        elseif ( $_GET['featured-resource-toggle'] != ''
            && $_GET['pardot-form-toggle'] != '' ) :

            // Adjust query accordingly
            $featured_resource_value = $_GET['featured-resource-toggle'];
            $query->query_vars['meta_query'] = array(
                                                    array(
                                                        'key'       => '_br_rc_featured_resource_toggle',
                                                        'compare'   => '=',
                                                        'value'     => $featured_resource_value
                                                        
                                                    ),
                                                    array(
                                                        'key'       => '_br_rc_pardot_form_url',
                                                        'compare'   => 'EXISTS'
                                                    ),
                                                    array(
                                                        'key'       => '_br_rc_pardot_form_url',
                                                        'compare'   => '!=',
                                                        'value'     => ''
                                                    )
                                                );
        endif;
    endif; 
}


/**
 * ?.? Add Custom Field to Term Edit Screen
 * 
 * Adds Pardot tracking toggle to term edit screen.
 *
 * @param   object      $tag        Current taxonomy term object.
 * @param   string      $taxonmy    Current taxonomy slug.
 *
**/
function br_rc_edit_term_custom_fields( $tag, $taxonomy ) {

    // Get the term ID and then load any existing term options
    // Used to set preexisting values
    $term_id    = $tag->term_id;
    $term_meta  = get_option( "taxonomy_$term_id" );

    // HTML output for additional field (Pardot tracking)
?>
<tr class="form-field">
    <th scope="row" valign="top">
        <label for="pardot-tracking">
            <?php _e( 'Pardot Tracking', 
                      'brainrider-resource-centre' ); ?>
        </label>
    </th>
    <td>
        <select name="term_meta[pardot_tracking]" id="pardot-tracking">
            <option value="1" <?php selected( $term_meta['pardot_tracking'], 
                                              1, 
                                              true ) ?>>Enabled</option>
            <option value="0" <?php selected( $term_meta['pardot_tracking'], 
                                              0, 
                                              true ) ?>>Disabled</option>
        </select>
        <br />
        <p class="description">
            <?php _e( 'Select whether engagement with resources connected to this
                       term will be tracked in Pardot.', 
                      'brainrider-resource-centre' ); ?>
        </p>
    </td>
</tr>
<?php 
}


/**
 * ?.? Add Custom Field to New Term Screen
 * 
 * Adds Pardot tracking toggle to new term screen (i.e. term index).
 *
 * @param   string    $taxonomy    The taxonomy slug.
 *
**/
function br_rc_add_term_custom_fields( $taxonomy ) {

// HTML output for additional field (Pardot tracking)
?>
<div class="form-field">    
    <label for="pardot-tracking">
        <?php _e( 'Pardot Tracking', 
                  'brainrider-resource-centre' ); ?>
    </label>
    <select name="term_meta[pardot_tracking]" id="pardot-tracking">
        <option value="1">Enabled</option>
        <option value="0">Disabled</option>
    </select>
    <br />
    <p class="description">
        <?php _e( 'Select whether engagement with resources connected to this
                   term will be tracked in Pardot.', 
                  'brainrider-resource-centre'); ?>
    </p>
</div>
<?php 
}


/**
 * ?.? Save Custom Term Field Values
 * 
 * ?.? Saves values of custom fields (e.g. Pardot tracking).
 *
 * @param   interger    $term_id    Term ID.
 * @param   interger    $tt_id      Term taxonomy ID.
 *
**/
function br_rc_save_term_custom_fields( $term_id, $tt_id  ) {

    // If term_meta has been submitted
    if ( isset( $_POST['term_meta'] ) ) :

        // Load any existing term options
        $term_meta  = get_option( "taxonomy_$term_id" );

        // Build array of keys (currently only pardot_tracking)
        $cat_keys   = array_keys( $_POST['term_meta'] );

        // Loop through keys and if POST array contains key...
        foreach ( $cat_keys as $key ) :
            if ( isset( $_POST['term_meta'][ $key ] ) ) :

                // Set options variable with new value
                $term_meta[ $key ] = $_POST['term_meta'][ $key ];
            endif;
        endforeach;

        // Update the options
        update_option( "taxonomy_$term_id", $term_meta );
    endif;
}


/**
 * ?.? Inialize Custom Term Field Hooks
 * 
 * Sets up relavant action hooks to load/save custom term fields and their values.
 *
**/
function br_rc_init_custom_term_fields() {

    // Get taxonomy names associated w/ custom plugin post type
    $resource_taxonomies_array = get_object_taxonomies( 'br_rc_resource', 'names' );

    // Loop through each taxonomy name
    // Build action hooks for edit, add, and saved state
    foreach ( $resource_taxonomies_array as $name ) :

        $edit_action_hook   = $name . '_edit_form_fields';
        $add_action_hook    = $name . '_add_form_fields';
        $save_action_hook   = 'edited_' . $name;

        add_action( "$edit_action_hook", 'br_rc_edit_term_custom_fields', 10, 2 );
        add_action( "$add_action_hook", 'br_rc_add_term_custom_fields', 10, 1 );
        add_action( "$save_action_hook", 'br_rc_save_term_custom_fields', 10, 2 );
    endforeach;
}
add_action( 'init', 'br_rc_init_custom_term_fields' );


/*--------------------------------------------------------------
?.? Metabox Setup
--------------------------------------------------------------*/
/**
 * ?.? Remove extraneous metaboxes
 * 
 * 
**/
function br_rc_metabox_removal() {

    $metabox_removal_array = array( 
        'commentstatusdiv',
        'commentstatusdiv'
        );

    // Filter the metabox removal array
    $metabox_removal_array = apply_filters( 'br_rc_metabox_removal_array_filter',
                                            $metabox_removal_array );

    // Loop through and remove specified metaboxes
    foreach ( $metabox_removal_array as $metabox_removal_array_item ) :
        remove_meta_box( $metabox_removal_array_item, 'br_rc_resource', 'normal' );
    endforeach;
}


/**
 * ?.? Create Additional Required Metaboxes
 * 
 * Sets up up various additional dashboard metaboxes.
 * 
**/
function br_rc_metabox_creation() {

    // Test module
    $test_metabox_title = __( 'Test Module',
                                    'brainrider-resource-centre' );
    add_meta_box( 
        'br-rc-test-module',
        $test_metabox_title,
        'br_rc_test_metabox_output',
        'br_rc_resource',
        'side',
        'high' );
    
    // Pardot module
    $pardot_metabox_title = __( 'Pardot Module',
                                'brainrider-resource-centre' );
    add_meta_box( 
        'br-rc-pardot-module', 
        $pardot_metabox_title, 
        'br_rc_pardot_metabox_output', 
        'br_rc_resource', 
        'normal', 
        'high' );

    // Transcript module
    $transcript_metabox_title = __( 'Transcript Module',
                                    'brainrider-resource-centre' );
    add_meta_box( 
        'br-rc-transcript-module',
        $transcript_metabox_title,
        'br_rc_transcript_metabox_output',
        'br_rc_resource',
        'normal',
        'high' );
}


/**
 * ?.? Create Pardot Metabox Output
 * 
 * Creates metabox for Pardot data on single resource edit post page.
 * 
 * @param   object      $post       Global post object.
 *
**/
function br_rc_pardot_metabox_output( $post ) {

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If Pardot API call returns (user and API) keys
    if ( $pardot_api_key_array ) :

        // Generate output 
        $output  = '<div class="br-rc-metabox">'; 

        // Form selection
        $output .= '<div class="br-rc-metabox-controls">';
        $output .= '<span class="br-rc-form-update-status br-rc-hidden">';
        $output .= __( 'Forms updated successfully!', 'brainrider-resource-centre' );
        $output .= '</span>';
        $output .= '<button id="pardot-form-refresh"';
        $output .= ' class="br-rc-refresh-button button-secondary button-large">';
        $output .= '<i class="fa fa-lg fa-refresh"></i>';
        $output .= '</button>';
        $output .= '</div>';
        $output .= br_rc_pardot_form_selection_output( $post );

        // Form statistics
        // Check if a form is currently loaded
        $current_form = get_post_meta( $post->ID, '_br_rc_pardot_form_url', true);
        if ( $current_form ) :
            $output .= '<div class="br-rc-metabox-controls">';
            $output .= '<span class="br-rc-stat-update-status br-rc-hidden">';
            $output .= __( 'Form statistics updated successfully!',
                           'brainrider-resource-centre' );
            $output .= '</span>';
            $output .= '<button id="pardot-stat-refresh" ';
            $output .= 'class="br-rc-refresh-button button-secondary button-large">';
            $output .= '<i class="fa fa-lg fa-refresh"></i>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= br_rc_pardot_form_stat_output( $post );
        endif;

        $output .= '</div>';

    // Else, if no (user and API) keys, output error message 
    // and link to settings page
    else: 
        $output  = '<h4>';
        $output .= __( 'Pardot authentication failed. Please check your 
                       credentials (i.e. email, password, and user key) on ',
                       'brainrider-resource-centre' );
        $output .= '<a href="';
        $output .= menu_page_url( 'br_rc_resource_settings', false );
        $output .= '#br-rc-pardot-settings" >';
        $output .= __( 'the settings page.', 'brainrider-resource-centre' );
        $output .= '</a></h4>';
        $output .= '<input type="hidden" name="br_rc_pardot_form_url" value="" />';
    endif;

    // Echo the output 
    echo $output;
}


/**
 * ?.? Create Transcript Metabox Output
 * 
 * Creates metabox for transcript drawer on single resource edit post page.
 *
 * @param   object      $post       Global post object. 
 *
**/
function br_rc_transcript_metabox_output( $post ) {

    // Get meta value
    $br_rc_transcript_toggle = get_post_meta( $post->ID, '
                                              _br_rc_transcript_toggle',
                                              true );
    $br_rc_transcript_text   = get_post_meta( $post->ID, 
                                              '_br_rc_transcript_text',
                                              true );

    $output  = '<div class="br-rc-metabox">';
    $output .= '<h4>';
    $output .= __( 'Transcript Drawer:', 'brainrider-resource-centre' );
    $output .= '</h4>';
    $output .= '<p>';
    $output .= __( 'Select \'Enabled\' below to activate the transcript module:',
                   'brainrider-resource-centre' );
    $output .= '</p>';
    $output .= '<select id="br-rc-transcript-toggle"';
    $output .= 'name="br_rc_transcript_toggle">';
    $output .= '<option value="1" ';
    $output .= selected( $br_rc_transcript_toggle, '1', false );
    $output .= '>';
    $output .= __( 'Enabled', 'brainrider-resource-centre' );
    $output .= '</option>';
    $output .= '<option value="0" ';
    $output .= selected( $br_rc_transcript_toggle, '0', false );
    $output .= '>';
    $output .= __( 'Disabled', 'brainrider-resource-centre' );
    $output .= '</option>';
    $output .= '</select>';
    $output .= '</div>';

    // Filter the output
    $output = apply_filters( 'br_rc_transcript_metabox_output_filter', $output );

    // Echo the output  
    echo $output;

    // Display editor
    wp_editor( 
        $br_rc_transcript_text, 
        'br_rc_transcript_text',
        array(
            'media_buttons' => false,
        ) 
    );
}


/**
 * ?.? Create Featured Resource Toggle
 * 
 * Adds 'featured resource' checkbox to submit box on single resource edit page.
 * 
 * @param   object      $post       Global post object.
 *
**/
add_action( 'post_submitbox_misc_actions', 'br_rc_featured_resource_toggle_output');
function br_rc_featured_resource_toggle_output( $post ) {

    // If we're not on a resource post edit page, return
    if ( 'br_rc_resource' != $post->post_type ) :
        return;
    endif;

    // Retrieve metadata value if it exists
    $featured_resource_toggle = get_post_meta( $post->ID, 
                                               '_br_rc_featured_resource_toggle',
                                               true );

    // Create output
    $output  = '<div class="misc-pub-section br-rc-featured-resource">';
    $output .= '<i class="fa fa-lg fa-star"></i>';
    $output .= '<label for="br-rc-featured-resource-toggle">';
    $output .= __( 'Featured Resource:', 'brainrider-resource-centre' );
    $output .= '</label>';
    $output .= '<input type="checkbox" name="br_rc_featured_resource_toggle"';
    $output .= 'id="br-rc-featured-resource-toggle" value="1" ';
    $output .= checked( $featured_resource_toggle, 1, false );
    $output .= '/></ br>';
    $output .= '</div>';

    // Echo the output
    echo $output;
}


/**
 * ?.? Update Postmeta Values
 * 
 * Saves associated postmeta values (e.g. pardot_url, featured_resource_toggle).
 *
 * @param   interger    $post_id    Post ID for current resource.
 *
**/
function br_rc_save_meta( $post_id ) {

    // Featured resource toggle
    // Update field if checked, else delete
    if ( isset( $_POST['br_rc_featured_resource_toggle'] ) ) :
        update_post_meta( $post_id, 
                          '_br_rc_featured_resource_toggle',
                          strip_tags( $_POST['br_rc_featured_resource_toggle'] )
                        );
    else: 
        delete_post_meta( $post_id, '_br_rc_featured_resource_toggle' );
    endif;

    // Pardot URL
    if ( isset( $_POST['br_rc_pardot_form_url'] ) ) :
        update_post_meta( $post_id,
                          '_br_rc_pardot_form_url',
                          strip_tags( $_POST['br_rc_pardot_form_url'] )
                        );
    endif;

    // Pardot iFrame Parameters
    if ( isset( $_POST['br_rc_pardot_form_parameters'] ) ) :
        update_post_meta( $post_id,
                          '_br_rc_pardot_form_parameters',
                          strip_tags( $_POST['br_rc_pardot_form_parameters'] )
                        );
    endif;

    // Transcript toggle
    if ( isset( $_POST['br_rc_transcript_toggle'] ) ) :
        update_post_meta( $post_id,
                          '_br_rc_transcript_toggle',
                          strip_tags( $_POST['br_rc_transcript_toggle'] )
                        );
    endif;

    // Transcript text
    // Update field
    if ( isset( $_POST['br_rc_transcript_text'] ) ) :
        update_post_meta( $post_id, 
                          '_br_rc_transcript_text', 
                          stripslashes( $_POST['br_rc_transcript_text'] )
                        );
    endif;
}


/*--------------------------------------------------------------
?.? Setings Page Setup
--------------------------------------------------------------*/
/**
 * ?.? Add BR RC Settings Page
 * 
 * Run necessary functions for settings initilization.
 * 
**/
function br_rc_settings_init() {

    register_setting(
        'br_rc_settings_group',
        'br_rc_settings_group',
        'br_rc_validate_settings'
    );

    // Setting sections array
    $section_array = array(

                    // Banner
                    'br_rc_banner_settings' => array(
                        'id'    => 'br-rc-banner-settings',
                        'title' => __( 'Banner', 'brainrider-resource-centre' ),
                    ),

                    // Filter
                    'br_rc_filter_settings' => array(
                        'id'    => 'br-rc-filter-settings',
                        'title' => __( 'Filter', 'brainrider-resource-centre' ),
                    ),

                    // Layout
                    'br_rc_layout_settings' => array(
                        'id'    => 'br-rc-layout-settings',
                        'title' => __( 'Layout', 'brainrider-resource-centre' ),
                    ),

                    // Related Posts
                    'br_rc_related_posts_settings' => array(
                        'id'    => 'br-rc-related-posts-settings',
                        'title' => __( 'Related Posts', 
                                       'brainrider-resource-centre' ),
                    ),

                    // Pardot Settings
                    'br_rc_pardot_settings' => array(
                        'id'    => 'br-rc-pardot-settings',
                        'title' => __( 'Pardot Integration', 
                                       'brainrider-resource-centre' ),
                    ),

                    // Advanced Settings
                    'br_rc_advanced_settings' => array(
                        'id'    => 'br-rc-advanced-settings',
                        'title' => __( 'Advanced Settings', 
                                       'brainrider-resource-centre' ),
                    ),
    );

    // Filter the section array
    $section_array = apply_filters( 'br_rc_section_array_filter', $section_array );

    // Create sections
    foreach ( $section_array as $key => $value ) :
        add_settings_section(
            $value['id'],
            $value['title'],
            'br_rc_create_section',
            'br_rc_resource_settings'
        );
    endforeach;

    function br_rc_create_section( $args ) {
        $section_title   = strtolower( $args['title'] );
        $id              = $args['id'];

        $output          = "<p id='$id'>";
        $output         .= sprintf( __("Enter your %s settings here.", 
                                       'brainrider-resource-centre' ), 
                                    $section_title );
        $output         .= '<p>';
        echo $output;
    }

    // Fields array
    $fields_array = array(

        // Banner
        'br_rc_banner_toggle' => array(
                                'id'            => 'br-rc-banner-toggle',
                                'key'           => 'br_rc_banner_toggle',
                                'title'         => __( 'Banner Toggle', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-banner-settings',
                                'hidden'        => false,
                            ),
        'br_rc_banner_title' => array(
                                'id'            => 'br-rc-banner-title',
                                'key'           => 'br_rc_banner_title',
                                'title'         => __( 'Banner Title', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-banner-settings',
                                'hidden'        => false,
                            ),
        'br_rc_banner_description' => array(
                                'id'            => 'br-rc-banner-description',
                                'key'           => 'br_rc_banner_description',
                                'title'         => __( 'Banner Description', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'textarea',
                                'options'       => array(),
                                'section'       => 'br-rc-banner-settings',
                                'hidden'        => false,
                            ),
        'br_rc_banner_image_url' => array(
                                'id'            => 'br-rc-banner-image-url',
                                'key'           => 'br_rc_banner_image_url',
                                'title'         => __( 'Banner Image URL', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'image',
                                'options'       => array(),
                                'section'       => 'br-rc-banner-settings',
                                'hidden'        => false,
                            ),

        // Filter
        'br_rc_filter_title' => array(
                                'id'            => 'br-rc-filter-title',
                                'key'           => 'br_rc_filter_title',
                                'title'         => __( 'Filter Title', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-filter-settings',
                                'hidden'        => false,
                            ),
        'br_rc_filter_cta'  => array(
                                'id'            => 'br-rc-filter-cta',
                                'key'           => 'br_rc_filter_cta',
                                'title'         => 'Filter CTA',
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-filter-settings',
                                'hidden'        => false,
                            ),
        'br_rc_filter_type' => array(
                                'id'            => 'br-rc-filter-type',
                                'key'           => 'br_rc_filter_type',
                                'title'         => __( 'Filter Type', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'select',
                                'options'       => array(
                                                        __( 'vertical', 
                                                            'brainrider-resource-centre' ),
                                                        __( 'horizontal', 
                                                            'brainrider-resource-centre' ),
                                                ),
                                'section'       => 'br-rc-filter-settings',
                                'hidden'        => false,
                            ),
        'br_rc_filter_search_toggle' => array(
                                'id'            => 'br-rc-filter-search-toggle',
                                'key'           => 'br_rc_filter_search_toggle',
                                'title'         => __( 'Search Toggle', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-filter-settings',
                                'hidden'        => false,
                            ),

        // Layout
        'br_rc_layout_type' => array(
                                'id'            => 'br-rc-layout-type',
                                'key'           => 'br_rc_layout_type',
                                'title'         => __( 'Layout Type',
                                                        'brainrider-resource-centre' ),
                                'type'          => 'select',
                                'options'       => array(
                                                        __( 'with_sidebar',
                                                            'brainrider-resource-centre' ),
                                                        __( 'full_width',
                                                            'brainrider-resource-centre' ),
                                                ),
                                'section'       => 'br-rc-layout-settings',
                                'hidden'        => false,
                            ), 

        // Related Posts
        'br_rc_related_posts_title' => array(
                                'id'            => 'br-rc-related-posts-title',
                                'key'           => 'br_rc_related_posts_title',
                                'title'         => __( 'Related Posts Title', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-related-posts-settings',
                                'hidden'        => false,
                            ),
        'br_rc_related_posts_description' => array(
                                'id'            => 'br-rc-related-posts-description',
                                'key'           => 'br_rc_related_posts_description',
                                'title'         => __( 'Related Posts Description', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'textarea',
                                'options'       => array(),
                                'section'       => 'br-rc-related-posts-settings',
                                'hidden'        => false,
                            ),

        // Pardot Settings
        'br_rc_pardot_email' => array(
                                'id'            => 'br-rc-pardot-email',
                                'key'           => 'br_rc_pardot_email',
                                'title'         => __( 'Pardot Email', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'email',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
                            ),
        'br_rc_pardot_password' => array(
                                'id'            => 'br-rc-pardot-password',
                                'key'           => 'br_rc_pardot_password',
                                'title'         => __( 'Pardot Password', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'password',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
                            ),
        'br_rc_pardot_user_key' => array(
                                'id'            => 'br-rc-pardot-user-key',
                                'key'           => 'br_rc_pardot_user_key',
                                'title'         => __( 'User API Key', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
                            ),
        'br_rc_pardot_account_id' => array(
                                'id'            => 'br-rc-pardot-account-id',
                                'key'           => 'br_rc_pardot_account_id',
                                'title'         => __( 'Pardot Account ID', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
                            ),
        'br_rc_pardot_https_toggle' => array(
                                'id'            => 'br-rc-pardot-https-toggle',
                                'key'           => 'br_rc_pardot_https_toggle',
                                'title'         => __( 'Pardot HTTPS Toggle',
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
            ),
        'br_rc_pardot_score_increment' => array(
                                'id'            => 'br-rc-pardot-score-increment',
                                'key'           => 'br_rc_pardot_score_increment',
                                'title'         => __( 'Tracking Score Increment',
                                                        'brainrider-resource-centre' ),
                                'type'          => 'text',
                                'options'       => array(),
                                'section'       => 'br-rc-pardot-settings',
                                'hidden'        => false,
                            ), 

        // Advanced Settings
        'br_rc_css_toggle' => array(
                                'id'            => 'br-rc-css-toggle',
                                'key'           => 'br_rc_css_toggle',
                                'title'         => __( 'Default Stylesheet Toggle', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-advanced-settings',
                                'hidden'        => false,
                            ),
        'br_rc_grid_toggle' => array(
                                'id'            => 'br-rc-grid-toggle',
                                'key'           => 'br_rc_grid_toggle',
                                'title'         => __( 'Default Grid Toggle', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-advanced-settings',
                                'hidden'        => false,
                            ),
        'br_rc_font_toggle' => array(
                                'id'            => 'br-rc-font-toggle',
                                'key'           => 'br_rc_font_toggle',
                                'title'         => __( 'Default Font Toggle', 
                                                       'brainrider-resource-centre' ),
                                'type'          => 'toggle',
                                'options'       => array(),
                                'section'       => 'br-rc-advanced-settings',
                                'hidden'        => false,
                            ),
                        ); 

        // Filter the fields array
        $fields_array = apply_filters( 'br_rc_fields_array_filter', $fields_array );

    // Establish the fields (according to above array)
    foreach ( $fields_array as $fields_array_item => $value ) :

        // Set value of encompassing tr class dependent upon fields hidden value
        $class = ( $value['hidden'] ) ? 'hidden' : '';

        // Set up $args to pass to callback function
        $args = array( 
            'id'            => $value['id'],
            'key'           => $value['key'],
            'title'         => $value['title'],
            'type'          => $value['type'],
            'options'       => $value['options'],
            'section'       => $value['section'],
            'label_for'     => $value['id'],
            'class'         => $class,
        );

        add_settings_field(
            $value['id'], 
            $value['title'],
            'br_rc_create_field',
            'br_rc_resource_settings',
            $value['section'],
            $args
        );
    endforeach;

    // Create fields output
    function br_rc_create_field( $args ) {
        $id             = $args['id'];
        $key            = $args['key'];
        $type           = $args['type'];
        $select_options = $args['options'];
        $section        = $args['section'];

        // Access stored (or default) options values
        $options        = get_option( 'br_rc_settings_group' );
        $field_value    = $options[ $key ];

        // Build select options (if they exist)
        $select_options_html = '';
        if ( !empty( $select_options ) ) :
            foreach( $select_options as $select_option) : 
                $select_options_html .= "<option value='$select_option'";
                $select_options_html .= selected( $field_value, $select_option,
                                                  false );
                $select_options_html .= ">";
                $select_options_html .= ucwords( preg_replace( '/_/', ' ', $select_option ) );
                $select_options_html .= "</option>"; 
            endforeach;
        endif;

        // Set array of default field types
        $field_type_array = array(

            // Text
            "text"      => "<input id='$id' name='br_rc_settings_group[$key]' 
            type='text' value='" . esc_attr( $field_value ) . "' size='39' />",

            // Email
            "email"     => "<input id='$id' name='br_rc_settings_group[$key]' 
            type='email' value='" . esc_attr( $field_value ) . "' size='39' />",

            // Password
            "password"  => "<input id='$id' name='br_rc_settings_group[$key]' 
            type='password' value='" . esc_attr( $field_value ) . "' size='39' />",

            // Checkbox
            "checkbox"  => "<input id='$id' name='br_rc_settings_group[$key]' 
            type ='checkbox' value='$key' " . checked( $field_value, $key, false ) . " />",

            // Toggle (Radio)
            "toggle"        => "<input id='$id" . "-1' name='br_rc_settings_group[$key]' 
            type='radio' value='1' " . checked( $field_value, '1', false ) . " />
            <label for='$id" . "-1'>" . __( 'On', 'brainrider-resource-centre' ) . 
            "</label><br /><input id='$id" . "-0' name='br_rc_settings_group[$key]'
            type='radio' value='0' " . checked( $field_value, '0', false ) . " />
            <label for='$id" . "-0'>" . __( 'Off', 'brainrider-resource-centre' ) . 
            "</label>" ,

            // Select
            "select"        => "<select id='$id' name='br_rc_settings_group[$key]' 
            type='select'>$select_options_html</select>",

            // Textarea
            "textarea"  => "<textarea id='$id' name='br_rc_settings_group[$key]' 
            rows='5' cols='40'>" . esc_textarea( $field_value ) . "</textarea>",

            // Image
            "image"     => "<input id='$id' name='br_rc_settings_group[$key]' 
            type='text' value='" . esc_url( $field_value ) . "' size='39' />
            <input id='upload-image-button' type='button' value='" . 
            __( 'Media Library Image', 'brainrider-resource-centre' ) 
            . "' class='button-secondary' />",
        );

        // Filter the field type array
        $field_type_array = apply_filters( 'br_rc_field_type_array_filter', 
                                           $field_type_array, 
                                           $args, 
                                           $field_value );

        // Output the specific field
        echo $field_type_array[ $type ];
    }
}


/**
 * ?.? Validate Plugin Settings Values
 * 
 * Sanitizes settings values.
 *
 * @param   array    $input    unsanitized/unchecked settings values.
 *
 * @return  array    $output   validated/sanitized settings values.
 *
**/
function br_rc_validate_settings( $input ) {

    // Execute function hook
    do_action( 'br_rc_validate_settings_action', $input );

    // Validate that sucker...
    $output = array();

    // Loop through the input values
    foreach ( $input as $key => $value ) : 

        // If the value is set
        if ( isset( $input[ $key ] ) ) :

            // First check if it's an array
            if ( is_array( $input[ $key ] ) ) :

                // Loop through inner array and set value of sanitized output
                foreach ( $input[ $key ] as $k => $v ) :
                    $output[ $key ][ $k ] = strip_tags( stripcslashes( $v ) );
                endforeach;

            // Else if not an array
            else:

                // Sanitize output directly
                $output[ $key ] = strip_tags( stripcslashes( $input[ $key ] ) );
            endif;

        endif; 
    endforeach;

    // Return the validated options
    return $output;
}


/**
 * ?.? Create Plugin Setting's Page
 * 
 *
 * Instatiates settings page for plugin, including menu item within admin.
 *
**/
function br_rc_create_settings_page() {

    $default_create_settings_array = array(
        'parent_slug'       => 'edit.php?post_type=br_rc_resource', 
        'page_title'        => __( 'BR RC Settings', 
                                   'brainrider-resource-centre' ),
        'menu_title'        => __( 'Settings', 
                                   'brainrider-resource-centre' ),
        'capability'        => 'manage_options',
        'menu_slug'         => 'br_rc_resource_settings',
        'output_function'   => 'br_rc_output_settings_page',
    );

    $default_create_settings_array = apply_filters( 
                                        'br_rc_default_create_settings_array_filter',
                                         $default_create_settings_array );

    add_submenu_page( 
        $default_create_settings_array['parent_slug'],
        $default_create_settings_array['page_title'],
        $default_create_settings_array['menu_title'],
        $default_create_settings_array['capability'],
        $default_create_settings_array['menu_slug'], 
        $default_create_settings_array['output_function'] );
}

/**
 * ?.? Output Plugin Setting's Page
 * 
 *
 * Displays HTML for settings page, including various inputs.
 *
**/
function br_rc_output_settings_page() {

    $default_output_settings_array = array(
        'page_title'    => 'BR RC Settings',
        'page_slug'     => 'br_rc_resource_settings',
    );

    // Filter the default output settings array
    $default_output_settings_array = apply_filters( 
                                        'br_rc_default_output_settings_array_filter',
                                        $default_output_settings_array );
?>

    <div class="wrap">
        <h2><?php echo $default_output_settings_array['page_title']; ?></h2>

        <form action="options.php" method="POST">

            <?php 
            settings_fields( 'br_rc_settings_group' );
            do_settings_sections( $default_output_settings_array['page_slug'] );
            ?>

            <input name="Submit" 
                   type="submit" 
                   value="<?php esc_attr_e( 'Save Changes', 
                                            'brainrider-resource-centre' ); ?>" />

        </form>
    </div>
    <?php

    // Pardot API authentication
    // Grab Pardot settings values
    $options            = get_option( 'br_rc_settings_group' );
    $pardot_email       = $options['br_rc_pardot_email'];
    $pardot_password    = $options['br_rc_pardot_password'];
    $pardot_user_key    = $options['br_rc_pardot_user_key'];

    // Get the pardot options
    $pardot_options = get_option( 'br_rc_pardot_settings_group' );

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If authenticate returns failure and all Pardot fields are not empty
    if ( !$pardot_api_key_array 
    && ( !empty( $pardot_email ) 
    && !empty( $pardot_password ) 
    && !empty( $pardot_user_key ) ) ) :

        // Show error notice
        br_rc_pardot_settings_failure();

    // Else if authenticate returns with credentials and the options aren't set
    elseif ( $pardot_api_key_array && !$pardot_options ) :

        // Create Pardot options
        // Set up custom fields in Pardot
        br_rc_pardot_form_stat_call();
        br_rc_pardot_form_call();
        br_rc_pardot_custom_redirect_call();
        br_rc_create_custom_pardot_fields();
    endif;
}


/**
 * ?.? Set Default Plugin Options
 *
 * Set default options for plugin settings. Used on activation.
 * 
 *
**/
function br_rc_set_default_options() {
    $default_options = array(

        // Banner
        'br_rc_banner_toggle'             => '1',
        'br_rc_banner_title'              => __( 'BR Resource Centre Title', 
                                                 'brainrider-resource-centre' ),
        'br_rc_banner_description'        => __( 'This is a default description 
                                                 for the BR resource centre plugin. 
                                                 Like all descriptions, it serves to 
                                                 explain the purpose of this webpage 
                                                 and encourage the reader to read 
                                                 further down the page.',
                                                 'brainrider-resource-centre' ),
        'br_rc_banner_image_url'          => '',

        // Filter
        'br_rc_filter_title'              => __( 'Filter', 
                                                 'brainrider-resource-centre' ),
        'br_rc_filter_cta'                => __( 'Submit', 
                                                 'brainrider-resource-centre' ),
        'br_rc_filter_type'               => __( 'vertical', 
                                                 'brainrider-resource-centre' ),
        'br_rc_filter_search_toggle'      => '1',

        // Layout
        'br_rc_layout_type'               => __( 'with_sidebar',
                                                 'brainrider-resource-centre' ),

        // Related Posts
        'br_rc_related_posts_title'       => __( 'Related Resources', 
                                                 'brainrider-resource-centre' ),
        'br_rc_related_posts_description' => __( 'This is the default description 
                                                 for the related posts section.
                                                 In short, it offers some context 
                                                 as to the excerpts that follow below.',
                                                 'brainrider-resource-centre' ),

        // Pardot Settings
        'br_rc_pardot_email'              => '',
        'br_rc_pardot_password'           => '',
        'br_rc_pardot_user_key'           => '',
        'br_rc_pardot_account_id'         => '',
        'br_rc_pardot_api_key'            => array(
                                                'key_value'     => '',
                                                'last_update'   => '',
                                             ),
        'br_rc_pardot_https_toggle'       => 0,
        'br_rc_pardot_score_increment'    => 1,

        // Advanced Settings
        'br_rc_css_toggle'                => '1',
        'br_rc_grid_toggle'               => '1',
        'br_rc_font_toggle'               => '1',
    );

    // Filter the default options
    $default_options = apply_filters( 'br_rc_set_default_options_filter',
                                       $default_options );

    // If no options exists, load the default options
    if ( !get_option( 'br_rc_settings_group' ) ) :

        // Loop through the options
        foreach ( $default_options as $key => $value ) :

            // Remove any newline characters/returns w/ single space
            // and reset the value to the key
            $value                   = preg_replace( "/\r|\n/", " ", $value );
            $default_options[ $key ] = $value;

        endforeach;

        // Create the default options
        update_option( 'br_rc_settings_group', $default_options);
    endif;
}


/**
 * ?.? Remove Plugin Database Options (Disabled)
 *
 * Deletes user set database options. Will return plugin settings to 
 * default value on reinitilization. Used on deactivation (disabled).
 * 
 *
**/
function br_rc_remove_options() {
    delete_option( 'br_rc_settings_group' );
    delete_option( 'br_rc_pardot_settings_group' );
    delete_post_meta_by_key( '_br_rc_pardot_form_url' );
}


/*--------------------------------------------------------------
?.? Styles/Scripts Setup
--------------------------------------------------------------*/
/**
 * ?.? Enqueue Plugin Stylsheets
 *
 * Enqueues stylesheets/scripts for use throughout plugin.
 *
*/
function br_rc_enqueue() {

    $options = get_option( 'br_rc_settings_group' );
    $css_toggle = $options['br_rc_css_toggle'];
    $grid_toggle = $options['br_rc_grid_toggle'];
    $font_toggle = $options['br_rc_font_toggle'];

    // Plugin-specific styles
    if ( $css_toggle == '1' ) :
        wp_register_style( 'br_rc_fe_stylesheet', 
                          plugins_url( 'brainrider-resource-centre/assets/css/br-rc-fe-stylesheet.css',
                                       'brainrider-resource-centre' ) );
        wp_enqueue_style( 'br_rc_fe_stylesheet' );
    endif;

    // Bootstrap grid styles
    if ( $grid_toggle == '1' ) :
        wp_register_style( 'br_rc_boostrap_stylesheet', 
                          plugins_url( 'brainrider-resource-centre/assets/css/br-rc-bootstrap-stylesheet.min.css',
                                       'brainrider-resource-centre' ) );
        wp_enqueue_style( 'br_rc_boostrap_stylesheet' );
    endif;

    // Google font styles
    if ( $font_toggle == '1' ) :
        wp_register_style( 'br_rc_google_fonts',
                           '//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700',
                           array(),
                           null );
        wp_enqueue_style( 'br_rc_google_fonts' );
    endif;

    // Back-end styles
    wp_register_style( 'br_rc_be_stylesheet', 
                          plugins_url( 'brainrider-resource-centre/assets/css/br-rc-be-stylesheet.css',
                                       'brainrider-resource-centre' ) );
    wp_enqueue_style( 'br_rc_be_stylesheet' );

    // Font Awesome font styles
    wp_register_style( 'br_rc_font_awesome',
                       '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                       array(),
                       null );
    wp_enqueue_style( 'br_rc_font_awesome' );

    // Plugin-specific scripts
    wp_enqueue_script( 'br_rc_scripts', 
                       plugins_url( 'brainrider-resource-centre/assets/js/br-rc-scripts.js',
                                    'brainrider-resource-centre'),
                                    array( 
                                        'jquery', 
                                        'media-upload', 
                                        'thickbox' ),
                                    null,
                                    true );

    // Pass over values to br_rc_scripts
    wp_localize_script( 
        'br_rc_scripts',
        'ajax_object', 
        array( 
            'ajax_url'                      => admin_url( 'admin-ajax.php' ),
            'br_rc_pardot_manual_refresh'   => true,
            'post_id'                       => get_the_id(),
             )  
    );

    // Thickbox styles
    wp_enqueue_style( 'thickbox' );

    // Execute function hook
    do_action( 'br_rc_enqueue_action', $options );
}

// Enqueue custom redirect data in header
add_action( 'admin_enqueue_scripts', 'br_custom_admin_head' );
function br_custom_admin_head() {
    
    // Access global $post object
    global $post;

    // Vars
    $output_array = br_rc_pardot_custom_redirect_selection_output( $post );
    ?>
        <script type='text/javascript'>
            var customRedirectOutput = "<?php echo $output_array['custom_redirect_output']; ?>";
            var pardotLoggedIn       = "<?php echo $output_array['pardot_logged_in']; ?>";
        </script>
    <?php
}


/*--------------------------------------------------------------
?.? Chron Jobs
--------------------------------------------------------------*/
/**
 * ?.? Register Daily Pardot Sync
 *
 * Registers hook for daily Pardot API calls
 *
*/
function br_rc_register_pardot_daily_sync() {
    
    // Authenticate and check scheduled event timestamp
    $timestamp = wp_next_scheduled( 'br_rc_schedule_daily_pardot_sync' );

    // If daily sync not scheduled and Pardot authenticate returns true,
    // schedule the event
    if ( !$timestamp ) :
        wp_schedule_event( strtotime( '24:00:00' ),
                           'daily',
                           'br_rc_schedule_daily_pardot_sync'
                         );
    endif;
}
add_action( 'br_rc_schedule_daily_pardot_sync', 'br_rc_pardot_form_call' );
add_action( 'br_rc_schedule_daily_pardot_sync', 'br_rc_pardot_form_stat_call' );
add_action( 'br_rc_schedule_daily_pardot_sync', 'br_rc_pardot_custom_redirect_call' );