<?php
/**
 * Pardot Functions
 * 
 * Functions used in connection with the Pardot API.
 *
**/

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Pardot API Calls
    ?.? Make Pardot API Authentication Request
    ?.? Make Miscellaneous Pardot API Request
    ?.? Update Pardot Form Options (All)
    ?.? Update Pardot Custom Redirect Options (All)
    ?.? Update Pardot Form Statistics Table (Single)
    ?.? Update Pardot Form Statistics Tables (All)
    ?.? Make Manual Pardot Form Call
    ?.? Make Manual Pardot Stat Call
    ?.? Get Propsect ID
    ?.? Create Taxonomy Term Pardot Custom Fields
    ?.? Update Prospect Record 
    ?.? Update Prospect Record (Custom Scoring)
?.? Pardot Support
    ?.? Generate Stat Query Array
?.? Pardot Admin Messaging
    ?.? Display Pardot API Success Message
    ?.? Display Pardot API Error Message   
?.? Pardot Metabox Output
    ?.? Output Pardot Form Selection Input
    ?.? Output Pardot Form Statistics Table
?.? Pardot WYSIWYG Functionality
    ?.? Add plugin stylesheet to editor
    ?.? Include custom buttons (e.g. 'Insert Pardot CTA') into Tiny-MCE Editor
    ?.? Output Insert Custom Pardot CTA 


/*--------------------------------------------------------------
?.? Pardot API Calls
--------------------------------------------------------------*/
/**
 * ?.? Make Pardot API Authentication Request
 *
 * @return array $pardot_api_key    Array containing user key and Pardot API key 
 *                                  (valid for one hour from time of authentication)
**/ 
function br_rc_authenticate_pardot_api() {

    // Vars
    $options            = get_option( 'br_rc_settings_group' ); // General Settings
    $pardot_options     = get_option( 'br_rc_pardot_settings_group' ); // Pardot Settings
    $email              = $options['br_rc_pardot_email'];
    $password           = $options['br_rc_pardot_password'];
    $user_key           = $options['br_rc_pardot_user_key'];
    $api_key            = isset( $pardot_options['br_rc_pardot_api_key']['key_value'] )
                          ? $pardot_options['br_rc_pardot_api_key']['key_value'] 
                          : '';
    $expiry_timestamp   = isset( $pardot_options['br_rc_pardot_api_key']['expiry_timestamp'] )
                          ? $pardot_options['br_rc_pardot_api_key']['expiry_timestamp']
                          : '';
    $current_timestamp  = time();

    // If the API key doesn't already exist
    // Or the key has expired (59 minutes or greater)
    // And the required credentials do exist...
    if ( ( !$api_key || $current_timestamp >= $expiry_timestamp ) 
           && $email 
           && $password 
           && $user_key ) :

        // Load the credentials array
        $pardot_credentials_array = array(
            'email'     => $email,
            'password'  => $password,
            'user_key'  => $user_key,
            'format'    => 'json',
        );

        // Make Pardot API Call (to authenticate) returns JSON string
        $json = br_rc_call_pardot_api( 'https://pi.pardot.com/api/login/version/3',
                                        $pardot_credentials_array, 
                                        $method = 'POST' );

        // JSON string is decoded and turned into array
        $pardot_api_object = json_decode( $json, true );

        // If API key exists, return it
        if ( array_key_exists( 'api_key', $pardot_api_object ) ) :

            // First get/set the new API key and new expiry timestamp
            $pardot_options['br_rc_pardot_api_key']['key_value']        = $pardot_api_object['api_key'];
            $pardot_options['br_rc_pardot_api_key']['expiry_timestamp'] = time() + ( 59 * 60 );

            // Then update options
            update_option( 'br_rc_pardot_settings_group', $pardot_options );

            // Finally, build out key array to be returned
            $pardot_api_key_array = [];
            $pardot_api_key_array['br_rc_pardot_api_key']  = $pardot_api_object['api_key'];
            $pardot_api_key_array['br_rc_pardot_user_key'] = $user_key;

            // return array
            return $pardot_api_key_array;

        // Else, on error, return false
        else: 
            return false;
        endif;

    // Else if the api and user keys already exist
    // And they have not expired
    elseif ( $api_key 
            && $user_key 
            && ( $current_timestamp < $expiry_timestamp ) 
          ) :

        // Build the key array from the saved options (no API call)
        $pardot_api_key_array = [];
        $pardot_api_key_array['br_rc_pardot_api_key']  = $api_key;
        $pardot_api_key_array['br_rc_pardot_user_key'] = $user_key;

        // Return the key array
        return $pardot_api_key_array;

    // Else, on failure, return false
    else:
        return false;
    endif;
}


