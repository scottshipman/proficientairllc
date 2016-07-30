<?php
/**
 * FB Widget Form
 *
 * @description: Widget form options in WP-Admin
 */
$settings = get_option( 'fb_widget_settings' );
?>

<!-- Title -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'title' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>

<!-- Listing Options -->

<div class="toggle-api-option-2 toggle-item toggled">
	<!-- Business ID -->
	<p>
		<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Business Page', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'id' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>">
			<?php
			$page_tokens = json_decode( $settings['fb_widget_page_tokens'] );
			foreach ( $page_tokens as $page_token ) {
				echo '<option value="' . $page_token->id . '" ' . ( $instance['id'] == $page_token->id ? 'selected="selected"' : '' ) . '>' . $page_token->name . '</option>';
			}
			?>
		</select>
	</p>

	<p class="facebook-pages-reload"><?php esc_attr_e( 'Pages not showing?', 'facebook-reviews-pro' ); ?>
		<a href="<?php echo admin_url( 'options-general.php?page=facebook-reviews-pro' ); ?>"><?php esc_attr_e( 'Reload Pages', 'facebook-reviews-pro' ); ?></a> <?php esc_attr_e( 'in Settings', 'facebook-reviews-pro' ); ?>
	</p>

	<!-- Display Reviews -->
	<p>
		<input id="<?php echo $this->get_field_id( 'display_reviews' ); ?>" class="reviews-toggle" name="<?php echo $this->get_field_name( 'display_reviews' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['display_reviews'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'display_reviews' ); ?>"><?php esc_attr_e( 'Display Business Reviews', 'facebook-reviews-pro' ); ?></label>
	</p>

	<div class="reviews-toggle-container <?php if ( $instance['display_reviews'] == '1' ) {
		echo 'toggled';
	} ?>">

		<h4 class="facebook-toggler"><?php _e( 'Review Options:', 'facebook-reviews-pro' ); ?><span></span></h4>

		<div class="display-options toggle-item">

			<!-- Filter Reviews -->
			<p>
				<label for="<?php echo $this->get_field_id( 'review_filter' ); ?>"><?php _e( 'Minimum Review Rating', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'review_filter' ); ?></label>

				<select id="<?php echo $this->get_field_id( 'review_filter' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'review_filter' ); ?>">

					<option value="none" <?php if ( empty( $instance['review_filter'] ) || $instance['review_filter'] == 'No filter' ) {
						echo "selected='selected'";
					} ?>><?php _e( 'No filter', 'facebook-reviews-pro' ); ?>
					</option>
					<option value="5" <?php if ( $instance['review_filter'] == '5' ) {
						echo "selected='selected'";
					} ?>><?php _e( '5 Stars', 'facebook-reviews-pro' ); ?>
					</option>
					<option value="4" <?php if ( $instance['review_filter'] == '4' ) {
						echo "selected='selected'";
					} ?>><?php _e( '4 Stars', 'facebook-reviews-pro' ); ?>
					</option>
					<option value="3" <?php if ( $instance['review_filter'] == '3' ) {
						echo "selected='selected'";
					} ?>><?php _e( '3 Stars', 'facebook-reviews-pro' ); ?>
					</option>
					<option value="2" <?php if ( $instance['review_filter'] == '2' ) {
						echo "selected='selected'";
					} ?>><?php _e( '2 Stars', 'facebook-reviews-pro' ); ?>
					</option>
					<option value="1" <?php if ( $instance['review_filter'] == '1' ) {
						echo "selected='selected'";
					} ?>><?php _e( '1 Star', 'facebook-reviews-pro' ); ?>
					</option>

				</select>

			</p>

			<!-- Review Limit -->
			<p>
				<label for="<?php echo $this->get_field_id( 'review_limit' ); ?>">
					<?php esc_html_e( 'Limit Number of Reviews Per Page (max 25):', 'facebook-widget-reviews' ); ?><?php echo fbw_admin_tooltip( 'review_limit' ); ?>
				</label><br />
				<input name="<?php echo esc_attr( $this->get_field_name( 'review_limit' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'review_limit' ) ); ?>" value="<?php echo intval( $instance['review_limit'] ); ?>" type="number" min="1" max="25">
			</p>

			<!-- Disable rating and time output -->
			<p>
				<input id="<?php echo $this->get_field_id( 'hide_rating' ); ?>" name="<?php echo $this->get_field_name( 'hide_rating' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['hide_rating'] ); ?>/>
				<label for="<?php echo $this->get_field_id( 'hide_rating' ); ?>"><?php _e( 'Hide Star Rating and Date', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'hide_rating' ); ?></label>
			</p>
			<!-- Disable ratings without text -->
			<p>
				<input id="<?php echo $this->get_field_id( 'hide_blank_rating' ); ?>" name="<?php echo $this->get_field_name( 'hide_blank_rating' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['hide_blank_rating'] ); ?>/>
				<label for="<?php echo $this->get_field_id( 'hide_blank_rating' ); ?>"><?php _e( 'Hide Blank Reviews', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'hide_blank_rating' ); ?></label>
			</p>


		</div>

	</div>

