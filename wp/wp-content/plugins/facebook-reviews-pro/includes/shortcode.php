<?php

/**
 * Class Facebook_Shortcode
 *
 * @description: Facebook Main Shortcode Class
 *
 */
class Facebook_Shortcode extends Facebook_Widget {

	static function init() {
		add_shortcode( 'facebook_widget_pro', array( __CLASS__, 'handle_shortcode' ) );

	}

	static function handle_shortcode( $atts ) {

		//Only Load scripts when widget or shortcode is active
		parent::widget_frontend_scripts();

		//extract shortcode arguments
		$defaults = apply_filters( 'facebook_widget_pro_shortcode_defaults', array(
			'before_widget'         => '',
			'after_widget'          => '',
			'before_title'          => '<h3 class="fbw-widget-title widget-title">',
			'after_title'           => '</h3>',
			'title'                 => '',
			'id'                    => '',
			'address'               => '',
			'phone'                 => '',
			'display_option'        => '',
			'cache'                 => '',
			'disable_title_output'  => '',
			'disable_map_scroll'    => 'true',
			'display_address'       => '',
			'display_phone'         => '',
			'disable_business_info' => '',
			'display_reviews'       => 'true',
			'display_google_map'    => '',
			'google_map_position'   => '',
			'profile_img_size'      => '80x80',
			'reviews_option'        => '',
			'review_filter'         => '',
			'review_limit'          => '25',
			'reviewers_link'        => 'true',
			'review_characters'     => 'true',
			'review_char_limit'     => '250',
			'hide_rating'           => '',
			'custom_read_more'      => '',
			'hide_read_more'        => '',
			'hide_blank_rating'     => '',
			'hide_out_of_rating'    => '',
			'hide_facebook_image'   => '',
			'target_blank'          => 'true',
			'no_follow'             => 'true',
		) );

		$atts = shortcode_atts( $defaults, $atts, 'facebook_widget_pro' );

		//Display Address if true
		$atts['address'] = fwp_check_shortcode_value( $atts['address'] );

		//Display Phone if true
		$atts['phone'] = fwp_check_shortcode_value( $atts['phone'] );

		//Display Google Map if true
		$atts['display_google_map'] = fwp_check_shortcode_value( $atts['display_google_map'] );

		//Display Google Map if true
		$atts['disable_map_scroll'] = fwp_check_shortcode_value( $atts['disable_map_scroll'] );

		//Display Reviews if true
		$atts['display_reviews'] = fwp_check_shortcode_value( $atts['display_reviews'] );

		//Hide More Review if specified
		$atts['hide_read_more']    = fwp_check_shortcode_value( $atts['hide_read_more'] );

		//Hide header if specified
		$atts['disable_business_info']    = fwp_check_shortcode_value( $atts['disable_business_info'] );

		//Display Address
		$atts['display_address']    = fwp_check_shortcode_value( $atts['display_address'] );

		//Display Phone
		$atts['display_phone']    = fwp_check_shortcode_value( $atts['display_phone'] );

		//Google Map
		$atts['display_google_map']    = fwp_check_shortcode_value( $atts['display_google_map'] );

		//Disable Map Scroll
		$atts['disable_map_scroll']    = fwp_check_shortcode_value( $atts['disable_map_scroll'] );

		//Hide review rating
		$atts['hide_rating'] = fwp_check_shortcode_value( $atts['hide_rating'] );

		//Hide blank ratings
		$atts['hide_blank_rating'] = fwp_check_shortcode_value( $atts['hide_blank_rating'] );

		//Disable Title Output
		$atts['disable_title_output'] = fwp_check_shortcode_value( $atts['disable_title_output'] );

		//Handle links opening
		$atts['target_blank'] = fwp_check_shortcode_value( $atts['target_blank'] );

		//Handle No Follow
		$atts['no_follow'] = fwp_check_shortcode_value( $atts['no_follow'] );

		//Using ob_start to output shortcode within content appropriately
		ob_start();
		$shortcode_widget = new Facebook_Widget();
		$shortcode_widget->widget( $defaults, $atts );
		$shortcode = ob_get_contents();
		ob_end_clean();

		//Output our Widget
		return apply_filters( 'facebook_widget_pro_shortcode_output', $shortcode );

	}

}

Facebook_Shortcode::init();

/**
 * Check Value
 *
 * @description: Helper Function
 *
 * @param $attr
 *
 * @return string
 */
function fwp_check_shortcode_value( $attr ) {

	if ( $attr === "true" || $attr === "1" ) {
		$attr = "1";
	} else {
		$attr = '0';
	}

	return $attr;

}