/**
 * ?.? Make Miscellaneous Pardot API Request
 *
 * @param   string  $url        The full Pardot API URL to call, 
 *                              e.g. "https://pi.pardot.com/api/prospect/version/3/do/query"
 * @param   array   $data       The data to send to the API - make sure to include
 *                              your api_key and user_key for authentication
 * @param   string  $method     The HTTP method, one of "GET", "POST", "DELETE"
 * @return  string              The raw XML response from the Pardot API
 * @throws  exception           If we were unable to contact the Pardot API or 
 *                              something went wrong
**/ 
function br_rc_call_pardot_api( $url, $data, $method = 'GET' ) {

    // Build out the full url, with the query string attached
    $query_string = http_build_query( $data, null, '&' );
    if ( strpos( $url, '?' ) !== false ) {
        $url = $url . '&' . $query_string;
    } else {
        $url = $url . '?' . $query_string;
    }

    // Create and send new HTTP request
    $pardot_api_request     = new WP_Http;
    $pardot_api_response    = $pardot_api_request->request( $url, array( 'method' => $method, ) );

    if ( $pardot_api_response === false ) :
        throw new Exception( "Unable to successfully complete Pardot API call to $url" );
    endif;

    // Retrieve body of response and return
    $pardot_api_response_body = wp_remote_retrieve_body( $pardot_api_response );
    return $pardot_api_response_body;
}


/**
 * ?.? Update Pardot Form Options (All)
 *
 * Performs Pardot form check, merging any new forms with existing options.
 *
 *                               
**/ 
function br_rc_pardot_form_call() {

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If authentication fails, return
    if ( !$pardot_api_key_array ) :
        return;
    endif; 

    // Make the API call
    $pardot_form_json = br_rc_call_pardot_api( 
        'https://pi.pardot.com/api/form/version/3/do/query?sort_by=updated_at',
        array(
            'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
            'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
            'format'    => 'json',
        ), 
        'POST'
    );

    // Get the currently stored options for Pardot
    $pardot_options             = get_option( 'br_rc_pardot_settings_group' );

    // Decode the received JSON data and convert to associative array
    $pardot_form_array          = json_decode( $pardot_form_json, true );
    $pardot_form_array          = $pardot_form_array['result']['form'];

    // Push Pardot form array data onto Pardot options array (at key 'forms')
    $pardot_options['forms']    = $pardot_form_array;

    // Update the timestamp
    $pardot_options['forms']['last_update'] = __( 'Available forms last updated on ', 
                                                  'brainrider-resource-centre' ) 
                                              . current_time( 'F j, Y, g:i a' );

    // Update those options!
    update_option( 'br_rc_pardot_settings_group', $pardot_options );
}


/**
 * ?.? Update Pardot Custom Redirect Options (All)
 *
 * Performs Pardot custom redirects check, merging any new forms with existing options.
 *
 *                               
**/
function br_rc_pardot_custom_redirect_call() {

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If authentication fails, return
    if ( !$pardot_api_key_array ) :
        return;
    endif;

    // Make the API call
    $pardot_custom_redirect_json = br_rc_call_pardot_api(
        'https://pi.pardot.com/api/customRedirect/version/3/do/query?sort_by=updated_at',
        array(
            'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
            'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
            'format'    => 'json',
        ),
        'POST'
    );

    // Get the currently stored options for Pardot
    $pardot_options                         = get_option( 'br_rc_pardot_settings_group' );

    // Decode the received JSON data and convert to associative array
    $pardot_custom_redirect_array           = json_decode( $pardot_custom_redirect_json, true );
    $pardot_custom_redirect_array           = $pardot_custom_redirect_array['result']['customRedirect'];
    
    // Push Pardot custom redirect array data onto Pardot options array (at key 'custom_redirect')
    $pardot_options['custom_redirect']      = $pardot_custom_redirect_array;
    
    // Update those options!
    update_option( 'br_rc_pardot_settings_group', $pardot_options );
}


/**
 * ?.? Update Pardot Form Statistics Table (Single)
 *
 * 
 * Refreshes the Pardot form statistic table on a single resource post.
 *
 *                               
**/
function br_rc_pardot_form_stat_call_single( $post_id ) {

    // Get saved/new Pardot form URLs
    $current_form_url = get_post_meta( $post_id, '_br_rc_pardot_form_url', true );
    $selected_form_url = ( isset( $_POST['br_rc_pardot_form_url'] ) ) 
                         ? stripslashes( $_POST['br_rc_pardot_form_url'] ) 
                         : '';

    // Check if a manual refresh has been called
    $pardot_manual_refresh = ( isset( $_POST['br_rc_pardot_manual_refresh'] ) )
                             ? $_POST['br_rc_pardot_manual_refresh'] 
                             : false;     

    // If a refresh has been called manually 
    // Or the resource has been updated after a new form was selected
    if ( $pardot_manual_refresh || ( $selected_form_url != $current_form_url ) ) :

        // Get credentials through Pardot API call
        $pardot_api_key_array = br_rc_authenticate_pardot_api();

        // If authentication fails, return
        if ( !$pardot_api_key_array ) :
            return;
        endif; 

        // Set up arrays of required params for upcoming calls
        $pardot_api_form_query_array   = br_rc_generate_pardot_query_array();
        $selected_pardot_form_array    = ( $selected_form_url ) 
                                         ? json_decode( $selected_form_url, true )
                                         : json_decode( $current_form_url, true );

        // Loop through each group in array and make call
        // Append results to new array
        $pardot_stat_array = array();
        foreach ( $pardot_api_form_query_array as $key => $arg_array ) :
            $results_array = br_rc_call_pardot_api( 
                'https://pi.pardot.com/api/visitorActivity/version/3/do/query?output=simple'
                . '&limit=1' // Limit to a single result
                . '&created_after=' . $arg_array['created_after'] // Activity created at...
                . '&type=' . $arg_array['type'] // Type of activity
                . '&form_id=' . $selected_pardot_form_array['id'], // Matches currently saved form
                array(
                    'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                    'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                    'format'    => 'json',
                ), 
                'POST'
            );
            $results_array = json_decode( $results_array, true );

            // Add results from API call to stat array
            $label_key = $arg_array['label'];
            $pardot_stat_array[ $label_key ][ $key ] = $results_array['result']['total_results'];
        endforeach;

        // Get currently saved Pardot options and push new stats onto array
        $pardot_options = get_option( 'br_rc_pardot_settings_group' );
        $pardot_options['stats'][ $post_id ] = $pardot_stat_array;

        // Append last updated timestamp
        $pardot_options['stats'][ $post_id ]['last_update'] = __( 'This form\'s statistics were last updated on ', 
                                                                'brainrider-resource-centre' ) 
                                                                . current_time( 'F j, Y, g:i a' );

        // Update options
        update_option( 'br_rc_pardot_settings_group', $pardot_options );

    endif;
}
add_action( 'save_post', 'br_rc_pardot_form_stat_call_single', 10 );


