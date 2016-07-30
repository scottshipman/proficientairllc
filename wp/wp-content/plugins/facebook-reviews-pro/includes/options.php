<?php
/**
 * Admin options page.
 *
 * @description: Creates a page to set your OAuth settings for the Facebook API v2.
 */

add_action( 'admin_menu', 'fb_widget_add_options_page' );

include_once( FB_WIDGET_PRO_PATH . '/licence/licence.php' );

// Include Licensing
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include_once( FB_WIDGET_PRO_PATH . '/licence/classes/EDD_SL_Plugin_Updater.php' );
}


global $fbwplicencing, $store_url, $item_name, $FB_PLUGIN_meta;
$store_url = 'https://wordimpress.com';
$item_name = 'Facebook Reviews Pro';

//Licence Args
$licence_args = array(
	'plugin_basename'     => FB_WIDGET_PRO_BASENAME, //Name of License Option in DB
	'store_url'           => $store_url, //URL of license API
	'item_name'           => $item_name, //Name of License Option in DB
	'settings_page'       => 'settings_page_facebook-reviews-pro', // Name of the Settings hook

	'licence_key_setting' => 'fbwp_licence_setting', //Name of License Option in DB
	'licence_key_option'  => 'edd_facebook_license_key', //Name of License Option in DB
	'licence_key_status'  => 'edd_facebook_license_status', //Name of License Option in DB
);

$fbwplicencing = new FB_Widget_Pro_Licensing( $licence_args );


/**
 * Licensing
 */
function fwp_edd_sl_wordimpress_updater() {
	global $store_url, $item_name;

	$fb_plugin_meta = get_plugin_data( FB_WIDGET_PRO_PATH . '/' . FB_WIDGET_PRO_SLUG . '.php', false );
	$options        = get_option( 'edd_facebook_license_key' );
	$licence_key    = ! empty( $options['license_key'] ) ? trim( $options['license_key'] ) : '';

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( $store_url, FB_WIDGET_PRO_BASENAME, array(
			'version'   => $fb_plugin_meta['Version'], // current version number
			'license'   => $licence_key, // license key (used get_option above to retrieve from DB)
			'item_name' => $item_name, // name of this plugin
			'author'    => 'WordImpress' // author of this plugin
		)
	);
}

add_action( 'admin_init', 'fwp_edd_sl_wordimpress_updater' );

/**
 * Plugin Uninstall
 *
 * Delete options when uninstalled
 */
function fb_widget_uninstall() {
	delete_option( 'fb_widget_settings' );
	delete_option( 'fb_widget_consumer_key' );
	delete_option( 'fb_widget_consumer_secret' );
	delete_option( 'fb_widget_token' );
	delete_option( 'fb_widget_token_secret' );
}

register_uninstall_hook( __FILE__, 'fb_widget_uninstall' );

/**
 * Plugin Activation
 *
 * @description: Function runs when plugin is activated
 */
function fb_widget_activate() {
	$options = get_option( 'fb_widget_settings' );
}

register_activation_hook( __FILE__, 'fb_widget_activate' );

/**
 * Facebook Widget Convert Tokens
 *
 * @description: runs ajax call to get token so we don't get errors
 */
function fb_widget_convert_token() {

	$sig     = filter_input( INPUT_POST, 'sig', FILTER_SANITIZE_SPECIAL_CHARS );
	$request = wp_remote_post( FB_WIDGET_CONVERT_TOKEN_URL, array( 'body' => array( 'sig' => $sig ) ) );
	if ( ! is_wp_error( $request ) ) {
		$result = json_decode( wp_remote_retrieve_body( $request ), ARRAY_A );
		wp_send_json_success( $result );
	}
	wp_send_json_error( array( 'message' => $request->get_error_message() ) );
}

add_action( 'wp_ajax_fb_convert_token', 'fb_widget_convert_token' );


/**
 * FB Options Page
 *
 * @description: Adds the options page
 */
function fb_widget_add_options_page() {
	// Add the menu option under Settings, shows up as "Facebook API Settings" (second param)
	$page = add_submenu_page( 'options-general.php', //The parent page of this menu
		esc_attr__( 'Facebook Reviews Pro Settings', 'facebook-reviews-pro' ), //The Menu Title
		esc_attr__( 'Fb Reviews Pro', 'facebook-reviews-pro' ), //The Page Title
		'manage_options', // The capability required for access to this item
		'facebook-reviews-pro', // the slug to use for the page in the URL
		'fb_widget_options_form' ); // The function to call to render the page

	// Using registered $page handle to hook script load conditionally
	add_action( 'admin_print_scripts-' . $page, 'fwb_widget_options_scripts' );

}

