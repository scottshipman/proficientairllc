/**
 * TinyMCE plugin
 *
 * @see: http://generatewp.com/take-shortcodes-ultimate-level/ (heavily referenced)
 */
(function () {

	tinymce.PluginManager.add( 'fbw_shortcode_button', function ( editor, url ) {

		var ed = tinymce.activeEditor;
		var sh_tag = 'facebook_widget_pro';


		/**
		 * Open Shortcode Generator Modal
		 *
		 * @param ui
		 * @param v
		 */
		function fbw_open_modal( ui, v ) {

			editor.windowManager.open( {
				title     : ed.getLang( 'fbw.shortcode_generator_title' ),
				id        : 'fbw_shortcode_dialog',
				width     : 600,
				height    : 450,
				resizable : true,
				scrollbars: true,
				url       : ajaxurl + '?action=fbw_shortcode_iframe'
			}, {
				shortcode       : ed.getLang( 'fbw.shortcode_tag' ),
				shortcode_params: window.decodeURIComponent( v )
			} );
		}

		//Add popup
		editor.addCommand( 'fbw_shortcode_popup', fbw_open_modal );

		//Add button
		editor.addButton( 'fbw_shortcode_button', {
			title  : ed.getLang( 'fbw.shortcode_generator_title' ),
			icon   : 'fbw dashicons-facebook',
			onclick: fbw_open_modal
		} );

		//replace from shortcode to an image placeholder
		editor.on( 'BeforeSetcontent', function ( event ) {
			event.content = fbw_replace_shortcode( event.content );
		} );

		//replace from image placeholder to shortcode
		editor.on( 'GetContent', function ( event ) {
			event.content = fbw_restore_shortcode( event.content );
		} );


		//open popup on placeholder double click
		editor.on( 'DblClick', function ( e ) {
			var cls = e.target.className.indexOf( 'wp-facebook-widget-pro' );
			var attributes = e.target.attributes['data-fbw-attr'].value;

			if ( e.target.nodeName == 'IMG' && cls > -1 ) {
				editor.execCommand( 'fbw_shortcode_popup', false, attributes );
			}
		} );

		/**
		 * Helper functions
		 */
		function getAttr( s, n ) {
			n = new RegExp( n + '=\"([^\"]+)\"', 'g' ).exec( s );
			return n ? window.decodeURIComponent( n[1] ) : '';
		}


		/**
		 * Facebook Replace Shortcode
		 *
		 * @param content
		 * @returns {XML|*|string|void}
		 */
		function fbw_replace_shortcode( content ) {
			return content.replace( /\[facebook_widget_pro([^\]]*)\]/g, function ( all, attr, con ) {
				return fbw_shortcode_html( 'wp-facebook-widget-pro', attr, con );
			} );
		}

		/**
		 * Restore Shortcodes
		 */
		function fbw_restore_shortcode( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(<\/p>)*/g, function ( match, image ) {
				var data = getAttr( image, 'data-fbw-attr' );

				if ( data ) {
					return '<p>[' + sh_tag + data + ']</p>';
				}
				return match;
			} );
		}

		/**
		 * HTML that Replaces Raw Shortcode
		 *
		 * @param cls class name
		 * @param data
		 * @param con
		 * @returns {string}
		 */
		function fbw_shortcode_html( cls, data, con ) {

			var placeholder = url + '/shortcode-placeholder.jpg';
			data = window.encodeURIComponent( data );

			return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-fbw-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
		}

	} );


})();