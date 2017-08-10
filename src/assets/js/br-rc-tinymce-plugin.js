/**
 * WYSIWYG Pardot Integration
 *
 * Adds Pardot integration to Wordpress's WYSIWYG editor.
 *
**/
(function() {

	// Conditional upon logged-in Pardot status,
	// Build the CTA Type output
	var ctaTypeSelectOutput = ( pardotLoggedIn ) 
							? '<p class="select br-rc-custom-cta-type">' +
							'<label for="br_rc_custom_cta_type">CTA Type:</label>' +
							'<select id="br-rc-custom-cta-type" name="br_rc_custom_cta_type">' +
							'<option value="" selected disabled>Select Your CTA Type</option>' +
							'<option value="br-rc-pardot-custom-redirect-url">Pardot Custom Redirect</option>' +
							'<option value="br-rc-custom-url">Custom URL</option>' +
							'</select>' +
							'<p class="error"></p>' +
							'</p>'
							: '<p class="select br-rc-custom-cta-type">' +
							'<label for="br_rc_custom_cta_type">CTA Type:</label>' +
							'<select id="br-rc-custom-cta-type" name="br_rc_custom_cta_type">' +
							'<option value="" selected disabled>Select Your CTA Type</option>' +
							'<option value="br-rc-pardot-custom-redirect-url" disabled>Pardot Custom Redirects Are Currently Unavailable. Please Visit the Settings Page</option>' +
							'<option value="br-rc-custom-url">Custom URL</option>' +
							'</select>' +
							'<p class="error"></p>' +
							'</p>';

	tinymce.create('tinymce.plugins.BrRc', {
		/**
		* Initializes the plugin, this will be executed after the plugin has been created.
		* This call is done before the editor instance has finished it's initialization so use the onInit event
		* of the editor instance to intercept that event.
		*
		* @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		* @param {string} url Absolute URL to where the plugin is located.
		*/
		init : function(ed, url) {
			ed.addButton('customcta', {
				title : 'Insert Custom CTA',
				cmd : 'customctamodal',
			});
			ed.addCommand('customctamodal', function() {
				ed.windowManager.open({
					title: 'Insert Custom CTA',
					width: jQuery( window).width() * 0.5,
					inline: 1,
					id: 'custom-cta-modal',
					html: 
						'<div>'+
						'<form id="custom-cta" name="custom-cta" >' +
						'<p class="errors">Please fix the highlighted errors below:</p>' +

						// CTA Type Select
						ctaTypeSelectOutput +

						// Pardot Custom Redirect 
						'<p class="select br-rc-select-type br-rc-pardot-custom-redirect-url">' +
						'<label for="br_rc_pardot_custom_redirect_url">Pardot Custom Redirect URL:</label>' +
						customRedirectOutput +
						'<p class="error"></p>' +
						'</p>' +

						// Custom URL
						'<p class="select br-rc-select-type br-rc-custom-url">' +
						'<label for="br_rc_custom_url">Custom URL:</label>' +
						'<input type="text" id="br-rc-custom-url" name="br_rc_custom_url" />' +
						'<p class="error"></p>' +
						'</p>' +

						// CTA Text
						'<p class="text br-rc-custom-cta-text">' +
						'<label for="br_rc_custom_cta_text">CTA Text:</label>' +
						'<input id="br-rc-custom-cta-text" name="br_rc_custom_cta_text" type="text" />' +
						'<p class="error"></p>' +
						'</p>' +

						// CTA Colour
						'<p class="text br-rc-custom-cta-color">' +
						'<label>CTA Color:</label>' +
						'<input id="br-rc-custom-cta-color" name="br_rc_custom_cta_color" type="text" />' +
						'<p class="error"></p>' +
						'</p>' +

						// CTA Alignment
						'<p class="br-rc-pardot-custom-cta-alignment">' +
						'<label for="br_rc_custom_cta_alignment">CTA Alignment:</label>' +
						'<select id="br-rc-custom-cta-alignment" ' +
						'name="br_rc_custom_cta_alignment">' +
						'<option value="left">Left</option>' +
						'<option value="center">Centre</option>' +
						'<option value="right">Right</option>' +
						'</select>' +
						'</p>' +

						'</form>' +
						'</div>',
					buttons: [{
						text: 'Insert Custom CTA',
						id: 'custom-cta-modal-insert',
						onclick: function() {

							// Vars
							var ctaSelectType 		= jQuery( '.br-rc-custom-cta-type select' );
							var ctaSelectTypeValue 	= ctaSelectType.val();
							var ctaUrlValue 		= ( ctaSelectTypeValue == 'br-rc-pardot-custom-redirect-url' ) 
													? jQuery( '#br-rc-pardot-custom-redirect-url' ).val() 
													: jQuery( '#br-rc-custom-url' ).val();  
							var ctaText				= jQuery( '#br-rc-custom-cta-text' );
							var ctaTextValue		= ctaText.val();
							var ctaAlignment		= jQuery( '#br-rc-custom-cta-alignment' ).val();
							var ctaColor			= jQuery( '#br-rc-custom-cta-color' ).val();
							var errors				= jQuery( '#custom-cta-modal .errors');
							var modalForm			= jQuery( '#custom-cta' );
							var modalContent		= jQuery( '#custom-cta-modal-body' );
							var urlExpression		= new RegExp( /[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/ );
							var ctaUrlVerify		= ( ctaSelectTypeValue == 'br-rc-custom-url' )
													? ctaUrlValue.match( urlExpression )
													: true;
							var colorExpression		= new RegExp( /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/ ); 
							var ctaColorVerify		= ( ctaColor )
													? ctaColor.match( colorExpression )
													: true;

							// Clear any existing errors
							jQuery( '.error' ).css( 'display', 'none' );
							jQuery( '.input-error' ).removeClass( 'input-error' );
							errors.css( 'display', 'none' );

							// Error validation
							if ( ctaSelectType && 
								 ctaUrlValue && 
								 ctaTextValue && 
								 ctaUrlVerify && 
								 ctaColorVerify ) {
								var button  = '<div class="br-rc-custom-cta-container ';
									button += ctaAlignment;
									button += '" >';
									button += '<a href="';
									button += ctaUrlValue;
									button += '" class="br-rc-custom-cta" ';
									button += 'style="background-color: ';
									button += ctaColor
									button += '" >';
									button += ctaTextValue;
									button += '</a>';
									button += '</div>';
								tinyMCE.execCommand( 'mceInsertContent', false, button );
								tinyMCE.activeEditor.windowManager.close();
							} else {
								errors.css( 'display', 'block' );
								if ( !ctaSelectTypeValue ) {
									jQuery( '#br-rc-custom-cta-type' ).addClass( 'input-error' );
									jQuery( '.br-rc-custom-cta-type + .error' ).text( 'Please select a CTA type.' );
									jQuery( '.br-rc-custom-cta-type + .error' ).css( 'display', 'block' );
								}
								if ( !ctaUrlValue && ctaSelectTypeValue == 'br-rc-pardot-custom-redirect-url' ) {
									jQuery( '#br-rc-pardot-custom-redirect-url' ).addClass( 'input-error' );
									jQuery( '.br-rc-pardot-custom-redirect-url + .error' ).text( 'Please select a custom redirect URL.' );
									jQuery( '.br-rc-pardot-custom-redirect-url + .error' ).css( 'display', 'block' );
								} else if ( !ctaUrlValue && ctaSelectTypeValue == 'br-rc-custom-url' ) {
									jQuery( '#br-rc-custom-url' ).addClass( 'input-error' );
									jQuery( '.br-rc-custom-url + .error' ).text( 'Please specify a custom URL.' );
									jQuery( '.br-rc-custom-url + .error' ).css( 'display', 'block' );
								} else if ( ctaUrlValue && ctaSelectTypeValue == 'br-rc-custom-url' && !ctaUrlVerify ) {
									jQuery( '#br-rc-custom-url' ).addClass( 'input-error' );
									jQuery( '.br-rc-custom-url + .error' ).text( 'Please enter a valid URL.' );
									jQuery( '.br-rc-custom-url + .error' ).css( 'display', 'block' );
								}
								if ( !ctaColorVerify ) {
									jQuery( '#br-rc-custom-cta-color' ).addClass( 'input-error' );
									jQuery( '.br-rc-custom-cta-color + .error' ).text( 'Please enter a valid hexadecimal color code (#------).' );
									jQuery( '.br-rc-custom-cta-color + .error' ).css( 'display', 'block' );
								}
								if ( !ctaTextValue ) {
									jQuery( '#br-rc-custom-cta-text' ).addClass( 'input-error' );
									jQuery( '.br-rc-custom-cta-text + .error' ).text( 'Please set the CTA text.' );
									jQuery( '.br-rc-custom-cta-text + .error' ).css( 'display', 'block' );
								}
							}

							// Popup resizing
							modalContent.height( modalForm.height() );
						},
					}, {
						text: 'Cancel',
						id: 'custom-cta-modal-close',
						onclick: 'close'
					}],
					onOpen: function() {
						
						// Vars
						var ctaSelectType 		= jQuery( '.br-rc-custom-cta-type select' );
						var ctaSelectUrlInputs	= jQuery( '.br-rc-select-type');
						var modalForm 			= jQuery( '#custom-cta' );
						var modalContent		= jQuery( '#custom-cta-modal-body' );
						var errors				= jQuery( '#custom-cta-modal .errors');

						modalContent.height( modalForm.height() + 50 );

						// Set event handler
						function ctaTypeSelect(e) {
							
							// Get selected CTA type
							var ctaSelectTypeValue = ctaSelectType.val();

							// Hide all inputs
							ctaSelectUrlInputs.css( 'display', 'none' );

							// Show relevant input
							jQuery( '.' + ctaSelectTypeValue ).css( 'display', 'block' );

							// Clear error values
							jQuery( '.error' ).css( 'display', 'none' );
							jQuery( '.input-error' ).removeClass( 'input-error' );
							errors.css( 'display', 'none' );

							// Get adjusted height (with new input)
							var adjustedModalForm 		= jQuery( '#custom-cta' );
							var adjustedModalContent	= jQuery( '#custom-cta-modal-body' );

							// Popup resizing
							adjustedModalContent.height( adjustedModalForm.height() + 50 );
						}

						// Set event handler trigger
						ctaSelectType.on( 'change', ctaTypeSelect );
					}
				});
			});
	},

	/**
	* Creates control instances based in the incomming name. This method is normally not
	* needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
	* but you sometimes need to create more complex controls like listboxes, split buttons etc then this
	* method can be used to create those.
	*
	* @param {String} n Name of the control to create.
	* @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
	* @return {tinymce.ui.Control} New control instance or null if no control was created.
	*/
	createControl : function(n, cm) {
		return null;
	},

	/**
	* Returns information about the plugin as a name/value array.
	* The current keys are longname, author, authorurl, infourl and version.
	*
	* @return {Object} Name/value array containing information about the plugin.
	*/
	getInfo : function() {
		return {
			longname : 'BR RC Pardot Buttons',
			author : 'Nathan M. House',
			authorurl : 'http://nathanmhouse.com',
			infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
			version : "0.1"
		};
	}
});

// Register plugin
tinymce.PluginManager.add( 'br_rc', tinymce.plugins.BrRc );
})();