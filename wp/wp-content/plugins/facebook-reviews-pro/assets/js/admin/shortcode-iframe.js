/**
 *  Faceboook Reviews JS: WP Admin Shortcode Generator
 *
 *  @description: JavaScripts for the shortcode generator iframe
 *
 *  @since: 1.0
 */
jQuery.noConflict();
(function ( $ ) {
	"use strict";

	var custom_params = '';
	var existing_shortcode = false;

	$( document ).ready( function () {

		//Cancel button (closes iframe modal)
		$( '#fbw_cancel' ).on( 'click', function ( e ) {
			top.tinymce.activeEditor.windowManager.close();
			e.preventDefault();
		} );

		custom_params = top.tinyMCE.activeEditor.windowManager.getParams();

		//Are there custom params?
		if ( custom_params.shortcode_params !== 'undefined' ) {
			existing_shortcode = true;
		}

		//Get things going for various functions
		init();

	} );

	// Init
	// @public
	function init() {
		fbw_tipsy();
		fbw_generator_submit();

		//iframe sizing
		setTimeout( function () {
			$( 'body.iframe' ).css( {height: 'auto'} );
		}, 200 );

		var hidden_fields_wrap = $( '.fbw-shortcode-hidden-fields-wrap' );
		var id_set_message = $( '.facebook-id-set' );
		var submit_wrap = $( '.fbw-shortcode-submit' );
		var more_options_toggle = $( '.fbw-toggle-shortcode-fields' );
		var edit_shortcode_notice = $( '.fbw-edit-shortcode' );

		//Facebook Page ID Set
		$( '#page_id' ).on( 'change', function () {
			if ( !$( this ).val() ) {
				//Hide options when val isn't set
				id_set_message.slideUp();
				submit_wrap.slideUp();
				more_options_toggle.slideUp();
				hidden_fields_wrap.slideUp();
				edit_shortcode_notice.slideUp();
				return false;
			} else if ( !edit_shortcode_notice.is( 'visible' ) ) {
				//Show additional options for new shortcodes
				id_set_message.slideDown();
				submit_wrap.slideDown();
				more_options_toggle.slideDown();
			}

		} );

		//Toggle fields
		more_options_toggle.on( 'click', function () {
			hidden_fields_wrap.slideToggle();
		} );

		//New or Existing Shortcode?
		if ( existing_shortcode === true ) {
			edit_shortcode_notice.show(); //show edit options
			hidden_fields_wrap.show(); //show table of options
			$( '#fbw_submit' ).val( 'Edit Shortcode' ); //Change submit button text
			fbw_set_existing_params( custom_params ); //Set default options
		}

	}


	/**
	 * Set Existing Options
	 *
	 * @description Sets the generator options according to the user's already preset shortcode configuration
	 *
	 * @param custom_params obj
	 */
	function fbw_set_existing_params( custom_params ) {

		//Set variables from passed custom_params
		var id = fbw_get_attr( custom_params.shortcode_params, 'id' ),
			title = fbw_get_attr( custom_params.shortcode_params, 'title' ),
			review_limit = fbw_get_attr( custom_params.shortcode_params, 'review_limit' ),
			cache = fbw_get_attr( custom_params.shortcode_params, 'cache' ),
			rating_filter = fbw_get_attr( custom_params.shortcode_params, 'review_filter' ),
			review_char_limit = fbw_get_attr( custom_params.shortcode_params, 'review_char_limit' ),
			profile_img_size = fbw_get_attr( custom_params.shortcode_params, 'profile_img_size' ),
			display_address = fbw_get_attr( custom_params.shortcode_params, 'display_address' ),
			display_phone = fbw_get_attr( custom_params.shortcode_params, 'display_phone' ),
			display_google_map = fbw_get_attr( custom_params.shortcode_params, 'display_google_map' ),
			disable_map_scroll = fbw_get_attr( custom_params.shortcode_params, 'disable_map_scroll' ),
			google_map_position = fbw_get_attr( custom_params.shortcode_params, 'google_map_position' ),
			disable_business_info = fbw_get_attr( custom_params.shortcode_params, 'disable_business_info' ),
			hide_rating = fbw_get_attr( custom_params.shortcode_params, 'hide_rating' ),
			hide_blank_rating = fbw_get_attr( custom_params.shortcode_params, 'hide_blank_rating' ),
			disable_title_output = fbw_get_attr( custom_params.shortcode_params, 'disable_title_output' ),
			target_blank = fbw_get_attr( custom_params.shortcode_params, 'target_blank' ),
			no_follow = fbw_get_attr( custom_params.shortcode_params, 'no_follow' );

		//Set Place ID (very important)
		if ( id ) {
			$( '#fbw_widget_place_id' ).val( id );
		} else {
			alert( 'There was no Place ID found for this shortcode. Please create a new one.' );
			return false;
		}

		//Change default settings to customized ones using the values of the variables set above
		if ( id ) {
			$( '#page_id' ).val( id );
		}
		if ( title ) {
			$( '#fbw_widget_title' ).val( title );
		}
		if ( rating_filter ) {
			$( '#fbw_widget_review_filter' ).val( rating_filter );
		}
		if ( review_limit ) {
			$( '#fbw_widget_review_limit' ).val( review_limit );
		}
		if ( cache ) {
			$( '#fbw_widget_cache' ).val( cache );
		}
		if ( disable_business_info == 'true' ) {
			$( '#fbw_widget_hide_header' ).prop( 'checked', true );
		}
		if ( display_address == 'true' ) {
			$( '#fbw_widget_display_address' ).prop( 'checked', true );
		}
		if ( display_phone == 'true' ) {
			$( '#fbw_widget_display_phone' ).prop( 'checked', true );
		}
		if ( display_google_map == 'true' ) {
			$( '#fbw_widget_display_google_map' ).prop( 'checked', true );
		}
		if ( disable_map_scroll == 'true' ) {
			$( '#fbw_widget_disable_map_scroll' ).prop( 'checked', true );
		}
		if ( google_map_position ) {
			$( '[name="fbw_widget_google_map_position"][value="' + google_map_position + '"]' ).prop( 'checked', true );
		}
		if ( hide_rating == 'true' ) {
			$( '#fbw_widget_hide_rating' ).prop( 'checked', true );
		}
		if ( hide_blank_rating == 'true' ) {
			$( '#fbw_widget_hide_blank_rating' ).prop( 'checked', true );
		}
		if ( profile_img_size ) {
			$( '#fbw_widget_profile_img_size' ).val( profile_img_size );
		}
		if ( disable_title_output == 'true' ) {
			$( '#fbw_widget_disable_title_output' ).prop( 'checked', true );
		}
		if ( no_follow == 'false' ) {
			$( '#fbw_widget_no_follow' ).prop( 'checked', false );
		}
		if ( target_blank == 'false' ) {
			$( '#fbw_widget_target_blank' ).prop( 'checked', false );
		}

	}


	/**
	 * Shortcode Generator On Submit
	 *
	 * @description: Outputs the shortcode in TinyMCE and does minor validation
	 */
	function fbw_generator_submit() {

		$( '#fbw_settings' ).on( 'submit', function ( e ) {

			e.preventDefault();

			//Set our variables
			var args = top.tinymce.activeEditor.windowManager.getParams(),
				fb_page_id = $( '[name="page_id"]' ).val(),
				title = $( '[name="fbw_widget_title"]' ).val(),
				review_limit = $( '[name="fbw_widget_review_limit"]' ).val(),
				review_filter = $( '[name="fbw_widget_review_filter"]' ).val(),
				hide_header = $( '[name="fbw_widget_hide_header"]' ).is( ':checked' ),
				display_address = $( '[name="fbw_widget_display_address"]' ).is( ':checked' ),
				display_phone = $( '[name="fbw_widget_display_phone"]' ).is( ':checked' ),
				display_google_map = $( '[name="fbw_widget_display_google_map"]' ).is( ':checked' ),
				disable_map_scroll = $( '[name="fbw_widget_disable_map_scroll"]' ).is( ':checked' ),
				google_map_position = $( '[name="fbw_widget_google_map_position"]:checked' ).val(),
				hide_rating = $( '[name="fbw_widget_hide_rating"]' ).is( ':checked' ),
				hide_blank_rating = $( '[name="fbw_widget_hide_blank_rating"]' ).is( ':checked' ),
				profile_img_size = $( '[name="fbw_widget_profile_img_size"]' ).val(),
				disable_title_output = $( '[name="fbw_widget_disable_title_output"]' ).is( ':checked' ),
				no_follow = $( '[name="fbw_widget_no_follow"]' ).is( ':checked' ),
				target_blank = $( '[name="fbw_widget_target_blank"]' ).is( ':checked' ),
				cache = $( '[name="fbw_widget_cache"]' ).val(),
				shortcode;

			//Let's do some validation to ensure the location's place ID is set
			if ( fb_page_id === '' ) {
				alert( 'Missing Location\'s Page ID. Please try reloading pages in the plugin settings.' );
				return false;
			}

			//Form the shortcode
			shortcode = '[' + args.shortcode;

			//Start with the ID
			if ( fb_page_id ) {
				shortcode += ' id="' + fb_page_id + '"';
			}

			//Title
			if ( title ) {
				shortcode += ' title="' + title + '"';
			}

			//review_limit
			if ( review_limit !== '25' ) {
				shortcode += ' review_limit="' + review_limit + '"';
			}

			//review_filter
			if ( review_filter !== 'none' ) {
				shortcode += ' review_filter="' + review_filter + '"';
			}

			//cache
			if ( cache !== '' && cache !== '2 Days' ) {
				shortcode += ' cache="' + cache + '"';
			}

			//hide_header
			if ( hide_header == true ) {
				shortcode += ' disable_business_info="true"';
			}

			//display_address
			if ( display_address == true ) {
				shortcode += ' display_address="true"';
			}

			//display_phone
			if ( display_phone == true ) {
				shortcode += ' display_phone="true"';
			}

			//display_google_map
			if ( display_google_map == true ) {
				shortcode += ' display_google_map="true"';
			}

			//disable_map_scroll
			if ( disable_map_scroll == true && display_google_map == true ) {
				shortcode += ' disable_map_scroll="true"';
			}

			//google_map_position
			if ( google_map_position && display_google_map == true ) {
				shortcode += ' google_map_position="' + google_map_position + '"';
			}

			//hide_rating
			if ( hide_rating == true ) {
				shortcode += ' hide_rating="true"';
			}

			//hide_blank_rating
			if ( hide_blank_rating == true ) {
				shortcode += ' hide_blank_rating="true"';
			}

			//profile_img_size
			if ( profile_img_size ) {
				shortcode += ' profile_img_size="' + profile_img_size + '"';
			}

			//no_follow
			if ( disable_title_output == true ) {
				shortcode += ' disable_title_output="true"';
			}

			//no_follow
			if ( no_follow !== true ) {
				shortcode += ' no_follow="false"';
			}

			//target_blank
			if ( target_blank !== true ) {
				shortcode += ' target_blank="false"';
			}

			shortcode += ']';

			top.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
			top.tinymce.activeEditor.windowManager.close();

		} );


	}

	/**
	 * Tooltips
	 */
	function fbw_tipsy() {
		//Tooltips for admins
		$( '.tooltip-info' ).tipsy( {
			fade    : true,
			html    : true,
			gravity : 'n',
			delayOut: 1000,
			delayIn : 500
		} );
	}

	/**
	 * Get Attribute
	 *
	 * @description: Helper function that plucks options from passed string
	 */
	function fbw_get_attr( s, n ) {
		n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
		return n ? window.decodeURIComponent( n[1] ) : '';
	}


})( jQuery );