</div>


<h4 class="facebook-toggler"><?php _e( 'Display Options:', 'facebook-reviews-pro' ); ?><span></span></h4>

<div class="display-options toggle-item">

	<!-- Profile Image Size -->
	<p>
		<label for="<?php echo $this->get_field_id( 'profile_img_size' ); ?>"><?php _e( 'Business Profile Image Size', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'profile_img_size' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'profile_img_size' ); ?>" id="<?php echo $this->get_field_id( 'profile_img_size' ); ?>" class="widefat">
			<?php
			$options = array( '40x40', '60x60', '80x80', '100x100' );
			foreach ( $options as $option ) {
				?>

				<option value="<?php echo $option; ?>" id="<?php echo $option; ?>" <?php if ( $instance['profile_img_size'] == $option || empty( $instance['profile_img_size'] ) && $option == '60x60' ) {
					echo 'selected="selected"';
				} ?>><?php echo $option; ?></option>

			<?php } ?>
		</select>
	</p>

	<!-- Disable title output checkbox -->
	<p>
		<input id="<?php echo $this->get_field_id( 'display_address' ); ?>" name="<?php echo $this->get_field_name( 'display_address' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['display_address'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'display_address' ); ?>"><?php _e( 'Display Business Address', 'facebook-reviews-pro' ); ?></label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'display_phone' ); ?>" name="<?php echo $this->get_field_name( 'display_phone' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['display_phone'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'display_phone' ); ?>"><?php _e( 'Display Business Phone Number', 'facebook-reviews-pro' ); ?></label>
	</p>
	<!-- Display phone -->

	<!-- Disable Business Name and Rating Information -->
	<p>
		<input id="<?php echo $this->get_field_id( 'disable_business_info' ); ?>" name="<?php echo $this->get_field_name( 'disable_business_info' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['disable_business_info'] ); ?> />
		<label for="<?php echo $this->get_field_id( 'disable_business_info' ); ?>"><?php _e( 'Hide Business Information', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'disable_business_info' ) ?></label>
	</p>


</div>

<h4 class="facebook-toggler"><?php _e( 'Google Map Options:', 'facebook-reviews-pro' ); ?><span></span></h4>

<div class="advanced-options toggle-item">

	<!-- Display Google Map checkbox -->
	<p>
		<input id="<?php echo $this->get_field_id( 'display_google_map' ); ?>" name="<?php echo $this->get_field_name( 'display_google_map' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['display_google_map'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'display_google_map' ); ?>"><?php _e( 'Display Google Map', 'facebook-reviews-pro' ); ?> <?php echo fbw_admin_tooltip( 'display_google_map' ) ?></label>
	</p>


	<!-- Google Map Scrollability -->
	<p>
		<input id="<?php echo $this->get_field_id( 'disable_map_scroll' ); ?>" name="<?php echo $this->get_field_name( 'disable_map_scroll' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['disable_map_scroll'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'disable_map_scroll' ); ?>"><?php _e( 'Disable Map Scroll', 'facebook-reviews-pro' ); ?> <?php echo fbw_admin_tooltip( 'disable_map_scroll' ) ?></label>
	</p>


	<!--- Google Map Position -->
	<p>
		<label id="<?php echo $this->get_field_id( 'google_map_position' ); ?>" for="<?php echo $this->get_field_id( 'google_map_position' ); ?>" class="fwp-radio-label"><?php _e( 'Map Position', 'facebook-reviews-pro' ); ?>:</label>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'google_map_position' ); ?>" value="above" <?php checked( 'above', $instance['google_map_position'] );
			if ( empty( $instance['google_map_position'] ) ) {
				echo "checked='checked'";
			} ?>><span class="facebook-method-label"><?php _e( 'Above Results', 'facebook-reviews-pro' ); ?></span></label>
		<br>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'google_map_position' ); ?>" value="below" <?php checked( 'below', $instance['google_map_position'] ); ?>><span class="facebook-method-label"><?php _e( 'Below Results', 'facebook-reviews-pro' ); ?></span>

	</p>


