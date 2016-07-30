/**
 * Facebook Widget Pro - Main JS
 */
jQuery( function ( $ ) {
	var stylesArray = [
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers"    : [
					{"color": "#b1bdd6"}
				]
			}, {
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers"    : [
					{"color": "#ffffff"},
					{"weight": 0.2}
				]
			}, {
				"featureType": "water",
				"elementType": "labels.text.stroke",
				"stylers"    : [
					{"color": "#a0aecc"}
				]
			}, {
				"featureType": "landscape",
				"stylers"    : [
					{"color": "#e8e5e5"}
				]
			}, {
				"featureType": "administrative.province",
				"stylers"    : [
					{"weight": 0.5},
					{"color": "#d1d0cf"}
				]
			}, {
				"featureType": "poi.park",
				"stylers"    : [
					{"visibility": "on"},
					{"color": "#c5dea2"}
				]
			}, {
				"featureType": "road",
				"elementType": "geometry",
				"stylers"    : [
					{"color": "#f3f3f2"}
				]
			}, {
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers"    : [
					{"visibility": "on"},
					{"invert_lightness": true},
					{"color": "#787878"}
				]
			}, {
				"featureType": "administrative.country",
				"stylers"    : [
					{"color": "#868686"},
					{"weight": 0.7}
				]
			}, {
				"featureType": "administrative.country",
				"elementType": "labels.text.fill",
				"stylers"    : [
					{"color": "#999999"}
				]
			}, {
				"featureType": "poi.park",
				"elementType": "labels.text.stroke",
				"stylers"    : [
					{"color": "#ffffff"}
				]
			}, {
				"featureType": "administrative.country",
				"elementType": "labels.text.stroke",
				"stylers"    : [
					{"color": "#ffffff"},
					{"weight": 2.8}
				]
			}
		],
		$fwpMaps = $( '.facebook-map' );

	/**
	 * Loop through maps and initialize
	 */
	$fwpMaps.each( function ( index, value ) {

		var map;
		var centerPoint = new google.maps.LatLng( fwpParams.business.location.latitude, fwpParams.business.location.longitude );
		var mapOptions = {
			zoom            : 14,
			center          : centerPoint,
			mapTypeId       : google.maps.MapTypeId.ROADMAP,
			disableDefaultUI: true,
			zoomControl     : true,
			styles          : stylesArray
		};

		if ( fwpParams.instance.disable_map_scroll ) {
			mapOptions.scrollwheel = false;
			mapOptions.draggable = false;
		}

		map = new google.maps.Map( value, mapOptions );

		var marker = new google.maps.Marker( {
			position: centerPoint,
			map     : map,
			icon    : fwpParams.fwpURL + '/assets/images/pink-pin.png',
			title   : fwpParams.business.name
		} );

		marker.setMap( map );

	} );


	/**
	 * Add width size
	 */
	$( document ).ready( function () {

		//Check width of each widget & add class if it's under a certain widget
		$( '[id^=fb-widget]' ).each( function () {

			var width = $( this ).outerWidth();

			if ( width > 499 ) {
				$( this ).addClass( 'facebook-widget-wide' );
			} else if ( width < 300 ) {
				$( this ).addClass( 'facebook-widget-slim' );

				//Super slim?
				if ( width < 185 ) {
					$( this ).addClass( 'facebook-widget-super-slim' );
				}

			}


		} );

	} );

	/**
	 * Click handler for pagination
	 */
	function addPaginationHandler(){
		$( '.fb-widget-paging' ).on( 'click', function(e){
			e.preventDefault();
			var $this = $( this );
			var $parent = $this.parent();
			var data = {
				direction: $this.data( 'direction' ),
				link: $this.data( 'graph-url' ),
				nonce: $parent.data( 'nonce' ),
				action: 'fwp_pagination',
				instance: fwpParams.instance.id
			};

			$.ajax( {
				url: $parent.data( 'api' ),
				data: data,
				method: "get",
				complete: function( r ){
					$( '#facebook-business-reviews' ).html( r.responseText );

				}
			});



		});
	}

	//set click handler on document ready and re-set on AJAX complete.
	$( document ).ready( function(){
		addPaginationHandler();
	} );
	$( document ).ajaxComplete( function(){
		addPaginationHandler();
	} );

} );
