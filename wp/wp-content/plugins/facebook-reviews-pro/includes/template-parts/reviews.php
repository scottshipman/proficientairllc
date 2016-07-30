<?php
/**
 * Facebook Business Information
 *
 * @description: Display Single Facebook Business using the Facebook Business API
 *  Only one business returned via this Facebook API
 * @api        : http://www.facebook.com/developers/documentation/v2/business
 */

$x = 0;
?>

<div id="fb-widget-<?php echo $instance['widget_id']; ?>" class="facebook-widget-pro facebook-widget-business<?php echo ! empty( $instance['align'] ) ? ' facebook-widget-' . strtolower( $instance['align'] ) : ''; ?>"<?php if ( ! empty( $width ) ) {
	echo "style='width:" . $instance['width'] . ";'";
} ?>>

	<?php
	/**
	 * Display Google Map ABOVE Results Option
	 */
	if ( $instance['google_map_position'] == 'above' || empty( $instance['google_map_position'] ) ) {
		include( 'map.php' );
	}

	/**
	 * Display Business information
	 * (if user hasn't checked to not display)
	 */
	if ( $instance['disable_business_info'] !== '1' ) {
		include( 'business-info.php' );
	}
	/**
	 * Display Reviews
	 */

	if ( $instance['display_reviews'] == '1' ) {

		echo fwp_reviews_inner( $instance, $businesses, $rating, $pagination );

	}

	//Display Google Map BELOW Results Option
	if ( $instance['google_map_position'] === 'below' ) {
		include( 'map.php' );
	}
	?>
</div><!--/.facebook-business -->

