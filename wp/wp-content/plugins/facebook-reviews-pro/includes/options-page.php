<?php
/**
 * Admin options page
 *
 * @description: Creates a page to set your OAuth settings for the Facebook API v2.
 */
?>
<div class="wrap" xmlns="http://www.w3.org/1999/html">

	<!-- Plugin Title -->
	<div id="fwp-title-wrap">
		<div id="icon-facebook" class=""></div>
		<h1><?php esc_attr_e( 'Facebook Reviews Pro Settings', 'facebook-reviews-pro' ); ?></h1>
		<label class="label-success label"><?php esc_attr_e( 'Premium Version', 'facebook-reviews-pro' ); ?></label>
	</div>


	<div class="metabox-holder">

		<div class="postbox-container" style="width:75%">
			<form id="facebook-settings" method="post" action="options.php">
				<div id="main-sortables" class="meta-box-sortables ui-sortable">

					<div class="postbox" id="facebook-widget-intro">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle">
							<span><?php esc_attr_e( 'Facebook Reviews Pro', 'facebook-reviews-pro' ); ?></span>
						</h3>

						<div class="inside">
							<p class="introduction"><?php _e( 'Thank you for choosing Facebook Reviews Pro. This plugin allows you to display Facebook Page reviews on your WordPress website with a flexible widget and shortcode.', 'facebook-reviews-pro' ); ?></p>

							<p>
								<strong><?php _e( 'Facebook Reviews Pro Activation Instructions:', 'facebook-reviews-pro' ); ?></strong>
							</p>

							<ol>
								<li><?php _e( 'Activate your license for Facebook Widget Pro using the metabox to the right. This ensures you will have access to support and plugin updates.', 'facebook-reviews-pro' ); ?></li>
								<li><?php _e( 'Once your license is activated, sign into Facebook or create an account if you do not have one already. Please note: your Facebook user will need to be an administrator of the page you want to display reviews due to the way Facebook\'s API security is handled.', 'facebook-reviews-pro' ); ?></li>
								<li><?php _e( 'Click the button below to connect to Facebook.', 'facebook-reviews-pro' ); ?></li>
								<li><?php _e( 'You will be redirected to Facebook and asked to authorize the WordImpress App. Please accept the authorization. Don\'t worry, it will never access any private data or post anything to your facebook pages.', 'facebook-reviews-pro' ); ?></li>
								<li><?php _e( 'When you return from Facebook, you should see a list of your Facebook pages in the metabox below.', 'facebook-reviews-pro' ); ?></li>
								<li><?php _e( 'If you see the list of pages, you are ready to use the plugin! You can now add the widget to your sidebars or use a shortcode. Click "Reload Pages" to update the list of pages or "Disconnect" to connect again.', 'facebook-reviews-pro' ); ?></li>
							</ol>

							<p>
								<strong><?php _e( 'Like this plugin?  Give it a like on Facebook:', 'facebook-reviews-pro' ); ?></strong>
							</p>

							<div class="social-items-wrap">

								<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FWordImpress%2F353658958080509&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=220596284639969" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>

								<a href="https://twitter.com/wordimpress" class="twitter-follow-button" data-show-count="false">Follow @wordimpress</a>
								<script>!function ( d, s, id ) {
										var js, fjs = d.getElementsByTagName( s )[0], p = /^http:/.test( d.location ) ? 'http' : 'https';
										if ( !d.getElementById( id ) ) {
											js = d.createElement( s );
											js.id = id;
											js.src = p + '://platform.twitter.com/widgets.js';
											fjs.parentNode.insertBefore( js, fjs );
										}
									}( document, 'script', 'twitter-wjs' );</script>
								<div class="google-plus">
									<!-- Place this tag where you want the +1 button to render. -->
									<div class="g-plusone" data-size="medium" data-annotation="inline" data-width="200" data-href="https://plus.google.com/117062083910623146392"></div>


									<!-- Place this tag after the last +1 button tag. -->
									<script type="text/javascript">
										(function () {
											var po = document.createElement( 'script' );
											po.type = 'text/javascript';
											po.async = true;
											po.src = 'https://apis.google.com/js/plusone.js';
											var s = document.getElementsByTagName( 'script' )[0];
											s.parentNode.insertBefore( po, s );
										})();
									</script>

								</div>
								<!--/.google-plus -->
							</div>
							<!--/.social-items-wrap -->

						</div>
						<!-- /.inside -->
					</div>
					<!-- /#facebook-widget-intro -->
					<?php
					// Tells WordPress that the options we registered are being handled by this form
					settings_fields( 'fb_widget_settings' );

					// Retrieve stored options, if any
					$options = get_option( 'fb_widget_settings' );
					?>

					<div class="postbox" id="api-options">

						<?php
						$is_connected = fb_widget_option( 'fb_widget_page_tokens', $options );
						if ( ! empty( $is_connected ) ) { ?>
							<h3 class="hndle">
								<span><?php _e( 'Facebook is Connected', 'facebook-reviews-pro' ); ?></span></h3>
						<?php } else { ?>
							<h3 class="hndle">
								<span><?php _e( 'Facebook is Not Connected', 'facebook-reviews-pro' ); ?></span>
							</h3>

						<?php } ?>

						<div class="inside api-info">
							<div id="fb_login_button" class="facebook-connect-wrap" style="<?php if ( ! empty( $is_connected ) ) {
								echo 'display:none;';
							} ?>">
								<a href="<?php echo FB_WIDGET_PRO_GET_TOKEN_URL; ?>" class="button button-primary facebook-connect-button"><?php _e( 'Connect to Facebook', 'facebook-reviews-pro' ); ?></a>
							</div>

							<div id="app-info-panel" style="<?php if ( empty( $is_connected ) ) {
								echo 'display:none;';
							} ?>">

								<label for="fb_widget_page_token" class="facebook-access-pages-label"><?php _e( 'Accessible Pages:', 'facebook-reviews-pro' ); ?> </label>

								<div id="fb_widget_page_token_preview">
									<?php
									$page_tokens = fb_widget_option( 'fb_widget_page_tokens', $options );
									$page_tokens = json_decode( $page_tokens, true );

									//Output list of accessible pages here:
									if ( ! empty( $page_tokens ) ) {
										foreach ( (array) $page_tokens as $page_token ) {

											echo '<div class="facebook-page-preview-wrap">';
											echo '<a href="https://facebook.com/' . $page_token['id'] . '" class="facebook-page-preview" target="_blank">';
											echo '<img src="https://graph.facebook.com/' . $page_token['id'] . '/picture" class="facebook-page-avatar" />';
											echo '<span class="facebook-page-name">' . $page_token['name'] . '</span>';
											echo '</a>';
											echo '</div>';
										}
									}
									?>

									<input type="hidden" id="fb_widget_page_tokens" name="fb_widget_settings[fb_widget_page_tokens]" value="<?php echo esc_attr( fb_widget_option( 'fb_widget_page_tokens', $options ) ); ?>" />
									<?php if ( ! empty( $page_tokens ) ) { ?>
										<a href="<?php echo FB_WIDGET_PRO_GET_TOKEN_URL; ?>" class="button reload-pages-button"><?php _e( 'Reload Pages', 'facebook-reviews-pro' ); ?></a>
										<a href="#" id="fb_disconnect" class="button facebook-disconnect"><?php _e( 'Disconnect', 'facebook-reviews-pro' ); ?></a>
									<?php } ?>

								</div>
							</div>
						</div>
						<!-- /.inside -->
					</div>
					<!-- /#api-settings -->
				</div>

				<!-- /.metabox-holder -->
			</form>
		</div>
		<!-- /#main-sortables -->
		<div class="alignright" style="width:24%">
			<div id="sidebar-sortables" class="meta-box-sortables ui-sortable">

				<div id="facebook-licence" class="postbox">
					<?php
					/**
					 * Output Licensing Fields
					 */
					global $fbwplicencing;
					if ( class_exists( 'FB_Widget_Pro_Licensing' ) ) {
						$fbwplicencing->edd_wordimpress_license_page();
					}
					?>
				</div>

				<div id="facebook-widget-pro-support" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php _e( 'Need Support?', 'facebook-reviews-pro' ); ?></span></h3>

					<div class="inside">
						<p><?php echo sprintf( esc_attr__( 'If you have any problems with this plugin or ideas for improvements or enhancements, please contact %1$sWordImpress support%2$s.', 'facebook-reviews-pro' ), '<a href="https://wordimpress.com/support/" target="_blank" class="new-window">', '</a>' ); ?></p>
					</div>
					<!-- /.inside -->
				</div>
				<!-- /.facebook-widget-pro-support -->

			</div>
			<!-- /.sidebar-sortables -->
			<div class="wip-buttons">
				<a href="https://wordimpress.com/plugins/business-reviews-bundle/?utm_source=wp-admin&utm_medium=Bundle%20Logo&utm_term=bundle-facebook-pro&utm_campaign=bundle-facebook-pro" class="bundle-link" target="_blank"><img src="<?php echo FB_WIDGET_PRO_URL; ?>/assets/images/bundle-banner-300x300.png" /></a>
				<a href="https://wordimpress.com/" class="wordimpress-link" target="_blank"></a>
			</div>

		</div>

	</div>
	<!-- /.postbox-container -->