/**
 * ?.? Update Pardot Form Statistics Tables (All)
 *
 * 
 * Refreshes the Pardot form statistic tables on applicable resource posts.
 *
 *                               
**/ 
function br_rc_pardot_form_stat_call() {

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If authentication fails, return
    if ( !$pardot_api_key_array ) :
        return;
    endif; 

    // Set up array containing required params for upcoming calls
    $pardot_api_form_query_array = br_rc_generate_pardot_query_array();

    // Create args for upcoming resource post type query
    // Posts are limited to those with currently existing Pardot forms
    $args = array(
        'post_type'         => 'br_rc_resource',
        'posts_per_page'    => -1,
        'meta_key'          => '_br_rc_pardot_form_url',
        'meta_compare'      => '!=',
        'meta_value'        => ' '
    );

    // Peform custom query
    $resource_post_query = new WP_Query( $args );

    // Run through loop
    if ( $resource_post_query->have_posts() ) :
        while ( $resource_post_query->have_posts() ) :
            $resource_post_query->the_post();

            //
            $id = get_the_id();

            // Get post meta value and convert to associative array
            $post_meta_pardot_form_json     = get_post_meta( $id, 
                                                     '_br_rc_pardot_form_url',
                                                     true );
            $post_meta_pardot_form_array    = json_decode( $post_meta_pardot_form_json,
                                                   true );

            // Loop through each group in array and make call
            // Append results to new array
            $pardot_stat_array = array();
            foreach ( $pardot_api_form_query_array as $key => $arg_array ) :
                $results_array = br_rc_call_pardot_api( 
                    'https://pi.pardot.com/api/visitorActivity/version/3/do/query?output=simple'
                    . '&limit=1' // Limit to a single result
                    . '&created_after=' . $arg_array['created_after'] // Activity created at...
                    . '&type=' . $arg_array['type'] // Type of activity
                    . '&form_id=' . $post_meta_pardot_form_array['id'], // Matches currently saved form
                    array(
                        'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                        'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                        'format'    => 'json',
                    ), 
                    'POST'
                );
                $results_array = json_decode( $results_array, true );

                // Add results from API call to stat array
                $label_key = $arg_array['label'];
                $pardot_stat_array[ $label_key ][ $key ] = $results_array['result']['total_results'];
            endforeach;

            // Get currently saved Pardot options and push new stats onto array
            $pardot_options = get_option( 'br_rc_pardot_settings_group' );
            $pardot_options['stats'][ $id ] = $pardot_stat_array;

            // Append last updated timestamp
            $pardot_options['stats'][ $id ]['last_update'] = __( 'This form\'s statistics were last updated on ', 
                                                               'brainrider-resource-centre' ) 
                                                           . current_time( 'F j, Y, g:i a' );

            // Update options
            update_option( 'br_rc_pardot_settings_group', $pardot_options );

        endwhile;
        wp_reset_postdata();
    endif;
}


/**
 * ?.? Make Manual Pardot Form Call
 *
 * 
 * Manually refreshes the select options for the Pardot form dropdown.
 *
 *                               
**/ 
add_action( 'wp_ajax_br_rc_pardot_form_refresh', 'br_rc_pardot_form_refresh' );
function br_rc_pardot_form_refresh() {
    
    // Access WP database and grab id value from AJAX call
    global $wpdb;
    $post_id = $_POST['post_id'];

    // Update the forms
    br_rc_pardot_form_call();

    // Rebuild the options through Pardot API call
    $output = br_rc_pardot_form_selection_output( $post_id );
    echo $output;

    // Required to termniate and return a proper response
    wp_die();
}


/**
 * ?.? Make Manual Pardot Stat Call
 *
 * 
 * Manually refreshes the stat table for the selected Pardot form
 *
 *                               
**/ 
add_action( 'wp_ajax_br_rc_pardot_stat_refresh', 'br_rc_pardot_stat_refresh' );
function br_rc_pardot_stat_refresh() {
    
    // Access WP database and grab id value from AJAX call
    global $wpdb;
    $post_id = $_POST['post_id'];

    // Update the stats
    br_rc_pardot_form_stat_call_single( $post_id );

    // Rebuild the options through Pardot API call
    $output = br_rc_pardot_form_stat_output( $post_id );

    echo $output;

    // Required to termniate and return a proper response
    wp_die();
}


