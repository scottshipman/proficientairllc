<?php
/**
 * Part Name: Default Masthead
 */
?>
<header id="masthead" class="site-header" role="banner">
    <?php
    $theme_settings = get_option('theme_mods_proficientairllc');
    $tagline_option = get_option('vantage_theme_settings');
    $license = esc_attr($theme_settings['theme_settings_general_site_contractor_number']);
    ?>
	<div class="hgroup full-container">
		<div id="logo-container">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" class="logo"><?php vantage_display_logo(); ?></a>
            <div id="license-number"><em>lic no: </em><?php echo $license; ?></div>
        </div>
            <div id="header-tagline"  <?php if( siteorigin_setting('logo_no_widget_overlay') ) echo 'class="no-logo-overlay"' ?>>
                <?php
                $tagline = esc_attr($tagline_option['general_site_tagline']);
                ?>
                <div class="tagline">
                        <?php echo $tagline; ?>
                </div>
            </div>

		<?php if( is_active_sidebar('sidebar-header') ) : ?>

			<div id="header-sidebar" <?php if( siteorigin_setting('logo_no_widget_overlay') ) echo 'class="no-logo-overlay"' ?>>
				<?php
					$phone_number = esc_attr($theme_settings['theme_settings_general_site_phone_number']);
				?>
				<div class="phone-number">
					<a href="tel:<?php echo preg_replace('/[^0-9]/','',$phone_number); ?>">
						<i class="fa fa-phone" aria-hidden="true"></i>
						<?php echo $phone_number; ?>
					</a>
				</div>
				<?php
				// Display the header area sidebar, and tell mobile navigation that we can use menus in here
				add_filter('siteorigin_mobilenav_is_valid', '__return_true');
				dynamic_sidebar( 'sidebar-header' );
				remove_filter('siteorigin_mobilenav_is_valid', '__return_true');
				?>
			</div>

		<?php else : ?>

			<div class="support-text">
				<?php do_action('vantage_support_text'); ?>
			</div>

		<?php endif; ?>

	</div><!-- .hgroup.full-container -->

	<?php get_template_part( 'parts/menu', apply_filters( 'vantage_menu_type', siteorigin_setting( 'layout_menu' ) ) ); ?>

</header><!-- #masthead .site-header -->