</div>


<h4 class="facebook-toggler"><?php _e( 'Advanced Options:', 'facebook-reviews-pro' ); ?><span></span></h4>

<div class="advanced-options toggle-item">

	<!-- Transient / Cache -->
	<p>
		<label for="<?php echo $this->get_field_id( 'cache' ); ?>"><?php _e( 'Cache Data', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'cache' ); ?></label>

		<select name="<?php echo $this->get_field_name( 'cache' ); ?>" id="<?php echo $this->get_field_id( 'cache' ); ?>" class="widefat">
			<?php $options = fbw_get_widget_cache_options();
			/**
			 * Output Cache Options (set 2 Days as default for new widgets)
			 */
			foreach ( $options as $option ) { ?>
				<option value="<?php echo $option; ?>" id="<?php echo $option; ?>" <?php if ( $instance['cache'] == $option || empty( $cache ) && $option == '1 Day' ) {
					echo ' selected="selected" ';
				} ?>>
					<?php echo $option; ?>
				</option>
			<?php } ?>
		</select>


	</p>

	<!-- Clear Cache Button -->
	<p class="clearfix">
		<span class="cache-message"></span>
		<a href="#" class="button fwp-clear-cache" title="Clear" data-transient-id="<?php echo $transient; ?>"><?php esc_attr_e( 'Clear Cache', 'facebook-widget-pro' ); ?></a>
		<span class="cache-clearing-loading spinner"></span>
	</p>


	<!-- Disable title output checkbox -->
	<p>
		<input id="<?php echo $this->get_field_id( 'disable_title_output' ); ?>" name="<?php echo $this->get_field_name( 'disable_title_output' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['disable_title_output'] ); ?>/>
		<label for="<?php echo $this->get_field_id( 'disable_title_output' ); ?>"><?php _e( 'Disable Title Output', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'disable_title_output' ); ?></label>
	</p>

	<!-- Open Links in New Window -->
	<p>
		<input id="<?php echo $this->get_field_id( 'target_blank' ); ?>" name="<?php echo $this->get_field_name( 'target_blank' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['target_blank'] ); ?> />
		<label for="<?php echo $this->get_field_id( 'target_blank' ); ?>"><?php _e( 'Open Links in New Window', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'target_blank' ); ?></label>
	</p>
	<!-- No Follow Links -->
	<p>
		<input id="<?php echo $this->get_field_id( 'no_follow' ); ?>" name="<?php echo $this->get_field_name( 'no_follow' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['no_follow'] ); ?> />
		<label for="<?php echo $this->get_field_id( 'no_follow' ); ?>"><?php _e( 'No Follow Links', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'no_follow' ); ?></label>
	</p>

</div>

<p class="fwp-widget-footer-links">
	<a href="https://wordimpress.com/documentation/facebook-reviews-pro/" target="_blank" class="new-window"><?php _e( 'Plugin Documentation', 'facebook-reviews-pro' ); ?></a>
	<a href="https://wordimpress.com/support/forum/facebook-reviews-pro/" target="_blank" class="new-window"><?php _e( 'Priority Support', 'facebook-reviews-pro' ); ?></a>
</p>