/**
 * ?.? Get Prospect ID
 *
 * 
 * Returns Pardot Prospect ID via visitor activity search.
 *
 * @return interger $prospect_id    ID number associated w/ Propsect ID.
 *                                  On failure return NULL.
 *                               
**/ 
function br_rc_get_prospect_id() {
    
    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If Pardot API call returns (user and API) keys
    if ( $pardot_api_key_array ) :

        // Get the options and set account id variable
        $options    = get_option( 'br_rc_settings_group' );
        $account_id = $options['br_rc_pardot_account_id'];

        // If the account id is set...
        if ( $account_id ) :

            // Create the visitor id
            $visitor_id = 'visitor_id' . $account_id;

            // Look for the cookie
            $visitor_id = ( isset( $_COOKIE[ $visitor_id ] ) ) 
                          ? $_COOKIE[ $visitor_id ] 
                          : '';

            // If the visitor id cookie exists
            if ( $visitor_id ) :

                // Make API call to grab a single visitor activity matching the ID
                $visitor_activity_json = br_rc_call_pardot_api( 
                'https://pi.pardot.com/api/visitorActivity/version/3/do/query'
                . '?visitor_id=' . $visitor_id // visitor activities matching provided ID
                . '&limit=1' // limit to 1
                . '&output=bulk', 
                array(
                'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                'format'    => 'json',
                ), 
                'POST'
                );

                // Decode the results into associative array and grab prospect ID
                $visitor_activity_array = json_decode( $visitor_activity_json, true);
                $prospect_id            = $visitor_activity_array['result']['visitor_activity']['prospect_id'];

                // Return prospect ID
                return $prospect_id;

            endif;
        endif;

        // Else, return NULL
        return NULL;

    endif;
}


/**
 * ?.? Create Taxonomy Term Pardot Custom Fields
 *
 * Creates (on initialization and create/edit of terms), new Pardot custom
 * fields according to custom post type's taxonomies/terms.
 *
 *                               
**/ 
function br_rc_create_custom_pardot_fields() {

    // Authenticate
    $pardot_api_key_array   = br_rc_authenticate_pardot_api();

    // On authentication failure, return
    if ( !$pardot_api_key_array ) :
        return;
    endif;

    // Get array of taxonomies belonging to custom post type
    $resource_taxonomies_array = get_object_taxonomies( 'br_rc_resource', 'names' );

    // Build array containing all resource post type terms
    $resource_terms_array = get_terms( array(
                                'taxonomy'      => $resource_taxonomies_array,
                                'hide_empty'    => false,
                            ) );

    // Set up the empty custom fields array
    $custom_fields_array = [];

    // Loop through each of the term objects attached to the resource post type
    foreach ( $resource_terms_array as $resource_term_object ) :

        // Prep the key (i.e. Pardot ID)
        $key = preg_replace( '/-/', '_', $resource_term_object->slug ) 
               . '_score';

        // Prep the value (i.e. custom field name)
        $value = '> ' . preg_replace( '/[^a-zA-Z0-9\-& ]/', 
                               '', 
                               html_entity_decode( $resource_term_object->name . ' Score' ) 
                 );

        // Load the custom fields array w/ prepped valued
        $custom_fields_array[ $key ] = $value;
    endforeach;

    // Get Pardot options
    $pardot_options = get_option( 'br_rc_pardot_settings_group' );

    // Loop through the array grabbing $key (field ID) and $value (field name)
    foreach ( $custom_fields_array as $key => $value ) :

        // Set flag for looped field to prevent unnecssary calls
        $flag = isset( $pardot_options['custom_fields'][ $key ] )
                ? $pardot_options['custom_fields'][ $key ]
                : false;

        // Check if field has already been created
        if ( !$flag ) :

            // Reencode the value
            $value = urlencode( $value );

            // Make Pardot API call and create custom fields
            br_rc_call_pardot_api(
                'https://pi.pardot.com/api/customField/version/3/do/create?'
                . 'field_id=' . $key // Field ID 
                . '&name=' . $value, // Field name
                array(
                    'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                    'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                ), 
                'POST'
            );

            // Set flag on specific term in options table
            $pardot_options['custom_fields'][ $key ] = true;
        endif;
    endforeach;

    // Update Pardot options
    update_option( 'br_rc_pardot_settings_group', $pardot_options );
}
add_action( 'created_term', 'br_rc_create_custom_pardot_fields', 10 );
add_action( 'edited_term', 'br_rc_create_custom_pardot_fields', 10 );