/**
 * Widget Option Scripts
 *
 * @description: Add FB Widget Pro option scripts to admin head - will only be loaded on plugin options page
 */
function fwb_widget_options_scripts() {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	//register admin JS
	wp_register_script( 'fb_widget_options_js', FB_WIDGET_PRO_URL . '/assets/js/admin/options.js' );
	wp_enqueue_script( 'fb_widget_options_js' );

	wp_register_script( 'fb_widget_facebook_sdk', '//connect.facebook.net/en_US/sdk.js' );
	wp_enqueue_script( 'fb_widget_facebook_sdk' );

	//register our stylesheet
	wp_register_style( 'fb_widget_options_css_min', FB_WIDGET_PRO_URL . '/assets/css/facebook-widget-pro-admin' . $suffix . '.css' );
	// It will be called only on plugin admin page, enqueue our stylesheet here
	wp_enqueue_style( 'fb_widget_options_css_min' );
}

/**
 * Add links to Plugin listings view
 *
 * @param $links
 *
 * @return mixed
 */
function fbwp_add_plugin_page_links( $links, $file ) {

	if ( $file == FB_WIDGET_PRO_BASENAME ) {
		// Add Widget Page link to our plugin
		$link = fbwp_get_options_link();
		array_unshift( $links, $link );

		// Add Support Forum link to our plugin
		$link = fbwp_get_support_forum_link();
		array_unshift( $links, $link );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'fbwp_add_plugin_page_links', 10, 2 );


/**
 * Plugin Meta Links
 *
 * @param $meta
 * @param $file
 *
 * @return array
 */
function fbwp_add_plugin_meta_links( $meta, $file ) {
	if ( $file == FB_WIDGET_PRO_BASENAME ) {
		$meta[] = "<a href='https://wordpress.org/support/view/plugin-reviews/facebook-widget-pro' target='_blank' title='" . esc_attr__( 'Rate Facebook Reviews Pro', 'facebook-reviews-pro' ) . "'>" . esc_attr__( 'Rate Plugin', 'facebook-reviews-pro' ) . "</a>";
		$meta[] = __( 'Premium Version', 'facebook-reviews-pro' );
	}

	return $meta;
}

//add_filter( 'plugin_row_meta', 'fbwp_add_plugin_meta_links', 10, 2 );

/**
 * Support Link
 *
 * @param string $linkText
 *
 * @return string
 */
function fbwp_get_support_forum_link( $linkText = '' ) {
	if ( empty( $linkText ) ) {
		$linkText = esc_attr__( 'Support', 'facebook-reviews-pro' );
	}

	return '<a href="https://wordimpress.com/support/" target="_blank" title="Get Support">' . $linkText . '</a>';
}

function fbwp_get_options_link( $linkText = '' ) {
	if ( empty( $linkText ) ) {
		$linkText = esc_attr__( 'Settings', 'facebook-reviews-pro' );
	}

	return '<a href="options-general.php?page=facebook-reviews-pro">' . $linkText . '</a>';
}


/**
 * Initiate the FB Widget
 *
 * @param $file
 */
function fb_widget_init( $file ) {
	// Register the Facebook_Widget settings as a group
	register_setting( 'fb_widget_settings', 'fb_widget_settings', 'fb_widget_sanitize_callback' );

	//call register settings function
	add_action( 'admin_init', 'fwb_widget_options_scripts' );

}

add_action( 'admin_init', 'fb_widget_init' );

/**
 * Sanitize Callback for settings
 *
 * @param $settings
 *
 * @return mixed
 */
function fb_widget_sanitize_callback( $settings ) {
	$fields = array( 'fb_widget_api_id', 'fb_widget_app_secret', 'fb_widget_app_token' );
	foreach ( $fields as $field ) {
		if ( isset( $settings[ $field ] ) ) {
			$settings[ $field ] = trim( strip_tags( $settings[ $field ] ) );
		}
	}

	return $settings;

}


// Output the Facebook_Widget option setting value
function fb_widget_option( $setting, $options = false ) {
	if ( ! is_array( $options ) ) {
		$options = get_option( 'fb_widget_settings', array() );
	}

	$value = "";
	// If the old setting is set, output that
	if ( get_option( $setting ) != '' ) {
		$value = get_option( $setting );
	} elseif ( is_array( $options ) ) {
		if ( isset( $options[ $setting ] ) ) {
			$value = $options[ $setting ];
		} else {
			return false;
		}
	}

	return $value;

}


/**
 * Get Widget Cache Options
 *
 * @return array
 */
function fbw_get_widget_cache_options() {
	$options = array(
		__( 'None', 'facebook-reviews-pro' ),
		__( '1 Hour', 'facebook-reviews-pro' ),
		__( '3 Hours', 'facebook-reviews-pro' ),
		__( '6 Hours', 'facebook-reviews-pro' ),
		__( '12 Hours', 'facebook-reviews-pro' ),
		__( '1 Day', 'facebook-reviews-pro' ),
		__( '2 Days', 'facebook-reviews-pro' ),
		__( '1 Week', 'facebook-reviews-pro' )
	);

	return apply_filters( 'fbw_widget_cache_options', $options );
}

/**
 * Widget Option Update
 *
 * @param $setting
 * @param $value
 *
 * @return bool
 */
function fb_widget_option_update( $setting, $value ) {
	$options             = get_option( 'fb_widget_settings', array() );
	$options[ $setting ] = $value;

	return update_option( 'fb_widget_settings', $options );
}


/**
 * Options Page
 *
 * @description: Generate the admin form
 */
function fb_widget_options_form() {

	include 'options-page.php';

} //end fb_widget_options_form


/**
 *  Google Places Reviews Admin Tooltips
 *
 * @param $tip_name
 *
 * @return bool|string
 */
function fbw_admin_tooltip( $tip_name ) {

	$tip_text = '';

	//Ensure there's a tip requested
	if ( empty( $tip_name ) ) {
		return false;
	}

	switch ( $tip_name ) {
		case 'id':
			$tip_text = esc_attr__( 'Select a Facebook page to display reviews. You can only display reviews for pages that you are an administrator of.', 'facebook-reviews-pro' );
			break;
		case 'title':
			$tip_text = esc_attr__( 'The title text appears at the very top of the widget above all other elements.', 'facebook-reviews-pro' );
			break;
		case 'review_limit':
			$tip_text = esc_attr__( 'Limit the number of reviews displayed for this business to a set number.', 'facebook-reviews-pro' );
			break;
		case 'review_filter':
			$tip_text = esc_attr__( 'Filter bad reviews to prevent them from displaying.', 'facebook-reviews-pro' );
			break;
		case 'hide_rating':
			$tip_text = esc_attr__( 'Disable the individual star rating and date per each review. Useful for certain feeds and output situations.', 'facebook-reviews-pro' );
			break;
		case 'hide_blank_rating':
			$tip_text = esc_attr__( 'Hide ratings with no review text.', 'facebook-reviews-pro' );
			break;
		case 'profile_img_size':
			$tip_text = esc_attr__( 'Customize the width and height of the business\' Facebook profile image.', 'facebook-reviews-pro' );
			break;
		case 'disable_business_info':
			$tip_text = esc_attr__( 'Disable the main business information profile image, name, rating, address and phone (if enabled). Useful for displaying only maps and ratings in the widget.', 'facebook-reviews-pro' );
			break;
		case 'cache':
			$tip_text = esc_attr__( 'Caching data will save Facebook data to your database in order to speed up response times and conserve API requests. The suggested settings is 1 Day.', 'facebook-reviews-pro' );
			break;
		case 'disable_title_output':
			$tip_text = esc_attr__( 'The title output is content within the \'Widget Title\' field above. Disabling the title output may be useful for some themes.', 'facebook-reviews-pro' );
			break;
		case 'display_google_map':
			$tip_text = esc_attr__( 'Display a Facebook style Google Maps with a marker on the business location.', 'facebook-reviews-pro' );
			break;
		case 'disable_map_scroll':
			$tip_text = esc_attr__( 'This option prevents the map from zooming when a user is using a mouse scrolling over it.', 'facebook-reviews-pro' );
			break;
		case 'target_blank':
			$tip_text = esc_attr__( 'This option will add target=\'_blank\' to the widget\'s links. This is useful to keep users on your website.', 'facebook-reviews-pro' );
			break;
		case 'no_follow':
			$tip_text = esc_attr__( 'This option will add rel=\'nofollow\' to the widget\'s outgoing links. This option may be useful for SEO.', 'facebook-reviews-pro' );
			break;
	}

	return '<img src="' . FB_WIDGET_PRO_URL . '/assets/images/help.png" title="' . $tip_text . '" class="tooltip-info" width="16" height="16" />';

}
