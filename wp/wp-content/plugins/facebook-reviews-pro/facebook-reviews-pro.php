<?php
/**
 * Plugin Name: Facebook Reviews Pro
 * Plugin URI: https://wordimpress.com/plugins/facebook-reviews-pro/
 * Description: Easily display Facebook page reviews and ratings with a simple and intuitive WordPress widget and shortcode.
 * Version: 1.1.1
 * Author: WordImpress
 * Author URI: http://wordimpress.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: facebook-widget-pro
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WordImpress/Facebook-Reviews-Pro
 *
 * This is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Facebook Reviews Pro. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! class_exists( 'WP_Facebook_Reviews_Pro' ) ) {

	final class WP_Facebook_Reviews_Pro {
		/** Singleton *************************************************************/

		/**
		 * @var WP_Facebook_Reviews_Pro The one true Give
		 *
		 * @since 1.0
		 */
		protected static $instance = null;

		/**
		 * Class Constructor
		 */
		public function __construct() {

			$this->setup_constants();

		}

		/**
		 * Main WP_Facebook_Reviews_Pro Instance
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Facebook_Reviews_Pro ) ) {
				self::$instance = new self();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				self::$instance->includes();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function setup_constants() {

			$home_url    = home_url();
			$plugins_url = plugins_url();

			if ( stripos( $home_url, 'https://' ) === false ) {
				$plugins_url = str_ireplace( 'https://', 'http://', $plugins_url );
			}

			//Define Globals
			define( 'FB_WIDGET_PRO_SLUG', 'facebook-reviews-pro' );
			define( 'FB_WIDGET_PRO_BASENAME', plugin_basename( __FILE__ ) );

			if ( ! defined( 'FB_WIDGET_PRO_PATH' ) ) {
				define( 'FB_WIDGET_PRO_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			}
			if ( ! defined( 'FB_WIDGET_PRO_URL' ) ) {
				define( 'FB_WIDGET_PRO_URL', $plugins_url . '/' . basename( plugin_dir_path( __FILE__ ) ) );
			}
			if ( ! defined( 'FB_WIDGET_PRO_SETTINGS_URL' ) ) {
				define( 'FB_WIDGET_PRO_SETTINGS_URL', admin_url( 'options-general.php?page=facebook_widget' ) );
			}
			if ( ! defined( 'FB_WIDGET_PRO_GET_TOKEN_URL' ) ) {
				define( 'FB_WIDGET_PRO_GET_TOKEN_URL', 'https://wordimpress.com/wi-api/get_token' );
			}
			if ( ! defined( 'FB_WIDGET_CONVERT_TOKEN_URL' ) ) {
				define( 'FB_WIDGET_CONVERT_TOKEN_URL', 'https://wordimpress.com/wi-api/convert_token' );
			}

		}

		/**
		 * Includes
		 */
		private function includes() {
			/**
			 * Get the Widget and Shortcode
			 */
			require_once( dirname( __FILE__ ) . '/includes/widget.php' );
			require_once( dirname( __FILE__ ) . '/includes/shortcode.php' );

			if ( is_admin() ) {
				require_once( dirname( __FILE__ ) . '/includes/notice.php' );
				require_once( dirname( __FILE__ ) . '/includes/options.php' );
				require_once( dirname( __FILE__ ) . '/includes/shortcode-generator.php' );
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since  1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugins's languages directory
			$fb_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$fb_lang_dir = apply_filters( 'give_languages_directory', $fb_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'facebook-reviews-pro' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'facebook-reviews-pro', $locale );

			// Setup paths to current locale file
			$mofile_local  = $fb_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/facebook-reviews-pro/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/give folder
				load_textdomain( 'facebook-reviews-pro', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local location from filter `give_languages_directory`
				load_textdomain( 'facebook-reviews-pro', $mofile_local );
			} else {
				// Load the default language files packaged up w/ Give
				load_plugin_textdomain( 'facebook-reviews-pro', false, $fb_lang_dir );
			}
		}


	}
}


/**
 * Returns the main instance of Google Places Reviews.
 *
 * @since  1.0
 * @return WP_Facebook_Reviews_Pro()
 */
function WP_Facebook_Reviews_Pro() {
	return WP_Facebook_Reviews_Pro::instance();
}

WP_Facebook_Reviews_Pro();