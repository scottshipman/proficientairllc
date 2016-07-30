<?php

/**
 *  WordImpress Licensing
 *
 * @description: Handles licencing for WordImpress products.
 */
class FB_Widget_Pro_Licensing {

	function __construct( $licence_args ) {

		$this->plugin_basename     = $licence_args['plugin_basename'];
		$this->settings_page       = $licence_args['settings_page'];
		$this->item_name           = $licence_args['item_name'];
		$this->store_url           = $licence_args['store_url'];
		$this->licence_key_setting = $licence_args['licence_key_setting'];
		$this->licence_key_option  = $licence_args['licence_key_option'];

		add_action( 'admin_init', array( $this, 'edd_wordimpress_register_option' ) );
		add_action( 'admin_init', array( $this, 'edd_wordimpress_activate_license' ) );
		add_action( 'admin_init', array( $this, 'edd_wordimpress_deactivate_license' ) );

		//enqueue Licence assets
		add_action( 'admin_enqueue_scripts', array( $this, 'register_licence_assets' ) );
		//AJAX Activate license
		add_action( 'wp_ajax_wordimpress_activate_license', array( $this, 'ajax_activate_license' ) );
		//disable on deactivation
		register_deactivation_hook( $this->plugin_basename, array( $this, 'plugin_deactivated' ) );

		//Admin Notices
		add_action( 'admin_notices', array( $this, 'license_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'license_admin_notices_ignore' ) );



	}

	/**
	 * Admin Notices for Licensing
	 */
	function license_admin_notice() {

		global $current_user;

		$user_id = $current_user->ID;

		// Check that the user hasn't already clicked to ignore the message and that they have appropriate permissions
		if ( ! get_user_meta( $user_id, $this->licence_key_setting . '_license_ignore_notice' ) && current_user_can( 'install_plugins' ) ) {
			//check for license
			$license = get_option( $this->licence_key_option );
			$status  = isset( $license["license_status"] ) ? $license["license_status"] : 'invalid';

			//display notice if no license valid or found
			if ( $status == 'invalid' || empty( $status ) ) {

				//ensures we're not redirect to admin pages using query string; ie '?=yelp_widget
				parse_str( $_SERVER['QUERY_STRING'], $params );

				$message = sprintf( __( 'Thank you for using %3$s. Please %1$sactivate your license%2$s for %3$s to receive support and updates. | %4$sHide Notice%2$s' ),
					'<a href="options-general.php?page=facebook-reviews-pro">',
					'</a>',
					$this->item_name,
					'<a href="?' . http_build_query( array_merge( $params, array( $this->licence_key_setting . '_license_ignore_notice' => '0' ) ) ) . '">'
				);

				echo '<div class="updated error"><p>';

				echo $message;

				echo '</p></div>';

			}

		}
		
	}

	/**
	 * Set Usermeta to ignore the
	 */
	function license_admin_notices_ignore() {
		global $current_user;

		$user_id = $current_user->ID;
		//If user clicks to ignore the notice, add that to their user meta
		if ( isset( $_GET[ $this->licence_key_setting . '_license_ignore_notice' ] ) && $_GET[ $this->licence_key_setting . '_license_ignore_notice' ] == '0' ) {
			add_user_meta( $user_id, $this->licence_key_setting . '_license_ignore_notice', 'true', true );
		}
	}


	/**
	 * Register assets
	 *
	 * @description: Loads JS and CSS for licence form
	 *
	 * @param $hook
	 *
	 * @return bool|string
	 */
	function register_licence_assets( $hook ) {

		if ( $hook !== $this->settings_page ) {
			return false;
		}
		//JS for AJAX Activation
		wp_register_script( 'wordimpress_licencing_js', plugins_url( 'licence/assets/js/licence.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'wordimpress_licencing_js' );

		// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( 'wordimpress_licencing_js', 'ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			)
		);

		//CSS
		wp_register_style( 'wordimpress_licencing_css', plugins_url( 'licence/assets/css/licence.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'wordimpress_licencing_css' );


	}

	/**
	 * Activate License
	 *
	 * @return bool
	 */
	function edd_wordimpress_activate_license() {

		//Listen for our activate button to be clicked & Bail if Not
		if ( ! isset( $_POST['edd_license_activate'] ) ) {
			return false;
		}

		//Only on Facebook Options Page do we activate Licenses
		if ( $_POST['option_page'] !== 'fbwp_licence_setting' ) {
			return false;
		}

		//run a quick security check
		if ( ! check_admin_referer( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ) ) {
			return false;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = $this->get_license();

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ) // the name of our product in EDD
		);

		// Call the WordImpress EDD API.
		$response = wp_remote_post( esc_url_raw( add_query_arg( $api_params, $this->store_url ) ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {

			//There was an error so output it
			if ( is_object( $response ) && isset( $response->errors ) ) {

				//Loop through response errors
				foreach ( $response->errors as $errors ) {

					//Output each error
					foreach ( $errors as $error ) {

						//Check for SSL error to provide more verbose explanation
						if ( $error == 'SSL connect error' ) {
							add_settings_error( 'facebook_reviews', 'facebook_license_activation_error', __( 'License Activation Error: ', 'facebook-reviews' ) . $error . '. ' . __( 'This can be easily fixed by contacting your website host and asking them to upgrade your server PHP version to 5.3+ and cURL to 7.39+', 'facebook-reviews' ) );
						} else {
							//Output all other
							add_settings_error( 'facebook_reviews', 'facebook_license_activation_error', __( 'License Activation Error: ' ) . $error );
						}


					}

				}

			}

			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"
		update_option( $this->licence_key_option,
			array(
				'license_key'        => $license,
				'license_item_name'  => $license_data->item_name,
				'license_expiration' => $license_data->expires,
				'license_status'     => $license_data->license,
				'license_name'       => $license_data->customer_name,
				'license_email'      => $license_data->customer_email,
				'license_payment_id' => $license_data->payment_id,
				'license_error'      => isset( $license_data->error ) ? $license_data->error : '',
			)
		);

	}


	/**
	 * Deactivate License
	 *
	 * @param bool $plugin_deactivate
	 *
	 * @return bool|void
	 */
	function edd_wordimpress_deactivate_license( $plugin_deactivate = false ) {

		// listen for our activate button to be clicked
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === $this->licence_key_setting && isset( $_POST['edd_license_deactivate'] ) || isset( $_POST['option_page'] ) && $_POST['option_page'] === $this->licence_key_setting && $plugin_deactivate === true ) {


			// run a quick security check
			if ( ! current_user_can( 'activate_plugins' ) && ! check_admin_referer( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ) ) {
				return;
			} // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = $this->get_license();

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ) // the name of our product in EDD
			);

			// Call the WordImpress EDD API.
			$response = wp_remote_post( $this->store_url, array(
				'timeout'   => 120,
				'sslverify' => false,
				'body'      => $api_params
			) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// $license_data->license will be either "deactivated" or "failed"
			if ( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
				delete_option( $this->licence_key_option );
				delete_option( $this->licence_key_status );
			}

		}
	}


	/**
	 * Get License
	 *
	 * Returns the license if in options
	 */
	function get_license() {
		if ( ! empty( $_POST[ $this->licence_key_option ]['license_key'] ) ) {
			$license = ! empty( $_POST[ $this->licence_key_option ]['license_key'] ) ? trim( $_POST[ $this->licence_key_option ]['license_key'] ) : '';
		} else {
			$current_options = get_option( $this->licence_key_option );
			$license         = $current_options["license_key"];
		}

		return $license;
	}


	/**
	 * Handles the output of the licence form in options
	 */
	function edd_wordimpress_license_page() {

		$license = get_option( $this->licence_key_option );

		$status = isset( $license['license_status'] ) ? $license['license_status'] : 'invalid';
		?>

		<div class="edd-wordimpress-license-wrap">
			<div class="handlediv" title="Click to toggle"><br></div>
			<h3 class="hndle"><span><?php _e( 'Plugin License?', 'facebook-reviews-pro' ); ?></span></h3>

			<div class="inside">
				<?php if ( $status !== false && $status == 'valid' ) { ?>

					<div class="license-stats">
						<p><strong><?php esc_attr_e( 'License Status:', 'facebook-reviews-pro' ); ?></strong>
							<span style="color: #468847;"><?php echo strtoupper( $license['license_status'] ); ?></span>
							(<?php echo $this->time_left_on_license( $license['license_expiration'] );
							esc_attr_e( ' Days Remaining', 'facebook-reviews-pro' ); ?>)
						</p>

						<?php
						$daysleft = $this->time_left_on_license( $license['license_expiration'] );
						if ( $daysleft < '16' ) { ?>

							<div class="alert alert-warning license">
								<p><?php esc_attr_e( 'Your license is expiring soon. Would you like to renew your license at 25% off the original price?', 'facebook-reviews-pro' ); ?>
									<a href="https://wordimpress.com/plugins/facebook-widget-pro" target="_blank">Click Here</a> and click the "Renewing a license key?" link at the checkout page.
								</p>
							</div>

						<?php } ?>
						<p>
							<strong><?php esc_attr_e( 'License Expiration:', 'facebook-reviews-pro' ); ?></strong> <?php echo $license['license_expiration']; ?>
						</p>

						<p>
							<strong><?php esc_attr_e( 'License Owner:', 'facebook-reviews-pro' ); ?></strong> <?php echo $license['license_name']; ?>
						</p>

						<p>
							<strong><?php _e( 'License Email:', 'facebook-reviews-pro' ); ?></strong> <?php echo $license['license_email']; ?>
						</p>

						<p>
							<strong><?php _e( 'License Payment ID:', 'facebook-reviews-pro' ); ?></strong> <?php echo $license['license_payment_id']; ?>
						</p>
					</div>

					<p class="alert alert-success license-status"><?php _e( 'Your license is active and you are receiving updates.', 'facebook-reviews-pro' ); ?></p>

					<?php
				} //Reached Activation?
				elseif ( $status == 'invalid' && isset( $license['license_error'] ) && $license['license_error'] == 'no_activations_left' ) { ?>

					<p class="alert alert-red license-status"><?php _e( 'The license you entered has reached the activation limit. To purchase more licenses please visit WordImpress.', 'facebook-reviews-pro' ); ?></p>

				<?php } elseif ( $status == 'invalid' && isset( $license['license_error'] ) && $license["license_error"] == 'missing' ) { ?>

					<p class="alert alert-red license-status"><?php _e( 'There was a problem with the license you entered. Please check that your license key is active and valid then reenter it below. If you are having trouble please contact support for assistance.', 'facebook-reviews-pro' ); ?></p>

				<?php } else { ?>

					<p class="alert alert-red license-status"><?php _e( 'Activate your license to receive automatic plugin updates for the life of your license.', 'facebook-reviews-pro' ); ?></p>

				<?php } ?>


				<form method="post" action="<?php echo get_permalink(); ?>">

					<?php settings_fields( $this->licence_key_setting ); ?>

					<input id="<?php echo $this->licence_key_option; ?>[license_key]" name="<?php echo $this->licence_key_option; ?>[license_key]" <?php echo ( $status !== false && $status == 'valid' ) ? 'type="password"' : 'type="text"'; ?> class="licence-input <?php echo ( $status !== false && $status == 'valid' ) ? ' license-active' : ' license-inactive'; ?>" value="<?php if ( $status !== false && $status == 'valid' ) {
						echo $license['license_key'];
					} ?>" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter license key here', 'facebook-reviews-pro' ); ?>"/>

					<label class="description licence-label" for="<?php echo $this->licence_key_option; ?>">
						<?php if ( $status !== false && $status == 'valid' ) {
							esc_attr_e( 'Your licence is active and valid.', 'facebook-reviews-pro' );
						} ?>
					</label>

					<?php if ( $status !== false && $status == 'valid' ) { ?>
						<?php wp_nonce_field( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ); ?>
						<input type="submit" class="button-secondary deactivate-license-btn" name="edd_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'facebook-reviews-pro' ); ?>"/>
						<?php
					} else {
						wp_nonce_field( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ); ?>
						<input type="submit" class="button-primary activate-license-btn" name="edd_license_activate" value="<?php esc_attr_e( 'Activate License', 'facebook-reviews-pro' ); ?>"/>
					<?php } ?>


					<?php //submit_button(); ?>

				</form>
			</div>
		</div>
		<?php
	}


	/**
	 * Registers the Settings
	 */
	function edd_wordimpress_register_option() {

		// creates our settings in the options table
		register_setting( $this->licence_key_setting, $this->licence_key_setting );

	}

	/**
	 * Returns Remaining Number of Days License is Active
	 *
	 * @param $exp_date
	 *
	 * @return float
	 */
	function time_left_on_license( $exp_date ) {
		$now       = time(); // or your date as well
		$your_date = strtotime( $exp_date );
		$datediff  = abs( $now - $your_date );

		return floor( $datediff / ( 60 * 60 * 24 ) );
	}

	/**
	 * Disable license on deactivation
	 *
	 * @see: http://wordpress.stackexchange.com/questions/25910/uninstall-activate-deactivate-a-plugin-typical-features-how-to/25979#25979
	 */
	public function plugin_deactivated() {
		// This will run when the plugin is deactivated, use to delete the database
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		return $this->edd_wordimpress_deactivate_license( $plugin_deactivate = true );
	}


}
