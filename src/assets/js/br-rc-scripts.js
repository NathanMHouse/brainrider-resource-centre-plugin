/**
 * Scripts
 * 
 *
 */

/*--------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
?.? Back-end
	?.? Media Library Integration
	?.? Update Pardot Form Options
	?.? Update Pardot Form Stats
	?.? Transcript Module Back-end
	?.? Form Stats Input Change
?.? Front-end
	?.? Filter Category Select
	?.? Transcript Module Front-end
	

/*--------------------------------------------------------------
?.? Back-end
--------------------------------------------------------------*/
/**
 * Media Library Integration
 *
 * Integrates wordpress media library into plugin settings page.
 *
**/
jQuery( document ).ready( function( $ ) {
	 
	 var formfield = null;

	 // Call function on button click
	 $( '#upload-image-button' ).click( function() {

	 	// Get name attribute for image field
	 	formfield = $( '#br-rc-banner-image-url' ).attr( 'name' );

	 	// Show media library  modal
	 	tb_show( '', 'media-upload.php?type=image&TB_iframe=true' );
	 	return false;
	 });

	 // Send html (i.e. chose image) to editor
	 window.original_send_to_editor = window.send_to_editor;
	 window.send_to_editor = function( html ) {

	 	// If formfield exists
	 	if ( formfield != null ) {

	 		// Find img element within passed html and grab src  
	 		imgElement  = $( html ).filter( 'img' );
	 		fileUrl = imgElement.attr( 'src' );

	 		// Set image form field to image src
	 		$( '#br-rc-banner-image-url' ).val( fileUrl );

	 		// Remove media library
	 		tb_remove();

	 		// Reset image formfield var to null
	 		formfield = null;
	 	} else {
	 		window.original_send_to_editor( html );
	 	}
	 };
});


/**
 * ?.? Update Pardot Form Options
 *
 * Makes call to Pardot API and refreshes form options 
 *
 *
**/
jQuery( document ).ready( function( $ ) {
	var refreshButton = $( '#pardot-form-refresh' );

	// Data passed in AJAX call
	var data = {
			'action': 'br_rc_pardot_form_refresh',
			'post_id': ajax_object.post_id // Set via localize script
	};

	function pardotApiCall( e ) {

		// Add loader class to button and
		// set font-awesome element to hidden
		$( '#pardot-form-refresh' ).addClass( 'br-rc-loading' );
		$( '#pardot-form-refresh i' ).css( 'visibility', 'hidden' );

		// Prevent submission/save of post
		e.preventDefault();

		// Make AJAX call
		// ajax_object passed via localize script
		$.post( ajax_object.ajax_url, data, function( responseOutput ) {

			// Replace section with response from handler
			$( '#br-rc-pardot-form-url-section' ).replaceWith( responseOutput );

			// Remove loader class and
			// toggle font-awesome visibility
			$( '#pardot-form-refresh' ).removeClass( 'br-rc-loading' );
			$( '#pardot-form-refresh i' ).css( 'visibility', 'visible' );

			// Toggle visibility class on status message span
			$( '.br-rc-form-update-status' ).toggleClass( 'br-rc-visible br-rc-hidden' );

			// Reset visibilty class on status message span after short interval
			setTimeout( function() {
				$( '.br-rc-form-update-status' ).toggleClass( 'br-rc-visible br-rc-hidden' );				
			}, 1500);
		});	

	}

	// Register event on button click
	refreshButton.on( 'click', pardotApiCall );
} );


