<?php
/**
 * Facebook Widget Class
 *
 * @Description: Main Facebook widget class
 */


/**
 * Adds Facebook Reviews Pro Widget
 */
class Facebook_Widget extends WP_Widget {

	/**
	 * Array of Widget Fields
	 *
	 * @var array
	 */
	public $widget_fields = array(
		'title'                 => '',
		'id'                    => '',
		'address'               => '',
		'phone'                 => '',
		'display_option'        => '',
		'cache'                 => '',
		'disable_title_output'  => '',
		'disable_map_scroll'    => '',
		'display_address'       => '',
		'display_phone'         => '',
		'disable_business_info' => '',
		'display_reviews'       => '1',
		'display_google_map'    => '',
		'google_map_position'   => '',
		'profile_img_size'      => '80x80',
		'reviews_option'        => '',
		'review_filter'         => '',
		'review_limit'          => '25',
		'reviewers_link'        => '1',
		'review_characters'     => '1',
		'review_char_limit'     => '250',
		'hide_rating'           => '',
		'custom_read_more'      => '',
		'hide_read_more'        => '',
		'hide_blank_rating'     => '',
		'hide_header'           => '',
		'hide_out_of_rating'    => '',
		'hide_facebook_image'   => '',
		'target_blank'          => '1',
		'no_follow'             => '1',
	);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
			'Facebook_Widget', // Base ID
			'Facebook Reviews Pro', // Name
			array( 'description' => esc_attr__( 'Display Facebook business ratings and reviews on your website.', 'facebook-reviews-pro' ), ) // Args
		);

		//Only Load scripts when widget or shortcode is active
		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'widget_frontend_scripts' ) );
		}

		//Widget Scripts / Styles
		add_action( 'admin_enqueue_scripts', array( $this, 'fb_widget_admin_scripts' ) );

		add_action( 'wp_ajax_clear_widget_cache', array( $this, 'clear_widget_cache' ) );

	}



	/**
	 * Widget Script Loading
	 *
	 * @description: Load Widget JS Script ONLY on Widget page
	 *
	 * @param $hook
	 *
	 * @return bool
	 */
	public function fb_widget_admin_scripts( $hook ) {

		if ( $hook !== 'widgets.php' ) {
			return false;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'fb_widget_admin_tooltips', plugins_url( 'assets/js/admin/tipsy' . $suffix . '.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'fb_widget_admin_tooltips' );

		wp_register_script( 'fb_widget_admin_scripts', plugins_url( 'assets/js/admin/admin-widget' . $suffix . '.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'fb_widget_admin_scripts' );

		wp_register_style( 'fb_widget_admin_css', plugins_url( 'assets/css/facebook-widget-pro-admin' . $suffix . '.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'fb_widget_admin_css' );

		return false;

	}


	/**
	 * Adds Facebook Reviews Pro Scripts
	 */
	public static function widget_frontend_scripts() {

		$settings = get_option( 'fb_widget_settings' );
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		//Load Google Maps API only if option to disable is NOT set
		if ( ! isset( $settings['fb_widget_disable_gmap'] ) || $settings['fb_widget_disable_gmap'] == 0 ) {
			wp_register_script( 'fb_google_maps_api', 'https://maps.googleapis.com/maps/api/js' );
			wp_enqueue_script( 'fb_google_maps_api' );

		}

		//Frontend Scripts
		wp_register_script( 'fb_widget_js', FB_WIDGET_PRO_URL . '/assets/js/frontend/facebook-widget-pro' . $suffix . '.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fb_widget_js' );


		//Facebook Reviews Pro CSS
		if ( ! isset( $settings["fb_widget_disable_css"] ) || $settings["fb_widget_disable_css"] == 0 ) {

			//Determine which version of the CSS to dish out
			//Register and enqueue the styles
			wp_register_style( 'facebook-widget-css', FB_WIDGET_PRO_URL . '/assets/css/facebook-widget-pro' . $suffix . '.css' );
			wp_enqueue_style( 'facebook-widget-css' );

		}

	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @return  string html
	 */
	public function widget( $args, $instance ) {

		//Facebook Widget Options
		$title                 = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$instance['widget_id'] = isset( $args['widget_id'] ) ? $args['widget_id'] : rand( 1, 99999 );
		$instance['align']     = empty( $instance['align'] ) ? '' : $instance['align'];

		//loop through options array and save variables for usage within function
		foreach ( $instance as $variable => $value ) {
			$instance[ $variable ] = ! isset( $instance[ $variable ] ) ? $this->widget_fields[ $variable ] : esc_attr( $instance[ $variable ] );
		}

		//Sanity Check
		if ( empty( $instance['id'] ) ) {
			return false;
		}

		// Use appropriate API depending on API Request Method option
		$response = $this->get_facebook_data( $instance );

		//Sanity Check - Ensure there's data from DB
		if(empty($response)) {
			return false;
		}

		// Instantiate output var
		$output = '';

		//Widget Output
		echo ! empty( $args['before_widget'] ) ? '' : $args['before_widget'];

		// if the title is set & the user hasn't disabled title output
		if ( $title && $instance['disable_title_output'] != 1 ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		//check for business response
		if ( isset( $response->data ) ) {
			$businesses = $response->data;
		} else {
			$businesses = array( $response );
		}

		//Error Check
		if ( isset( $response->error ) ) {
			$this->handle_facebook_api_error( $response );

			return false;
		}

		//Check for business data
		if ( ! isset( $businesses[0] ) ) {
			echo '<div class="facebook-error">' . esc_attr__( 'No results were returned from Facebook for this business.', 'facebook-reviews-pro' ) . '</div>';

			return false;
		}


		//API Returned valid results; no errors

		// Open link in new window if set
		if ( $instance['target_blank'] == '1' ) {
			$instance['target_blank'] = 'target="_blank" ';
		} else {
			$instance['target_blank'] = '';
		}

		// Add nofollow relation if set
		if ( $instance['no_follow'] == '1' ) {
			$instance['no_follow'] = 'rel="nofollow" ';
		} else {
			$instance['no_follow'] = '';
		}


		$rating_data = fwp_count_rating( $response );
		$response = $rating_data[ 'response' ];
		$rating = $rating_data[ 'rating' ];

		$jsonArray = json_encode( $response );

		//prepare pagination args if we can
		$pagination = fwp_make_pagination_array( $response );

		//Display Appropriate View per API selection
		$output .= include( 'template-parts/reviews.php' );

		echo empty( $after_widget ) ? '' : $after_widget;

		// localize for AJAX
		$params = array(
			'fwpPath'  => FB_WIDGET_PRO_PATH,
			'fwpURL'   => FB_WIDGET_PRO_URL,
			'business' => $businesses[0],
			'instance' => $instance
		);


		wp_localize_script( 'fb_widget_js', 'fwpParams', $params );

		//Output Widget Contents
		return $output;

	}

	/**
	 * Get Facebook Data
	 *
	 * @description:
	 *
	 * @param $instance
	 *
	 * @return mixed|void
	 */
	public function get_facebook_data( $instance ) {
		return fwp_get_facebook_data( $instance, $instance[ 'review_limit' ] );


	}

	/**
	 * Widget Update
	 * @description: Saves the widget options
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		//loop through options array and save to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			$instance[ $field ] = strip_tags( stripslashes( $new_instance[ $field ] ) );
		}

		// clear cache on settings changes
		delete_transient( '_fwpcache_' . $this->id );

		return $instance;
	}


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {

		$transient = '_fwpcache_' . $this->id;

		//loop through options array and save to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			$instance[ $field ] = ! isset( $instance[ $field ] ) ? $value : esc_attr( $instance[ $field ] );
		}

		/**
		 * @var: Get API Option: either Search or Business
		 */
		$api_option = get_option( 'fb_widget_settings' );

		//Verify that the API values have been input prior to output

		if ( ( ! empty( $api_option['enable_backup_key'] ) ) && ( empty( $api_option['fb_widget_consumer_key'] ) || empty( $api_option['fb_widget_consumer_secret'] ) || empty( $api_option['fb_widget_token'] ) || empty( $api_option['fb_widget_token_secret'] ) ) ) {
			//the user has not properly configured plugin so display a warning
			?>
			<div class="alert alert-red"><?php echo sprintf( esc_attr__( 'Please input your Facebook API information in the %1$splugin settings%2$s page prior to enabling Facebook Reviews Pro.', 'facebook-reviews-pro' ), '<a href="options-general.php?page=facebook-reviews-pro">', '</a>' ); ?></div>
			<?php
		} //The user has properly input Facebook API info so output widget form so output the widget contents
		else {

			include( 'widget-form.php' );

		} //endif check for Facebook API key inputs

	} //end form function

	/**
	 * Display Business Address
	 *
	 * @description: Displays the business address from Facebook
	 *
	 * @param $location
	 *
	 * @return string
	 */
	public static function display_address( $location ) {

		$output = '<address>';

		$street  = isset( $location->street ) ? $location->street : '';
		$state   = isset( $location->state ) ? $location->state : '';
		$zip     = isset( $location->zip ) ? $location->zip : '';
		$city    = isset( $location->city ) ? $location->city : '';
		$country = isset( $location->country ) ? $location->country : '';

		$output .= $street . '<br />' . $city . ', ' . $state . ' ' . $zip . '<br / >' . $country;

		$output .= '<address>';

		return apply_filters( 'facebook_widget_display_address', $output );

	}

	/**
	 * Facebook Profile Image Size
	 *
	 * @param $profile_img_size
	 * @param $choice
	 *
	 * @return mixed|void
	 */
	public static function profile_image_size( $profile_img_size, $choice ) {

		if ( $choice == 'size' ) {
			//Set profile image size
			switch ( $profile_img_size ) {
				case '40x40':
					$output = "width='40' height='40'";
					break;
				case '60x60':
					$output = "width='60' height='60'";
					break;
				case '80x80':
					$output = "width='80' height='80'";
					break;
				case '100x100':
					$output = "width='100' height='100'";
					break;
				default:
					$output = "width='60' height='60'";
			}
		} else {
			//Set profile image size
			switch ( $profile_img_size ) {
				case '40x40':
					$output = "fwp-size-40";
					break;
				case '60x60':
					$output = "fwp-size-60";
					break;
				case '80x80':
					$output = "fwp-size-80";
					break;
				case '100x100':
					$output = "fwp-size-100";
					break;
				default:
					$output = "fwp-size-60";
			}
		}

		return apply_filters( 'facebook_widget_profile_image_size', $output );

	}

	/*
	 * Handle Facebook Error Messages
	 */
	public function handle_facebook_api_error( $response ) {

		$output = '<div class="facebook-error">';

		if ( $response->error->id == 'EXCEEDED_REQS' ) {
			$output .= esc_attr__( 'The default Facebook API has exhausted its daily limit. Please enable your own API Key in your Facebook Reviews Pro settings.', 'facebook-reviews-pro' );
		} elseif ( $response->error->id == 'BUSINESS_UNAVAILABLE' ) {
			$output .= __( '<strong>Error:</strong> Business information is unavailable. Either you mistyped the Facebook biz ID or the business does not have any reviews.', 'facebook-reviews-pro' );
		} //output standard error
		else {
			if ( ! empty( $response->error->id ) ) {
				$output .= $response->error->id . ": ";
			}
			if ( ! empty( $response->error->field ) ) {
				$output .= $response->error->field . " - ";
			}
			$output .= $response->error->text;
		}
		$output .= '</div>';

		echo $output;

	}

	/**
	 * Time Since
	 *
	 * @description: Works out the time since the entry post, takes a an argument in unix time (seconds)
	 *
	 * @param     $date
	 * @param int $granularity
	 *
	 * @return string
	 */
	static public function get_time_since( $date, $granularity = 1 ) {
		return fwp_time_since( $date, $granularity );
	}


	/**
	 * AJAX Clear Widget Cache
	 *
	 * @description: Same handler function
	 */
	public function clear_widget_cache() {

		if ( isset( $_POST['transient_id'] ) ) {

			delete_transient( $_POST['transient_id'] );
			esc_attr_e( 'Cache cleared', 'facebook-reviews-pro' );

		} else {
			esc_attr_e( 'Error: Transient ID not set. Cache not cleared', 'facebook-reviews-pro' );
		}

		die();

	}

}

/*
 * @DESC: Register Twitter Widget Pro widget
 */
add_action( 'widgets_init', create_function( '', 'register_widget( "Facebook_Widget" );' ) );

/**
 * Facebook Widget cURL
 *
 * @DESC: CURLs the Facebook API with our url parameters and returns JSON response
 */
function fb_widget_curl( $signed_url ) {

	// Send Facebook API Call using WP's HTTP API
	$data = wp_remote_get( $signed_url );

	// make sure the response came back okay
	if ( is_wp_error( $data ) ) {
		return false;
	}

	//Use curl only if necessary
	if ( empty( $data['body'] ) ) {
		$ch = curl_init( $signed_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$data = curl_exec( $ch ); // Facebook response
		curl_close( $ch );
		$data     = fwp_update_http_for_ssl( $data );
		$response = json_decode( $data );
	} else {
		$data     = fwp_update_http_for_ssl( $data );
		$response = json_decode( $data['body'] );
	}

	// Handle Facebook response data
	return $response;

}

/**
 * Function update http for SSL
 *
 */
function fwp_update_http_for_ssl( $data ) {

	if ( ! empty( $data['body'] ) && is_ssl() ) {
		$data['body'] = str_replace( 'http:', 'https:', $data['body'] );
	} elseif ( is_ssl() ) {
		$data = str_replace( 'http:', 'https:', $data );
	}
	$data = str_replace( 'http:', 'https:', $data );

	return $data;

}

add_action( 'wp_ajax_fwp_pagination', 'fwp_review_pagination_callback' );
add_action( 'wp_ajax_nopriv_fwp_pagination', 'fwp_review_pagination_callback' );

/**
 * Handle pagination for reviews via AJAX
 */
function fwp_review_pagination_callback(){
	if( isset( $_GET[ 'link' ], $_GET[ 'direction' ], $_GET[ 'nonce' ], $_GET[ 'instance' ] ) ){
		
		//validate request nonce
		if( wp_verify_nonce( $_GET[ 'nonce' ], 'fwp-paging' ) ){
			if( filter_var( $_GET[ 'link' ], FILTER_VALIDATE_URL ) ){
				$query = parse_url( $_GET[ 'link' ], PHP_URL_QUERY );
				$parsed = array();
				parse_str( $query, $parsed );

				if( isset( $parsed[ 'fb-page-nonce' ] ) ){
					//validate pagination cursor nonce to make sure that the URL is kosher
					if( 'next' == $_GET[ 'direction' ] && isset( $parsed[ 'after' ] ) ){
						$nonce_action = $parsed[ 'after' ];
					}elseif( 'previous' == $_GET[ 'direction' ] && isset( $parsed[ 'before' ] ) ) {
						$nonce_action = $parsed[ 'before' ] ;
					}else{
						exit;
					}
					
					if( wp_verify_nonce( $parsed[ 'fb-page-nonce' ], $nonce_action ) ){
						$pagination_url = esc_url_raw(  $_GET['link'] );

						$instance_id =  $_GET[ 'instance' ];
						$instances = get_option( 'widget_facebook_widget', array() );
						$found = false;
						if( ! empty( $instances ) ){
							foreach ( $instances as $i => $instance ){
								if( isset( $instance[ 'id' ] ) && $instance_id == $instance[ 'id' ] ) {
									$found = true;
									break;
								}
							}

						}

						if( ! $found ){
							exit;
						}

						$response = fwp_get_facebook_data( $instance, $pagination_url );

						if( empty( $response ) ){
							status_header( 404 );
							exit;
						}elseif ( isset( $response->data ) ) {
							$businesses = $response->data;
						} else {
							$businesses = array( $response );
						}
						status_header( 200 );
						$rating = fwp_count_rating( $response );
						$pagination = fwp_make_pagination_array( $response );
						echo fwp_reviews_inner( $instance, $businesses, $rating, $pagination );
						exit;

						
					}
					
				}
				
			}
			
		}

		
	}

	exit;
	
}

/**
 * Get the reviews section markup
 * 
 * @param array $instance
 * @param object $businesses
 * @param string $rating
 * @param array $pagination
 *
 * @return string
 */
function fwp_reviews_inner( $instance, $businesses, $rating, $pagination ){
	if( is_array( $rating ) ){
		$rating = $rating[ 'rating' ];
	}
	ob_start();
	include( 'template-parts/review-section.php' );
	$inner =  ob_get_clean();
	return $inner;
}

/**
 * Get time since a date
 * 
 * @param $date
 * @param int $granularity
 *
 * @return string
 */
function fwp_time_since( $date, $granularity = 1 ){
	$difference = time() - $date;
	$retval     = '';
	$periods    = array(
		'decade' => 315360000,
		'year'   => 31536000,
		'month'  => 2628000,
		'week'   => 604800,
		'day'    => 86400,
		'hour'   => 3600,
		'minute' => 60,
		'second' => 1
	);

	foreach ( $periods as $key => $value ) {
		if ( $difference >= $value ) {
			$time = floor( $difference / $value );
			$difference %= $value;
			$retval .= ( $retval ? ' ' : '' ) . $time . ' ';
			$retval .= ( ( $time > 1 ) ? $key . 's' : $key );
			$granularity --;
		}
		if ( $granularity == '0' ) {
			break;
		}
	}

	return ' posted ' . $retval . ' ago';
}

/**
 * Get data from FB widget
 *
 * @param array $instance Widget instance
 * @param string $pagination Optional. URL For paginated requests
 *
 * @return bool|mixed|void
 */
function fwp_get_facebook_data( $instance, $pagination = '' ){
	if ( ! filter_var( $pagination, FILTER_VALIDATE_URL) ) {
		// Get our options
		$options = get_option( 'fb_widget_settings' );

		// Base unsigned URL
		$unsigned_url = 'https://graph.facebook.com/';

		//Build URL Parameters
		$tokens = json_decode( $options['fb_widget_page_tokens'], true );

		//Sanity Check
		if ( empty( $tokens ) ) {
			return false;
		}

		$access_token = 0;
		foreach ( $tokens as $token ) {
			if ( $token['id'] == $instance['id'] ) {
				$access_token = $token['access_token'];
				break;
			}
		}

		if( ! is_numeric( $pagination ) ){
			$pagination = 25;
		}

		$urlparams = array(
			'access_token' => $access_token,
			'fields'       => "about,bio,ratings.limit($pagination),picture,name,website,link,location,phone,albums{cover_photo,name,picture}",
		);

		$unsigned_url .= $instance['id'];
		$query_string = http_build_query( $urlparams );
		$signed_url   = $unsigned_url . '?' . $query_string;
	}else{
		$signed_url = $pagination;
	}
	$cache        = isset( $instance['cache'] ) ? strtolower( $instance['cache'] ) : '';

	// Cache: cache option is enabled
	if (  $cache !== 'none' ) {

		//Setup Transient
		$transient = '_fwpcache_' . $instance['id'];

		$key = sanitize_key( md5( $signed_url ) );
		$cached_data  = get_transient( $transient );
		if( is_array( $cached_data ) && isset( $cached_data[ $key ] ) ){
			$response = $cached_data[ $key ];
		}else{
			$response = false;
		}

		// Check for an existing copy of our cached/transient data
		if ( $response === false  ) {

			// It wasn't there, so regenerate the data and save the transient
			//Assign Time to appropriate Math
			switch ( $cache ) {
				case '1 Hour':
					$expiration = 3600;
					break;
				case '3 Hours':
					$expiration = 3600 * 3;
					break;
				case '6 Hours':
					$expiration = 3600 * 6;
					break;
				case '12 Hours':
					$expiration = 60 * 60 * 12;
					break;
				case '1 Day':
					$expiration = 60 * 60 * 24;
					break;
				case '2 Days':
					$expiration = 60 * 60 * 48;
					break;
				case '1 Week':
					$expiration = 60 * 60 * 168;
					break;
				default:
					$expiration = 60 * 60 * 12;
					break;
			}


			// Cache data wasn't there, so regenerate the data and save the transient
			$response = fb_widget_curl( $signed_url );
			if( ! is_array( $cached_data ) ){
				$cached_data = array();
			}

			$cached_data[ $key ] = $response;
			
			set_transient( $transient, $cached_data, $expiration );

		}

	} else {
		//No Cache option enabled;
		$response = fb_widget_curl( $signed_url );

	}



	return apply_filters( 'facebook_widget_data', $response );
}


function fwp_count_rating( $response ){
	$rating                 = 0;
	$response->rating_count = 0;
	$response->rating_total = 0;
	if ( ! empty( $response->ratings->data ) ) {
		foreach ( $response->ratings->data as $line_rating ) {
			$rating += $line_rating->rating;
			$response->rating_count += 1;
		}
		$response->rating_total = $rating / $response->rating_count;
	}
	return array( 'response' => $response, 'rating' => $rating );
}


function fwp_make_pagination_array( $response ){
	$pagination = array(
		'next' => '',
		'previous' => ''
	);

	//for initial request
	if( isset( $response->ratings->paging ) ){
		if( isset( $response->ratings->paging->previous ) ){
			$pagination[ 'previous' ] = add_query_arg( 'fb-page-nonce', wp_create_nonce( $response->ratings->paging->cursors->before ), $response->ratings->paging->previous  );
		}

		if( isset( $response->ratings->paging->next ) ){
			$pagination[ 'next' ] = add_query_arg( 'fb-page-nonce', wp_create_nonce( $response->ratings->paging->cursors->after ), $response->ratings->paging->next  );
		}

	}
	//for paginated requests
	elseif ( isset( $response->paging ) ){
		if( isset( $response->paging->previous ) ){
			$pagination[ 'previous' ] = add_query_arg( 'fb-page-nonce', wp_create_nonce( $response->paging->cursors->before ), $response->paging->previous  );
		}

		if( isset( $response->paging->next ) ){
			$pagination[ 'next' ] = add_query_arg( 'fb-page-nonce', wp_create_nonce( $response->paging->cursors->after ), $response->paging->next  );
		}
	}

	return $pagination;
}