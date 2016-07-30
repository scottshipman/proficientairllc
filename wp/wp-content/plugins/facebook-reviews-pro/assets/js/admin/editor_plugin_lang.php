<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
	fbw:{
		shortcode_generator_title: "' . esc_js( __( 'Add Facebook Reviews', 'gpr' ) ) . '",
		shortcode_tag: "' . esc_js( apply_filters( 'fbw_shortcode_tag', 'facebook_widget_pro' ) ) . '"
	}
}})';