/**
 * ?.? Update Pardot Form Stats
 *
 * Makes call to Pardot API and refreshes form stats.
 *
 *
**/
jQuery( document ).ready( function( $ ) {
	var refreshButton = $( '#pardot-stat-refresh' );

	// Data passed in AJAX call
	var data = {
			'action': 'br_rc_pardot_stat_refresh',

			// Set via localize_script
			'br_rc_pardot_manual_refresh': ajax_object.br_rc_pardot_manual_refresh,
			'post_id': ajax_object.post_id
	};

	function pardotApiCall( e ) {

		// Add loader class to button and
		// set font-awesome element to hidden
		$( '#pardot-stat-refresh' ).addClass( 'br-rc-loading' );
		$( '#pardot-stat-refresh i' ).css( 'visibility', 'hidden' );

		// Prevent submission/save of post
		e.preventDefault();

		// Make AJAX call
		// ajax_object passed via localize script
		$.post( ajax_object.ajax_url, data, function( responseOutput ) {

			// Replace section with response from handler
			$( '#br-rc-pardot-stat-section' ).fadeOut( 
												500,
												function(){ 
													$(this).html( responseOutput )
														   .fadeIn( 500 ) 
												} );

			// Remove loader class and
			// toggle font-awesome visibility
			$( '#pardot-stat-refresh' ).removeClass( 'br-rc-loading' );
			$( '#pardot-stat-refresh i' ).css( 'visibility', 'visible' );

			// Toggle visibility class on status message span
			$( '.br-rc-stat-update-status' ).toggleClass( 'br-rc-visible br-rc-hidden' );

			// Reset visibilty class on status message span after short interval
			setTimeout( function() {
				$( '.br-rc-stat-update-status' ).toggleClass( 'br-rc-visible br-rc-hidden' );				
			}, 1500);
		});	

	}

	// Register event on button click
	refreshButton.on( 'click', pardotApiCall );
} );


/**
 * ?.? Transcript Module Back-end
 *
 * Detects value of transcript toggle and set hide class where appropriate.
**/
jQuery( document ).ready( function( $ ) {

	// Vars
	var transcriptTextarea = $( '#wp-br_rc_transcript_text-wrap' ); // The transcript editor
	var transcriptToggle = $( '#br-rc-transcript-toggle' ); // The transcript toggle
	var	selectedValue = transcriptToggle.val(); // The selected transcript value

	// If toggle select is false/0, set hide class
	if ( selectedValue == 0 ) {
		transcriptTextarea.addClass( 'br-rc-hide' );
	}

	// Call handler
	function toggleTextArea() {

		// Toggle hide class
		transcriptTextarea.toggleClass( 'br-rc-hide' );
	}

	// Set event listener on change event of transcript toggle
	transcriptToggle.on( 'change', toggleTextArea );

});



/**
 * ?.? Form Stats Input Change
 *
 * 
**/
jQuery( document ).ready( function( $ ) {

	// Vars
	var formSelect = $( '[name="br_rc_pardot_form_url"]' );
	var statSection  = $( '#br_rc_pardot_stat_intro' );
	
	// Call handler
	function displayMessage() {

		var message = '<p>Update the resource to refresh the available form statistics.</p>';

		// Display message in place of form
		statSection.fadeOut( 500, function(){ $(this).html( message ).fadeIn( 500 ) } );		
	}

	// Set event listener on change event of transcript toggle
	formSelect.on( 'change', displayMessage );

});


/*--------------------------------------------------------------
?.? Front-end
--------------------------------------------------------------*/
/**
 * ?.? Filter Category Select
 *
 * Alters the action depending upon the selected category.
**/
jQuery( document ).ready( function( $ ) {

	// Vars
	var resourceFilter = $( '#br-rc-filter' ); // The filter
	var categoriesSelect = $( '#Categories' ); // The categories
	var selectedValue = categoriesSelect.val(); // The currently selected value
	var actionAttribute = $( '#br-rc-filter' ).attr( 'action' ); // The action attribute

	// If value is already selected alter the action attribute of the filter
	resourceFilter.attr( 'action', actionAttribute + selectedValue );
	
	// Call handler
	function updateAction() {

		// Recheck the current selected value
		selectedValue = categoriesSelect.val();

		// Update thea action attribute of filter
		resourceFilter.attr( 'action', actionAttribute + selectedValue );
	}

	// Set event listener on change event of categories select input
	categoriesSelect.on( 'change', updateAction );

});


/**
 * ?.? Transcript Module Front-end
 *
 * Allows for functionality of slide toggle on button click.
**/
jQuery( document ).ready( function( $ ) {

	//Vars
	var transcriptDrawerToggle = $( '#br-rc-transcript-drawer-toggle' );
	var transcriptDrawer = $( '#br-rc-transcript-content' );
	var transcriptToggleIcon = $( '.fa-angle-down' );

	// Hide transcript drawer on load
	transcriptDrawer.hide();

	// Call hand;er
	function toggleTranscriptDrawer() {

		// Toggle visibility with slide
		// Asjust icon depending on drawer state
		transcriptDrawer.slideToggle( 500, function() {
			transcriptToggleIcon.toggleClass( 'fa-angle-down fa-angle-up' );
		} );
	}

	// Add event listener on click event of button click
	transcriptDrawerToggle.on( 'click', toggleTranscriptDrawer );	

});