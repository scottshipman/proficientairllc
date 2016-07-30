<?php

if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( ! isset( $instance, $businesses, $rating ) ){
	return;
}

$the_reviews = array();
if( isset( $businesses[0]->ratings ) ){
	$the_reviews =  $businesses[0]->ratings->data;
}
if( isset( $businesses[0] ) && is_object( $businesses[0] ) && property_exists( $businesses[0], 'reviewer' ) ){
	$the_reviews = $businesses;

}

?>
<div id="facebook-business-reviews">
	<div class="facebook-business-reviews<?php if ( $instance['disable_business_info'] == 1 ) {
		echo ' no-business-info';
	} ?>">
		<?php
		//Get the Reviews
		if ( ! empty( $the_reviews ) ) {

			$counter = 0;
			//Loop through reviews
			foreach ( $the_reviews as $review ) {

				//Hide empty ratings?
				if ( ! empty( $instance['hide_blank_rating'] ) && empty( $review->review_text ) ) {
					continue;
				}

				//Counter increments AFTER hide blank ratings
				$counter ++;

				//Have we hit limit?
				if ( intval( $instance['review_limit'] ) < $counter ) {
					continue;
				}


				//Review Filter: Skip if review doesn't meet min rating requirement
				if ( $instance['review_filter'] !== 'none' && $review->rating < intval( $instance['review_filter'] ) ) {
					continue;
				}
				?>
				<div class="facebook-review fb-widget-clearfix">
					<div class="fb-widget-top fb-widget-clearfix">
						<div class="fb-widget-reviewer-avatar">
							<a href="https://facebook.com/<?php echo $review->reviewer->id ?>" <?php echo $instance['target_blank'] ?> <?php echo $instance['no_follow'] ?> title="<?php echo $review->reviewer->name ?>"><img border="0" alt="<?php echo $review->reviewer->name ?>" src="https://graph.facebook.com/<?php echo $review->reviewer->id ?>/picture"></a>
						</div>

						<div class="fb-widget-rating-wrap">
							<div class="facebook-review-name">
								<a href="https://facebook.com/<?php echo $review->reviewer->id ?>" <?php echo $instance['target_blank'] ?> <?php echo $instance['no_follow'] ?> title="<?php echo $review->reviewer->name ?>"><?php echo $review->reviewer->name; ?></a>
							</div>
							<?php
							//The Star Rating
							if ( $instance['hide_rating'] !== '1' ) {
								$star_rating = sprintf( _n( '%s star', '%s stars', $rating, 'facebook-reviews-pro' ), $review->rating );
								?>
								<div class="facebook-review-rating base-rate-<?php echo $review->rating; ?>"><?php echo apply_filters( 'fb_review_star_rating_text', $star_rating ); ?> &#8212;
									<?php
									for ( $i = 1; $i <= $review->rating; $i ++ ) {
										echo '&#9733;';
									} ?></div>
								<div class="review-time"><?php echo fwp_time_since( strtotime( $review->created_time ) ); ?></div>
							<?php } ?>
						</div>

					</div>

					<?php if ( isset( $review->review_text ) ) { ?>
						<div class="facebook-review-excerpt">
							<?php echo wpautop( $review->review_text ); ?>
						</div>
					<?php } ?>
				</div>

			<?php } //end foreach
		} //endif

		/**
		 * Widget pagination
		 */

		if( isset( $pagination ) && ( ! empty( $pagination[ 'next' ] ) || ! empty( $pagination[ 'previous' ] ) ) ){ ?>
			<nav class="fb-widget-rating-pagination-wrap" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fwp-paging' ) ); ?>" data-api="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
				<?php if ( ! empty( $pagination[ 'previous' ] ) ) {
					printf( '<a class="fb-widget-paging" style="float:left;" id="fb-widget-previous" href="#" data-graph-url="%s" data-direction="previous">%s</a>', esc_attr( $pagination[ 'previous' ] ), esc_html__( 'Previous', 'facebook-reviews-pro' ) );
				}

				if ( ! empty( $pagination[ 'next' ] ) ) {
					printf( '<a class="fb-widget-paging" style="float:right;" id="fb-widget-next" href="#" data-graph-url="%s" data-direction="next">%s</a>', esc_attr( $pagination[ 'next' ] ), esc_html__( 'Next', 'facebook-reviews-pro' ) );
				}?>
				<div style="clear: both"></div>
			</nav>
		<?php }



		?>


	</div>
</div>