/**
 * ?.? Update Prospect Record 
 *
 * Updates Prospect record field(s) (default and custom) w/ specified value(s). 
 *
 * @param   interger   $prospect_id         Prospect ID value. 
 *                                          Default set to false which will trigger
 *                                          ID fetch.
 * @param   array      $data                Associative array containing key/value pair
 *                                          of field id and new field value.
 * 
 *                               
**/ 
function br_rc_pardot_field_update( $prospect_id = false, $data = array() ) {

    // Authenticate
    $pardot_api_key_array   = br_rc_authenticate_pardot_api();

    // Set the prospect ID
    // If the value has not been set, attempt to acquire
    $prospect_id = ( $prospect_id )
                   ? $prospect_id
                   : br_rc_get_prospect_id();

    // If we still don't have a valid ID, return out
    if ( !$propsect_id ) :
        return;
    endif;

    // Create the update array
    $update_array = array(
                        'prospects' => array(
                            $prospect_id => array(),
                        ),
                    );

    // Loop through the data and push onto update array
    foreach ( $data as $field_id => $field_value ) :
        $update_array['prospects'][ $prospect_id ][ $field_id ] = $field_value;
    endforeach;

    // Convert to JSON
    $update_json = json_encode( $update_array );


    // If Pardot API call returns (user and API) keys and you are able retrieve and ID...
    if ( $pardot_api_key_array && $prospect_id) :

        // Make update call to Pardot API and update record
        br_rc_call_pardot_api(
            'https://pi.pardot.com/api/prospect/version/3/do/batchUpdate?prospects='
            . $update_json,
            array(
                'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
            ), 
            'POST'
        );
    endif;
}


/**
 * ?.? Update Prospect Record (Custom Scoring)
 *
 * Updates/creates scoring on Prospect record. 
 * Triggered at template level (br_rc_after_content_single action)
 * based on terms associated w/ post.
 *                               
**/ 
function br_rc_pardot_score_update() {

    // Authenticate
    $pardot_api_key_array = br_rc_authenticate_pardot_api();

    // If Pardot API call returns (user and API) keys
    if ( $pardot_api_key_array ) :

        // Post terms setup
        // Get the taxonomies attached to resource custom post type and post ID
        $resource_taxonomies_array  = get_object_taxonomies( 'br_rc_resource' );
        $post_id                    = get_the_ID();

        // Set up our empty array
        $resource_post_terms_array = [];

        // Loop through the gathered taxonomies
        foreach ( $resource_taxonomies_array as $name ) :

            // Create a temporary array containing current posts terms
            // Loop through returned objects and push slug onto prepped array
            $temp_terms_array = get_the_terms( $post_id, $name );

            // Check if returned array exists
            if ( is_array( $temp_terms_array ) ) :
                foreach ( $temp_terms_array as $term_object ) :

                    // Check if Pardot tracking has been disabled for this term
                    $term_id         = $term_object->term_id;
                    $term_options    = get_option( "taxonomy_$term_id" );
                    $pardot_tracking = isset( $term_options[ 'pardot_tracking' ] ) 
                                       ? $term_options[ 'pardot_tracking' ]
                                       : true; // default set to true

                    // If term is tracked, add it to array
                    if ( $pardot_tracking ) :

                        $resource_post_terms_array[] = preg_replace( '/-/', 
                                                                     '_', 
                                                                     $term_object->slug . '_score' ) ;

                    // Else, cycle through next iteration
                    else:
                        continue;
                    endif;
                endforeach;
            endif;
        endforeach;

        // Update data setup
        // Get Prospect's ID
        $prospect_id          = br_rc_get_prospect_id();

        // If we return an ID, make API call to access Prospect record,
        // and convert results to array
        if ( $prospect_id ) :
            $prospect_record_json = br_rc_call_pardot_api( 
                'https://pi.pardot.com/api/prospect/version/3/do/read/id/'
                . $prospect_id
                . '&output=simple',
                array(
                'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                'format'    => 'json',
                ), 
                'POST'
            );
            $prospect_record_array = json_decode( $prospect_record_json, true );

            // Create the update array
            $update_array = array(
                                'prospects' => array(
                                    $prospect_id => array(),
                                ),
                            );

            // Get our plugin options
            $options            = get_option( 'br_rc_settings_group');

            // Check if increment score is set and not empty and grab value 
            $score_increment    = isset( $options['br_rc_pardot_score_increment'] )
                                  && !empty( $options['br_rc_pardot_score_increment'] )
                                  ? intval( $options['br_rc_pardot_score_increment'] )
                                  : 1; // Default when no value is set is 1

            // Loop through our array containing terms associated w/ post
            foreach ( $resource_post_terms_array as $field_id ) :

                // Check for existing score (Pardot does not return unset fields)
                $initial_score = array_key_exists( $field_id, 
                                                   $prospect_record_array['prospect'] );

                // If score already exists, increment, otherwise set base value
                if ( !$initial_score ) :
                    $field_value = $score_increment;
                else:
                    $field_value = intval( $prospect_record_array['prospect'][ $field_id ] ) 
                                   + $score_increment;
                endif;

                $update_array['prospects'][ $prospect_id ][ $field_id ] = $field_value;
            endforeach;

            // Convert to JSON
            $update_json = json_encode( $update_array );

            // Send new data to Pardot through API call
            br_rc_call_pardot_api(
                'https://pi.pardot.com/api/prospect/version/3/do/batchUpdate?prospects='
                . $update_json,
                array(
                    'api_key'   => $pardot_api_key_array['br_rc_pardot_api_key'],
                    'user_key'  => $pardot_api_key_array['br_rc_pardot_user_key'],
                ), 
                'POST'
            );
        endif;
    endif;
}
add_action( 'br_rc_after_content_single', 'br_rc_pardot_score_update' );


