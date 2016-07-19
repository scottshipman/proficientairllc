<?php
/**
 * Configure theme settings.
 *
 * @package vantage
 * @since vantage 1.0
 * @license GPL 2.0
 */

/**
 * Setup theme settings.
 * 
 * @since vantage 1.0
 */
function proficientairllc_theme_settings(){


	/**
	 * General Settings
	 */



	// EDITBYSCOTT	
siteorigin_settings_add_field( 'general', 'site_phone_number', 'text', __( 'Site Phone Number', 'vantage' ), array(
		'description' => __( "The Phone Number to display in the masthead area.", 'vantage' )
	) );



}
add_action('siteorigin_settings_init', 'proficientairllc_theme_settings');