</div><!-- /#wrap -->
<script>
	jQuery( function ( $ ) {
		var wrapper = $( '#fb_widget_page_token_preview' );

		<?php if( ! empty( $_GET['state'] ) && false === strpos( $_SERVER['REQUEST_URI'], 'settings-updated=true' ) ){ ?>
		//Clicked "Connect to Facebook":
		facebook_ajax_convert_token();

		<?php } else { ?>
		if ( window.history && window.history.pushState ) {
			window.history.pushState( {"state": true}, "", "options-general.php?page=facebook-reviews-pro" );
		}
		<?php } ?>

		//Disconnect Facebook
		$( document ).on( 'click', '#fb_disconnect', function ( e ) {
			e.preventDefault();
			if ( confirm( '<?php esc_attr_e( 'Are you sure you want to disconnect from Facebook?', 'facebook-reviews-pro' ); ?>' ) ) {
				$( '#fb_widget_page_tokens' ).val( '' );
				$( '#facebook-settings' ).submit();
			}
		} );

		/**
		 * Facebook AJAX Concert Token
		 */
		function facebook_ajax_convert_token() {

			//Animations
			$( '.facebook-connect-wrap' ).hide();

			//AJAX
			var data = {
				action: 'fb_convert_token',
				sig   : '<?php echo isset( $_GET['tokenid'] ) ? $_GET['tokenid'] : ''; ?>'
			};

			$.post( ajaxurl, data, function ( result ) {

				var res;
				if ( result.success ) {
					res = result.data;
					if ( !res.error ) {

						$( '#fb_widget_page_tokens' ).val( JSON.stringify( res ) );
						$( '#facebook-settings' ).submit();

					} else {
						wrapper.html( '<div class="error settings-error"><p><strong>' + result.data.error + '</strong></p></div>' );
					}
				} else {
					wrapper.html( '<div class="error settings-error"><p><strong>' + result.data.message + '</strong></p></div>' );
				}
			} );
		}

	} );
</script>