<?php
/**
 * Convert Facebook Tokens
 *
 */
function fbwp_convert_tokens() {

	$urlparams    = array(
		'grant_type'        => 'fb_exchange_token',
		'client_id'         => $_POST['id'],
		'client_secret'     => $_POST['secret'],
		'fb_exchange_token' => $_POST['data']['accessToken']
	);
	$query_string = http_build_query( $urlparams );
	$url          = "https://graph.facebook.com/oauth/access_token?" . $query_string;

	$request = wp_remote_get( $url );
	if ( ! is_wp_error( $request ) ) {
		$str       = wp_remote_retrieve_body( $request );
		$has_error = json_decode( $str, ARRAY_A );
		if ( ! empty( $has_error ) ) {
			wp_send_json_error( $has_error );
		}
		parse_str( $str, $data );

		$urlparams    = array(
			'access_token' => $data['access_token']
		);
		$query_string = http_build_query( $urlparams );
		$url          = "https://graph.facebook.com/" . $_POST['data']['userID'] . "/accounts?" . $query_string;
		$request      = wp_remote_get( $url );
		if ( ! is_wp_error( $request ) ) {
			$str    = wp_remote_retrieve_body( $request );
			$pages  = json_decode( $str, ARRAY_A );
			$tokens = array(
				'access_token' => $data['access_token'],
				'pages'        => $pages['data']
			);
			if ( ! empty( $data['expires'] ) ) {
				$tokens['expires'] = $data['expires'];
			}
			wp_send_json_success( $tokens );
		}
	}
	die;
}

add_action( 'wp_ajax_convert_fb_token', 'fbwp_convert_tokens' );


/**
 * Activation admin notice
 */
