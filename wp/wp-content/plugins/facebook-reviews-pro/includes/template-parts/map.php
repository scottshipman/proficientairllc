<?php
/**
 * @description: Display Google Map with Facebook listings
 */
if( empty( $businesses[ 0 ]->location ) ){
	return;
}

$jsonArray = '{"results": [' . $jsonArray . ']}';

//If user wants to display Google Map
if ( $instance['display_google_map'] == '1' ) {
	?>
	<div class="facebook-map-container<?php if ( ! empty( $instance['google_map_position'] ) ) {
		echo ' facebook-map-' . sanitize_title( $instance['google_map_position'] );
	} ?>" <?php if ( $instance['disable_map_scroll'] == '1' ) {
		echo " data-map-scroll='false'";
	} ?>>
		<div class="facebook-map"></div>
	</div>

<?php } ?>