/*--------------------------------------------------------------
?.? Pardot Support
--------------------------------------------------------------*/
/**
 * ?.? Generate Stat Query Array
 *
 * Generate array containing query variable for use in daily Pardot check.
 *
 * @return $output     array   Array containing query varirables use in 
 *                             br_rc_pardot_form_stat_call(_single).
 *                                                                                 
**/ 
function br_rc_generate_pardot_query_array() {
    $output = array(

        // Today
        'today_view'          => array(
                                    'label'         => __( 'Today',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'today',
                                    'type'          => 2,
                                ),
        'today_error'         => array(
                                    'label'         => __( 'Today',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'today',
                                    'type'          => 3,
                                ),
        'today_success'       => array(
                                    'label'         => __( 'Today',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'today',
                                    'type'          => 4,
                                ),
       
        // Last 7 Days
        'last_7_days_view'    => array(
                                    'label'         => __( 'Last 7 Days',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'last_7_days',
                                    'type'          => 2,
                                ),
        'last_7_days_error'   => array(
                                    'label'         => __( 'Last 7 Days',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'last_7_days',
                                    'type'          => 3,
                                ),
        'last_7_days_success' => array(
                                    'label'         => __( 'Last 7 Days',
                                                            'brainrider-resource-centre' ),
                                    'created_after' => 'last_7_days',
                                    'type'          => 4,
                                ),

        // This Month
        'this_month_view'     => array(
                                    'label'         => __( 'This Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'this_month',
                                    'type'          => 2,
                                ),
        'this_month_error'    => array(
                                    'label'         => __( 'This Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'this_month',
                                    'type'          => 3,
                                ),
        'this_month_success'  => array(
                                    'label'         => __( 'This Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'this_month',
                                    'type'          => 4,
                                ),

        // Last Month
        'last_month_view'     => array(
                                    'label'         => __( 'Last Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'last_month',
                                    'type'          => 2,
                                ),
        'last_month_error'    => array(
                                    'label'         => __( 'Last Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'last_month',
                                    'type'          => 3,
                                ),
        'last_month_success'  => array(
                                    'label'         => __( 'Last Month',
                                                           'brainrider-resource-centre' ),
                                    'created_after' => 'last_month',
                                    'type'          => 4,
                                ),
    );
    
    // Return the output
    return $output;
}


/*--------------------------------------------------------------
?.? Pardot Admin Messaging
--------------------------------------------------------------*/
/**
 * ?.? Display Pardot API Success Message
 *
 * Echoes Pardot success message.
 * 
**/
function br_rc_pardot_settings_success() {

    $output  = '<div class="notice notice-success is-dismissible">';
    $output .= '<p>';
    $output .= __( 'Pardot settings saved successfully.', 
                   'brainrider-resource-centre' );
    $output .= '</p>';
    $output .= '</div>';

    $output = apply_filters( 'br_rc_pardot_settings_success_filter', $output );
            
    echo $output;
}


/**
 * ?.? Display Pardot API Error Message
 *
 * Echoes Pardot error message.
 * 
**/
function br_rc_pardot_settings_failure() {
    
    $output  = '<div class="notice notice-error is-dismissible">';
    $output .= '<p>';
    $output .= __( 'Pardot authentication failed. Please check your credentials
                   (i.e. email, password, and user key) in the Pardot settings
                   section.', 'brainrider-resource-centre' );
    $output .= '</p>';
    $output .= '</div>';

    $output = apply_filters( 'br_rc_pardot_settings_failure_filter', $output );

    echo $output;
}


/*--------------------------------------------------------------
?.? Pardot Metabox Output
--------------------------------------------------------------*/
/**
 * ?.? Output Pardot Form Selection Input
 *
 * Displays the form selection input within the Pardot metabox.
 *
 * @param $post                     object  Global post object.
 *
 * @return $output                  string  HTML output for dropdown.
 *                               
**/ 
function br_rc_pardot_form_selection_output( $post ) {

    // Get the post id depending upon if it's alrady been passed as an argument
    $post_id = ( is_object($post ) ) ? $post->ID : $post;

    // Grab the stored Pardot form data from the options table
    $pardot_options = get_option( 'br_rc_pardot_settings_group' );

    // Set the timestamp and then remove it from array
    $pardot_form_timestamp = $pardot_options['forms']['last_update'];
    unset( $pardot_options['forms']['last_update'] );

    // Set remaining values to variable
    $pardot_form_array = $pardot_options['forms'];

    // Get post meta value and convert to associative array
    $post_meta_pardot_form_json = get_post_meta( $post_id, 
                                                     '_br_rc_pardot_form_url',
                                                     true );

    $post_meta_pardot_form_array = json_decode( $post_meta_pardot_form_json,
                                                   true );

    // Build the output
    $output  = '<div id="br-rc-pardot-form-url-section">';
    $output .= '<h4>';
    $output .= __( 'Form Selection:', 'brainrider-resource-centre' );
    $output .= '</h4>';
    $output .= '<p>';
    $output .= __( 'Select a Pardot form to include it on the page.',
                   'brainrider-resource-centre' );
    $output .= '</p>';
    $output .= '<select name="br_rc_pardot_form_url">';
    $output .= '<option value="">';
    $output .= __( 'No Pardot Form', 'brainrider-resource-centre' );
    $output .= '</option>';

    // Loop through the received and decoded form array and build options
    // Value passed as JSON object containing ID and url
    foreach ( $pardot_form_array as $pardot_form_array_item ) :

        // Grab id from array
        $pardot_form_id = $pardot_form_array_item['id'];

        // Create label from name
        $pardot_form_label = $pardot_form_array_item['name'];

        // Create url from embed code
        $pardot_form_embed_code = htmlentities( 
                                    $pardot_form_array_item['embedCode'] );
        $start                  = strpos( $pardot_form_embed_code, 'http' );
        $end                    = strpos( $pardot_form_embed_code,
                                          '&quot; ',
                                          $start );
        $length                 = $end - $start;
        $pardot_form_url        = substr( $pardot_form_embed_code,
                                          $start,
                                          $length );

        // Get plugin settings options
        $plugin_settings                    = get_option( 'br_rc_settings_group' );

        // Check if https toggle is activated
        if ( $plugin_settings['br_rc_pardot_https_toggle'] ) :

            // Perform preg_replace to alter domain from vanity URL
            $pardot_form_url = preg_replace(
                                    '/https?:\\/\\/.+?\\//',
                                    'https://go.pardot.com/',
                                    $pardot_form_url
                                );
        endif;

        // Create associate array and then encode as JSON 
        // (used for option value)
        $option_value_array = array(
            'id'    => $pardot_form_id,
            'url'   => $pardot_form_url,
            'label' => $pardot_form_label,
        );
        $option_value_json = json_encode( $option_value_array );

        $output .= '<option value="'; 
        $output .= esc_attr( $option_value_json );
        $output .= '" ';
        $output .= selected( $post_meta_pardot_form_array['id'], 
                             $option_value_array['id'], 
                             false );
        $output .= ' >';
        $output .= esc_html( $pardot_form_label );
        $output .= '</option>';

    endforeach;
    $output .= '</select>';

    // Grab stored Pardot form parameter data from post meta
    $post_meta_pardot_form_paramters = get_post_meta( $post_id, 
                                                     '_br_rc_pardot_form_parameters',
                                                     true );

    // Generate input for Pardot form parameters
    $output .= '<p>';
    $output .= __( 'Specify additional parameters to include at the end of the iFrame URL.', 
                   'brainrider-resource-centre' );
    $output .='</p>';
    $output .= '<input type="text" name="br_rc_pardot_form_parameters" value="';
    $output .= $post_meta_pardot_form_paramters;
    $output .= '">';

    // Set timestamp for last update
    $output .= '<div class="br-rc-pardot-form-timestamp">';
    $output .=  $pardot_form_timestamp;
    $output .= '</div>';
    $output .= '</div>';

    // Return the output
    return $output;
}


/**
 * ?.? Output Pardot Form Statistics Table
 *
 * Displays the form statistic table within the Pardot metabox.
 *
 * @param $post                     object  Global post object.
 *
 * @return $output                  string  HTML output for table.
 *                               
**/ 
function br_rc_pardot_form_stat_output( $post ) {

    // Get the post id depending upon if it's alrady been passed as an argument
    $post_id = ( is_object($post ) ) ? $post->ID : $post;

    // Grab the options
    $pardot_options     = get_option( 'br_rc_pardot_settings_group' );
    
    // Set the timestamp and then remove it from array 
    $pardot_stat_timestamp = $pardot_options['stats'][ $post_id ]['last_update'];
    unset( $pardot_options['stats'][ $post_id ]['last_update'] );

    // Get the stats for the curret post's form
    $pardot_stat_array  = $pardot_options['stats'][ $post_id ];

    // Create form statistics' output
    // Array containing table headings
    $table_headlines_array = array(
        __( 'Timeframe', 'brainrider-resource-centre' ),
        __( 'Views', 'brainrider-resource-centre' ),
        __( 'Errors', 'brainrider-resource-centre' ),
        __( 'Submissions', 'brainrider-resource-centre' ),
    );
    $output  = '<div id="br-rc-pardot-stat-section">';
    $output .= '<h4>';
    $output .= __( 'Form Performance Metrics:', 'brainrider-resource-centre' );
    $output .= '</h4>';
    $output .= '<div id="br_rc_pardot_stat_intro">';
    $output .= '<p>';
    $output .= __( 'The statistics below reflect the currently saved form.',
                   'brainrider-resource-centre' );
    $output .= '<table class="br-rc-pardot-metrics-table">';
    $output .= '<thead>';
    $output .= '<tr>';
    for ( $i = 0; $i < count( $table_headlines_array ); $i++ ) :
        if ( $i == 0 ) :
            $output .= '<td>';
            $output .= __( 'Timeframe', 'brainrider-resource-centre' );
            $output .= '</td>';
        else: 
            $output .= '<th>';
            $output .= sprintf( __( "%s", 'brainrider-resource-centre' ),
                              $table_headlines_array[ $i ] );
            $output .= '</th>';
        endif;
    endfor;
    $output .= '</tr>';
    $output .= '</thead>';
    foreach ( $pardot_stat_array as $key => $sub_stat_array ) :
        $output .= '<tr>';
        $output .= '<td>';
        $output .= $key;
        $output .= '</td>';
        foreach ( $sub_stat_array as $sub_stat_array_key 
                  => $sub_stat_array_value ) :
            $output .= '<td>';
            $output .= $sub_stat_array_value;
            $output .= '</td>';
        endforeach;
        $output .= '</tr>';
    endforeach;
    $output .= '</table>';
    $output .= '</div>';

    // Set timestamp for last update
    $output .= '<div class="br-rc-pardot-stat-timestamp">';
    $output .=  $pardot_stat_timestamp;
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}


/*--------------------------------------------------------------
?.? Pardot WYSIWYG Functionality
--------------------------------------------------------------*/
/**
 * ?.? Add plugin stylesheet to editor
 *
 *                               
**/
function br_rc_add_editor_styles() {
    add_editor_style( plugins_url( 
        'brainrider-resource-centre/assets/css/br-rc-be-stylesheet.css', 
        'brainrider-resource-centre' 
    ) );
}


/**
 * ?.? Include custom buttons (e.g. 'Insert Pardot CTA') into Tiny-MCE Editor
 *
 *                               
**/
function br_rc_custom_buttons() {
    add_filter( 'mce_external_plugins', 'br_rc_add_custom_buttons' );
    add_filter( 'mce_buttons', 'br_rc_register_custom_buttons' );
}

function br_rc_add_custom_buttons( $plugin_array ) {
    $plugin_array['br_rc'] = plugins_url( 'brainrider-resource-centre/assets/js/br-rc-tinymce-plugin.js',
                                    'brainrider-resource-centre');
    return $plugin_array;
}

function br_rc_register_custom_buttons( $buttons ) {
    array_push( $buttons, 'customcta' );
    return $buttons;
}


/**
 * ?.? Output Insert Custom Pardot CTA 
 *
 * @param $post                   object Global post object.
 *
 * @return $output                string HTML output for dropdown.
 *                               
**/
function br_rc_pardot_custom_redirect_selection_output( $post ) {

    // Get the post id depending upon if it's alrady been passed as an argument
    $post_id = ( is_object($post ) ) ? $post->ID : $post;

    // Grab the stored Pardot custom redirect data from the options table
    $pardot_options                             = get_option( 'br_rc_pardot_settings_group' );
    $pardot_custom_redirect_array               = $pardot_options['custom_redirect'];

    // Get post meta value and convert to associative array
    $post_meta_pardot_custom_redirect_json      = get_post_meta( $post_id, 
                                                     '_br_rc_pardot_custom_redirect_url',
                                                     true );
    $post_meta_pardot_custom_redirect_array      = json_decode( $post_meta_pardot_custom_redirect_json,
                                                   true );

    // Create empty output array
    $output_array = [];

    if ( $pardot_custom_redirect_array ) :

        // Build the output
        $output  = "<select id='br-rc-pardot-custom-redirect-url' name='br_rc_pardot_custom_redirect_url'>";
        $output .= "<option value=''>";
        $output .= __( "Select Custom Redirect", "brainrider-resource-centre" );
        $output .= "</option>";

        // Loop through the received and decoded custom redirect array and build options
        // Value passed as JSON object containing ID and url
        foreach ( $pardot_custom_redirect_array as $pardot_custom_redirect_array_item ):

            // Grab id from array
            $pardot_custom_redirect_id          = $pardot_custom_redirect_array_item['id'];

            // Create label from name
            $pardot_custom_redirect_label       = $pardot_custom_redirect_array_item['name'];


            // Get plugin settings options
            $plugin_settings                    = get_option( 'br_rc_settings_group' );

            // Check if https toggle is activated
            if ( $plugin_settings['br_rc_pardot_https_toggle'] ):

                // Perform preg_replace to alter domain from vanity URL
                $pardot_custom_redirect_array_item['url'] = preg_replace( 
                                                        '/https?:\/\/.+\.com/', 
                                                        'https://go.pardot.com', 
                                                        $pardot_custom_redirect_array_item['url'] 
                                                        );
            endif;

            // Create url from url (convert characters to html entities)
            $pardot_custom_redirect_url         = htmlentities( $pardot_custom_redirect_array_item['url'] );

            $output .= "<option value='"; 
            $output .= esc_attr( $pardot_custom_redirect_url );
            $output .= "' ";
            $output .= selected( $post_meta_pardot_custom_redirect_array, 
                                 $pardot_custom_redirect_url, 
                                 false );
            $output .= " >";
            $output .= esc_html($pardot_custom_redirect_label);
            $output .= "</option>";
        endforeach;
        $output .= "</select>";

        // Construct the output array
        $output_array['custom_redirect_output'] = $output;
        $output_array['pardot_logged_in'] = true;

    // Else, if no redirect array exists (i.e. authentication has failed)
    // Adjust output array
    else: 

        // Construct the output array
        $output_array[ 'custom_redirect_output' ] = '';
        $output_array[ 'pardot_logged_in' ] = false;
        
    endif;

    // Return the output array
    return $output_array;
}