<?php

/**
 * FBR_Shortcode_Generator class.
 */
class FBR_Shortcode_Generator {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_head', array( $this, 'add_shortcode_button' ), 20 );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ), 20 );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 20, 1 );

		// Tiny MCE button icon
		add_action( 'admin_head', array( __CLASS__, 'set_tinymce_button_icon' ) );

		add_action( 'wp_ajax_fbw_shortcode_iframe', array( $this, 'fbw_shortcode_iframe' ), 9 );
	}

	/**
	 * Add a button for the fbw shortcode to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ), 10 );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ), 10 );
		}
	}

	/**
	 * Add TinyMCE language function.
	 *
	 * @param array $arr
	 *
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr['fbw_shortcode_button'] = FB_WIDGET_PRO_PATH . '/assets/js/admin/editor_plugin_lang.php';

		return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {

		array_push( $buttons, '|', 'fbw_shortcode_button' );

		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 *
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {

		$plugin_array['fbw_shortcode_button'] = FB_WIDGET_PRO_URL . '/assets/js/admin/editor_plugin.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 *
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;

		return $ver;
	}

	/**
	 * Adds admin styles for setting the tinymce button icon
	 */
	public static function set_tinymce_button_icon() {
		?>
		<style>
			i.mce-i-fbw {
				font: 400 20px/1 dashicons;
				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px
			}

			#fbw_shortcode_dialog-body {
				background: #F1F1F1;
			}

			.fbw-shortcode-submit {
				margin: 0 -15px;
				position: fixed;
				bottom: 0;
				background: #FFF;
				width: 100%;
				padding: 15px;
				border-top: 1px solid #DDD;
			}

			div.facebook-id-set {
				clear: both;
				float: left;
				width: 100%;
			}

		</style>
		<?php
	}

	/**
	 * Display the contents of the iframe used when the fbw Shortcode Generator is clicked
	 * TinyMCE button is clicked.
	 *
	 * @return int
	 */
	public static function fbw_shortcode_iframe() {
		global $wp_scripts;
		set_current_screen( 'facebook-reviews-pro' );
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$settings = get_option( 'fb_widget_settings' );

		//Shortcode Generator Specific JS
		wp_register_script( 'fb_widget_admin_tooltips', plugins_url( 'assets/js/admin/tipsy' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		wp_enqueue_script( 'fb_widget_admin_tooltips' );

		wp_register_script( 'fbw_shortcode_admin_generator', FB_WIDGET_PRO_URL . '/assets/js/admin/shortcode-iframe' . $suffix . '.js', array( 'jquery' ) );
		wp_enqueue_script( 'fbw_shortcode_admin_generator' );

		//Styles
		wp_register_style( 'fb_widget_admin_css', plugins_url( 'assets/css/facebook-widget-pro-admin' . $suffix . '.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'fb_widget_admin_css' );

		iframe_header(); ?>

		<style>
			#fbw-wrap {
				margin: 0 1em;
				overflow: hidden;
				padding-bottom: 75px;
			}

			/* iFrame Styles */
			#fbw_settings label {
				margin-bottom: 5px;
				display: block;
			}

			div.fbw-shortcode-hidden-fields-wrap {
				display: none;
			}

			div.updated {
				width: 100%;
				float: left;
				box-sizing: border-box;
			}

			tr.fbw-row-separator td {
				background: #EDEDED;
				color: #333;
				font-size: 15px;
				font-weight: bold;
			}

			.fbw-edit-shortcode {
				margin: 15px 0 20px !important;
			}

		</style>
		<div class="wrap" id="fbw-wrap">
			<form id="fbw_settings" style="float: left; width: 100%;">
				<?php do_action( 'fbw_shortcode_iframe_before' ); ?>

				<div class="updated fbw-edit-shortcode" style="display: none;">
					<p><?php _e( '<strong>Edit Active Shortcode:</strong> Customize the options for this shortcode by adjusting the options below.', 'facebook-reviews-pro' ); ?></p>
				</div>

				<fieldset id="fbw_location_lookup_fields" class="fbw-place-search-wrap clear" style="margin:1em 0;">

					<div class="fbw-page-wrap">
						<label for="page_id"><?php _e( 'Select a Facebook Page', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'id' ); ?></label>

						<select class="widefat" id="page_id" name="page_id">
							<option value="">-- <?php esc_attr_e( 'Select a Facebook Page', 'facebook-reviews-pro' ); ?> --</option>
							<?php
							$page_tokens = json_decode( $settings['fb_widget_page_tokens'] );
							foreach ( $page_tokens as $page_token ) {
								echo '<option value="' . $page_token->id . '">' . $page_token->name . '</option>';
							}
							?>
						</select>
					</div>

					<div class="updated facebook-id-set" style="display: none;">
						<p><?php esc_attr_e( 'The Facebook page is set for this shortcode.', 'facebook-reviews-pro' ); ?></p>
					</div>

				</fieldset>

				<a href="#" class="fbw-toggle-shortcode-fields" style="display: none;box-shadow: none;margin: 0 0 20px;">&raquo; <?php echo sprintf( __( '%1$sToggle Additional Shortcode Options%2$s (all optional)', 'facebook-reviews-pro' ), '<strong>', '</strong>' ); ?>
				</a>

				<div class="fbw-shortcode-hidden-fields-wrap">

					<table class="widefat">
						<thead>
						<tr>
							<th class="row-title"><?php esc_attr_e( 'Name', 'facebook-reviews-pro' ); ?></th>
							<th><?php esc_attr_e( 'Option(s)', 'facebook-reviews-pro' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td class="row-title">
								<label for="fbw_widget_title"><?php _e( 'Title:', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'title' ); ?></label>
							</td>
							<td>
								<input type="text" id="fbw_widget_title" name="fbw_widget_title" class="widefat fbw-title" placeholder="<?php esc_attr_e( 'Enter a title...', 'facebook-reviews-pro' ); ?>" />
							</td>
						</tr>
						<!-- Display Options -->
						<tr class="fbw-row-separator">
							<td colspan="2">
								<?php esc_attr_e( 'Review Options', 'facebook-reviews-pro' ); ?>
							</td>
						</tr>
						<!-- Minimum Review Rating -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_review_filter"><?php esc_attr_e( 'Minimum Review Rating:', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'review_filter' ); ?></label>
							</td>
							<td>
								<select name="fbw_widget_review_filter" class="widefat" id="fbw_widget_review_filter"><?php
									$options = array(
										__( 'none', 'facebook-reviews-pro' ),
										__( '5', 'facebook-reviews-pro' ),
										__( '4', 'facebook-reviews-pro' ),
										__( '3', 'facebook-reviews-pro' ),
										__( '2', 'facebook-reviews-pro' ),
										__( '1', 'facebook-reviews-pro' ),
									);
									$default = 'none';
									//Counter for Option Values
									$counter = 0;

									foreach ( $options as $option ) {
										echo '<option value="' . $option . '" id="' . $option . '"', $default == $option ? ' selected="selected"' : '', '>', $option, '</option>';
										$counter ++;
									}
									?></select>
							</td>

						</tr>
						<!-- Review Limit -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_review_limit"><?php esc_attr_e( 'Limit Number of Reviews:', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'review_limit' ); ?></label>
							</td>
							<td>
								<input name="fbw_widget_review_limit" id="fbw_widget_review_limit" value="25" type="number" min="1" max="999" />
							</td>

						</tr>
						<!-- Hide Star Rating & Date -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_hide_rating"><?php _e( 'Hide Star Rating & Date', 'facebook-reviews-pro' ); ?>:<?php echo fbw_admin_tooltip( 'hide_rating' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_hide_rating" name="fbw_widget_hide_rating" class="fbw-hide-rating" />
							</td>
						</tr>
						<!-- Hide Blank Reviews -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_hide_blank_rating"><?php _e( 'Hide Blank Reviews', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'hide_blank_rating' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_hide_blank_rating" name="fbw_widget_hide_blank_rating" class="fbw-hide-blank-rating" />
							</td>
						</tr>
						<!-- Display Options -->
						<tr class="fbw-row-separator">
							<td colspan="2">
								<?php esc_attr_e( 'Display Options', 'facebook-reviews-pro' ); ?>
							</td>
						</tr>
						<!-- Profile Image Size -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_profile_img_size"><?php esc_attr_e( 'Business Profile Image Size:', 'facebook-reviews-pro' ); ?><?php echo fbw_admin_tooltip( 'profile_img_size' ); ?></label>
							</td>
							<td>
								<select name="fbw_widget_profile_img_size" class="widefat" id="fbw_widget_profile_img_size"><?php
									$options = array( '40x40', '60x60', '80x80', '100x100' );
									$default = '80x80';
									//Counter for Option Values
									$counter = 0;

									foreach ( $options as $option ) {
										echo '<option value="' . $option . '" id="' . $option . '"', $default == $option ? ' selected="selected"' : '', '>', $option, '</option>';
										$counter ++;
									}
									?></select>
							</td>

						</tr>
						<!-- Display Business Address -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_display_address"><?php _e( 'Display Business Address', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'display_address' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_display_address" name="fbw_widget_display_address" class="fbw-display-business-address" />
							</td>
						</tr>
						<!-- Display Business Phone -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_display_phone"><?php _e( 'Display Business Phone Number', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'display_phone' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_display_phone" name="fbw_widget_display_phone" class="fbw-display-business-phone" />
							</td>
						</tr>
						<!-- Hide Business Information -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_hide_header"><?php _e( 'Hide Business Information', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'hide_header' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_hide_header" name="fbw_widget_hide_header" class="fbw-hide-header" />
							</td>
						</tr>

						<!-- Google Map Options -->
						<tr class="fbw-row-separator">
							<td colspan="2">
								<?php esc_attr_e( 'Google Map Options', 'facebook-reviews-pro' ); ?>
							</td>
						</tr>
						<!-- Display Google Map -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_display_google_map"><?php _e( 'Display Google Map', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'display_google_map' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_display_google_map" name="fbw_widget_display_google_map" class="fbw-display-google-map" />
							</td>
						</tr>
						<!-- Disable Map Scroll -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_disable_map_scroll"><?php _e( 'Disable Map Scroll', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'disable_map_scroll' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_disable_map_scroll" name="fbw_widget_disable_map_scroll" class="fbw-disable-map-scroll" checked />
							</td>
						</tr>
						<!--- Google Map Position -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_google_map_position" class="fwp-radio-label"><?php _e( 'Map Position', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'disable_map_scroll' ) ?></label>
							</td>
							<td>
								<label><input type="radio" name="fbw_widget_google_map_position" value="above" checked><span class="facebook-method-label"><?php _e( 'Above Results', 'facebook-reviews-pro' ); ?></span></label>
								<br>
								<label><input type="radio" name="fbw_widget_google_map_position" value="below"><span class="facebook-method-label"><?php _e( 'Below Results', 'facebook-reviews-pro' ); ?></span>

							</td>
						</tr>
						<!-- Advanced Options -->
						<tr class="fbw-row-separator">
							<td colspan="2">
								<?php esc_attr_e( 'Advanced Options', 'facebook-reviews-pro' ); ?>
							</td>
						</tr>
						<!-- Cache -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_cache"><?php esc_attr_e( 'Cache Response', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'cache' ) ?></label>
							</td>
							<td>
								<select name="fbw_widget_cache" id="fbw_widget_cache" class="widefat">
									<?php
									$options = fbw_get_widget_cache_options();
									$cache   = '2 Days';
									/**
									 * Output Cache Options (set 2 Days as default for new widgets)
									 */
									foreach ( $options as $option ) {
										?>
										<option value="<?php echo $option; ?>"
										        id="<?php echo $option; ?>" <?php if ( $cache == $option || empty( $cache ) && $option == '2 Days' ) {
											echo ' selected="selected" ';
										} ?>>
											<?php echo $option; ?>
										</option>
										<?php $counter ++;
									} ?>
								</select>
							</td>
						</tr>

						<!-- Disable Title Output -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_disable_title_output"><?php _e( 'Disable Title Output', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'disable_title_output' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_disable_title_output" name="fbw_widget_disable_title_output" class="fbw-disable-title-output" />
							</td>
						</tr>
						<!-- Target Blank -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_target_blank"><?php _e( 'Open Links in New Window', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'target_blank' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_target_blank" name="fbw_widget_target_blank" class="fbw-target-blank" checked />
							</td>
						</tr>
						<!-- Nofollow -->
						<tr>
							<td class="row-title">
								<label for="fbw_widget_no_follow"><?php _e( 'Add rel="nofollow" to Links', 'facebook-reviews-pro' ); ?>: <?php echo fbw_admin_tooltip( 'no_follow' ) ?></label>
							</td>
							<td>
								<input type="checkbox" id="fbw_widget_no_follow" name="fbw_widget_no_follow" class="fbw-fbw_widget_no_follow" checked />
							</td>
						</tr>

						</tbody>
					</table>
				</div>

				<?php do_action( 'fbw_shortcode_iframe_after' ); ?>

				<fieldset class="fbw-shortcode-submit">
					<input id="fbw_submit" type="submit" class="button-small button-primary" value="<?php _e( 'Create Shortcode', 'facebook-reviews-pro' ); ?>" />
					<input id="fbw_cancel" type="button" class="button-small button-secondary" value="<?php _e( 'Cancel', 'facebook-reviews-pro' ); ?>" />
				</fieldset>

			</form>
		</div>


		<?php iframe_footer();
		exit();
	}
}

new FBR_Shortcode_Generator();
