<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package vantage
 * @since vantage 1.0
 * @license GPL 2.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-main">

		<?php do_action('vantage_entry_main_top') ?>

		<header class="entry-header">

            <?php
            //Breadcrumb
            if ( function_exists( 'breadcrumb_trail' ) ) {
                $args = array(
                    'show_browse'     => false,
                    'before'          => '<i class="fa fa-chevron-circle-right" style="padding-right:.5em;" aria-hidden="true"></i>'
                );
                breadcrumb_trail($args);
            }

            ?>
			<!--EDITBYSCOTT<h1 class="entry-title"><?php the_title(); ?></h1>-->
			<?php if ( siteorigin_setting( 'blog_post_metadata' ) ) : ?>
			<div class="entry-meta">
				<?php vantage_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'vantage' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->

		<?php do_action('vantage_entry_main_bottom') ?>

	</div>

</article><!-- #post-<?php the_ID(); ?> -->