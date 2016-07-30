<?php
/**
 * Business Information
 *
 * @description: Main Business Information Content
 */

//Format Ratings Properly
$rating       = 0;
$rating_count = 0;
$rating_total = 0;
if ( ! empty( $businesses[ $x ]->ratings->data ) ) {
	foreach ( $businesses[ $x ]->ratings->data as $line_rating ) {
		$rating += $line_rating->rating;
		$rating_count += 1;
	}
	$rating_total = round( $rating / $rating_count );
}
/**
 * Find the biggest business profile image
 */
foreach ( $businesses[ $x ]->albums->data as $album ) {
	if ( $album->name == 'Profile Pictures' ) {
		$profile_image = $album->picture->data->url;
	}
}

//Ensure we have a profile image, if not use fallback
if ( empty( $profile_image ) ) {
	if ( ! empty( $businesses[ $x ]->picture->data->url ) ) {
		$profile_image = $businesses[ $x ]->picture->data->url;
	} else {
		$profile_image = FB_WIDGET_PRO_URL . '/assets/images/blank-biz.png';
	}
}
?>

<div class="facebook-business-info fb-widget-clearfix <?php echo $this->profile_image_size( $instance['profile_img_size'], '' ); ?><?php echo $instance['display_reviews'] !== '1' ? ' fbw-hide-reviews' : ''; ?>">
	<div class="biz-img-wrap">
		<img class="picture" src="<?php echo $profile_image; ?>" alt="<?php echo esc_attr( $businesses[ $x ]->name ); ?>" <?php echo $this->profile_image_size( $instance['profile_img_size'], 'size' ); ?>/>
	</div>

	<div class="fb-widget-business-info fb-widget-clearfix">
		<?php if ( ! empty( $businesses[ $x ]->website ) ) { ?>
			<div class="facebook-business-name-wrap">
				<a class="facebook-business-name-link" <?php echo $instance['target_blank'] . $instance['no_follow']; ?> href="<?php echo esc_attr( $businesses[ $x ]->link ); ?>" title="<?php echo esc_attr( $businesses[ $x ]->name ); ?> Facebook page"><?php echo $businesses[ $x ]->name; ?></a>
			</div>
		<?php } else { ?>
			<?php echo $businesses[ $x ]->name; ?>
		<?php } ?>
		<div class="facebook-aggregate-rating">
			<?php if ( ! empty( $rating_count ) ) {

				//The Star Rating
				$star_rating = sprintf( _n( '%s star', '%s stars', intval( $rating_total ), 'facebook-reviews-pro' ), $rating_total );
				?>
				<span class="facebook-review-rating base-rate-<?php echo $rating_total; ?>"><?php echo apply_filters( 'fb_business_review_star_rating_text', $star_rating ); ?> &#8212;
					<?php
					//Star HTML Icons
					for ( $i = 1; $i <= $rating_total; $i ++ ) {
						echo '&#9733;';
					} ?></span>
				<span class="review-count"><?php echo esc_attr( $rating_count ); ?><?php _e( ' reviews', 'facebook-reviews-pro' ); ?></span>

			<?php } else { ?>

				<a href="<?php echo $businesses[ $x ]->link; ?>" <?php echo $instance['target_blank'] . $instance['no_follow']; ?> class="no-reviews-link"><?php echo apply_filters( 'facebook_no_reviews_message', esc_attr__( 'Be the first to review', 'facebook-widget-pro' ) . ' &raquo;' ); ?></a>


			<?php } ?>

		</div>

		<div class="facebook-logo-link-wrap">
			<a class="facebook-logo-link" href="<?php echo esc_attr( $businesses[ $x ]->link ); ?>" <?php echo $instance['target_blank'] . $instance['no_follow']; ?>><img src="<?php echo FB_WIDGET_PRO_URL . '/assets/images/facebook.png'; ?>" alt="<?php echo $businesses[ $x ]->name; ?> <?php esc_attr_e( 'on Facebook', 'facebook-reviews-pro' ); ?>" /></a>
		</div>

	</div>
	<?php if ( $instance['display_address'] == 1 || $instance['display_phone'] == 1 ) { ?>
		<div class="facebook-address-wrap">
			<?php
			/**
			 * Display Business Address & Phone Number
			 */

			//Address
			if ( $instance['display_address'] == 1 && ! empty( $businesses[ $x ]->location ) ) {
				echo $this->display_address( $businesses[ $x ]->location );
			}


			//Phone
			if ( $instance['display_phone'] == 1 && ! empty( $businesses[ $x ]->phone ) && false === strpos( $businesses[ $x ]->phone, 'not-applicable' ) ) { ?>
				<div class="fwp-phone"><?php echo $businesses[ $x ]->phone; ?></div>
			<?php } //endif phone	?>

		</div>
	<?php } ?>

</div><!--/.facebook-business-info -->