function fbwp_activation_admin_notice() {

	global $current_user, $pagenow;

	$user_id = $current_user->ID;

	//Only display on plugins page
	if ( $pagenow !== 'plugins.php' ) {
		return false;
	}

	//Only Display if NOT Dismissed -  Check that the user hasn't already clicked to ignore the message
	if ( get_user_meta( $user_id, 'fbwp_activation_ignore_notice' ) ) {
		return false;
	} ?>
	<style>
		div.updated.fwp,
		div.updated.fwp header,
		div.updated.fwp header img,
		div.updated.fwp header h3,
		div.updated.fwp .dismiss,
		.fwp-actions,
		.fwp-action,
		.fwp-action #mc_embed_signup,
		div.updated.fwp .fwp-action span.dashicons:before {
			-webkit-box-sizing: border-box;
			/* Safari/Chrome, other WebKit */
			-moz-box-sizing: border-box;
			/* Firefox, other Gecko */
			box-sizing: border-box;
			/* Opera/IE 8+ */
			width: 100%;
			position: relative;
			padding: 0;
			margin: 0;
			overflow: hidden;
			float: none;
			display: block;
			text-align: left;
		}

		.fwp-action a,
		.fwp-action a:hover,
		div.updated.fwp .fwp-action.mailchimp:hover,
		div.updated.fwp .fwp-action.mailchimp span {
			-webkit-transition: all 500ms ease-in-out;
			-moz-transition: all 500ms ease-in-out;
			-ms-transition: all 500ms ease-in-out;
			-o-transition: all 500ms ease-in-out;
			transition: all 500ms ease-in-out;
		}

		div.updated.fwp {
			margin: 1rem 0 2rem 0;
		}

		div.updated.fwp header h3 {
			line-height: 1.4;
		}

		@media screen and (min-width: 280px) {
			div.updated.fwp {
				border: 0;
				background: transparent;
				-webkit-box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
				box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
			}

			div.updated.fwp header {
				background: #5E79BF;
				color: white;
				position: relative;
				height: 5rem;
			}

			div.updated.fwp header img {
				display: none;
				max-width: 35px;
				margin: 23px 0 0 20px;
				float: left;
			}

			div.updated.fwp header h3 {
				float: left;
				max-width: 60%;
				margin: 1rem;
				display: inline-block;
				color: white;
			}

			div.updated.fwp a.dismiss {
				display: block;
				position: absolute;
				left: auto;
				top: 0;
				bottom: 0;
				right: 0;
				width: 6rem;
				background: rgba(255, 255, 255, .15);
				color: white;
				text-align: center;
			}

			.fwp a.dismiss:before {
				font-family: 'Dashicons';
				content: "\f153";
				display: inline-block;
				position: absolute;
				top: 50%;

				transform: translate(-50%);
				right: 40%;
				margin: auto;
				line-height: 0;
			}

			div.updated.fwp a.dismiss:hover {
				color: #777;
				background: rgba(255, 255, 255, .5)
			}

			/* END ACTIVATION HEADER
			 * START ACTIONS
			 */
			div.updated.fwp .fwp-action {
				display: table;
			}

			.fwp-action a,
			.fwp-action #mc_embed_signup {
				background: rgba(0, 0, 0, .1);
				color: rgba(51, 51, 51, 1);
				padding: 0 1rem 0 6rem;
				height: 4rem;
				display: table-cell;
				vertical-align: middle;
			}

			.fwp-action.mailchimp {
				margin-bottom: -1.5rem;
				top: -.5rem;
			}

			.fwp-action.mailchimp p {
				margin: 9px 0 0 0;
			}

			.fwp-action #mc_embed_signup form {
				display: inline-block;
			}

			div.updated.fwp .fwp-action span {
				display: block;
				position: absolute;
				left: 0;
				top: 0;
				bottom: 0;
				height: 100%;
				width: auto;
			}

			div.updated.fwp .fwp-action span.dashicons:before {
				padding: 2rem 1rem;
				color: #5E79BF;
				line-height: 0;
				top: 50%;
				transform: translateY(-50%);
				background: rgba(163, 163, 163, .25);
			}

			div.updated.fwp .fwp-action a:hover,
			div.updated.fwp .fwp-action.mailchimp:hover {
				background: rgba(0, 0, 0, .2);
			}

			div.updated.fwp .fwp-action a {
				text-decoration: none;
			}

			div.updated.fwp .fwp-action a,
			div.updated.fwp .fwp-action #mc_embed_signup {
				position: relative;
				overflow: visible;
			}

			.fwp-action #mc_embed_signup form,
			.fwp-action #mc_embed_signup form input#mce-EMAIL {
				width: 100%;
			}

			div.updated.fwp .mailchimp form input#mce-EMAIL + input.submit-button {
				display: block;
				position: relative;
				top: -1.75rem;
				float: right;
				right: 4px;
				border: 0;
				background: #cccccc;
				border-radius: 2px;
				font-size: 10px;
				color: white;
				cursor: pointer;
			}

			div.updated.fwp .mailchimp form input#mce-EMAIL:focus + input.submit-button {
				background: #5E79BF;
			}

			.fwp-action #mc_embed_signup form input#mce-EMAIL div#placeholder,
			input#mce-EMAIL:-webkit-input-placeholder {
				opacity: 0;
			}
		}

		@media screen and (min-width: 780px) {
			div.updated.fwp header h3 {
				line-height: 3;
			}

			div.updated.fwp .mailchimp form input#mce-EMAIL + input.submit-button {
				top: -1.55rem;
			}

			div.updated.fwp header img {
				display: inline-block;
			}

			div.updated.fwp header h3 {
				max-width: 50%;
			}

			.fwp-action {
				width: 30%;
				float: left;
			}

			div.updated.fwp .fwp-action a {

			}

			.fwp-action a,
			.fwp-action #mc_embed_signup {
				padding: 0 1rem 0 4rem;
			}

			div.updated.fwp .fwp-action span.dashicons:before {

			}

			div.updated.fwp .fwp-action.mailchimp {
				width: 40%;
			}
		}
	</style>
	<div class="updated fwp">
		<header>
			<img src="<?php echo FB_WIDGET_PRO_URL; ?>/assets/images/facebook-logo-transparent-icon.png" class="facebook-logo"/>

			<h3><?php _e( 'Thank you for installing Facebook Reviews Pro', 'facebook-reviews-pro' ); ?></h3>
			<a href="?fbwp_nag_ignore=0" class="dismiss"></a>
		</header>
		<div class="fwp-actions">
			<div class="fwp-action">
				<a href="<?php echo admin_url(); ?>options-general.php?page=facebook-reviews-pro">
					<span class="dashicons dashicons-admin-settings"></span><?php _e( 'Go to Settings', 'facebook-reviews-pro' ); ?>
				</a>
			</div>

			<div class="fwp-action">
				<a href="https://wordimpress.com/documentation/facebook-reviews-pro/" target="_blank"> <span class="dashicons
dashicons-media-document"></span> <?php _e( 'View Plugin Documentation', 'facebook-reviews-pro' ); ?></a>
			</div>

			<div class="fwp-action mailchimp">
				<script>
					jQuery(function ($) {
						var mcemail = $('#mce-EMAIL').val();
					});
				</script>
				<div id="mc_embed_signup">
					<span class="dashicons dashicons-edit"></span>

					<form action="//wordimpress.us3.list-manage.com/subscribe/post?u=3ccb75d68bda4381e2f45794c&amp;id=2dbd32ab83" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<div class="mc-field-group">
							<p>
								<small><?php _e( 'Get notified of plugin updates:', 'facebook-reviews-pro' ); ?></small>
							</p>
							<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="my.email@wordpress.com">

							<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="submit-button">
						</div>
						<div id="mce-responses" class="clear">
							<div class="response" id="mce-error-response" style="display:none"></div>
							<div class="response" id="mce-success-response" style="display:none"></div>
						</div>
						<div style="position: absolute; left: -5000px;">
							<input type="text" name="b_3ccb75d68bda4381e2f45794c_83609e2883" value="">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}


add_action( 'admin_notices', 'fbwp_activation_admin_notice' );

/**
 * Nag Ignore
 */
function fbwp_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset( $_GET['fbwp_nag_ignore'] ) && '0' == $_GET['fbwp_nag_ignore'] ) {
		add_user_meta( $user_id, 'fbwp_activation_ignore_notice', 'true', true );
	}
}

add_action( 'admin_init', 'fbwp_nag_ignore' );