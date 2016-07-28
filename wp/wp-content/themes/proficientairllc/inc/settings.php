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

    $settings = SiteOrigin_Settings::single();
	/**
	 * General Settings
	 */

    $settings->add_field( 'general', 'site_phone_number', 'text', __( 'Site Phone Number', 'proficientairllc' ), array(
        'description' => __( "The Phone Number to display in the masthead area.", 'proficientairllc' )
    ) );

    $settings->add_field( 'general', 'site_tagline', 'text', __( 'Company Tagline', 'proficientairllc' ), array(
        'description' => __( "The Tagline to display in the masthead area.", 'proficientairllc' )
    ) );

    $settings->add_field( 'general', 'site_contractor_number', 'text', __( 'Company License Number', 'proficientairllc' ), array(
        'description' => __( "The License Number to display in the masthead area.", 'proficientairllc' )
    ) );

	// EDITBYSCOTT	
//siteorigin_settings_add_field( 'general', 'site_phone_number', 'text', __( 'Site Phone Number', 'proficientairllc' ), array(
//		'description' => __( "The Phone Number to display in the masthead area.", 'proficientairllc' )
//	) );
//
//siteorigin_settings_add_field( 'general', 'site_tagline', 'text', __( 'Company Tagline', 'proficientairllc' ), array(
//    'description' => __( "The Tagline to display in the masthead area.", 'proficientairllc' )
//) );

}
add_action('siteorigin_settings_init', 'proficientairllc_theme_settings');

