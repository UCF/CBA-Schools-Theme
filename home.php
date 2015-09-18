<?php disallow_direct_load( 'home.php' ); ?>

<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-4">
			<?php
				wp_nav_menu(
						array(
							'theme_location' => 'devos-menu',
							'container'      => 'false',
							'menu_class'     => 'menu '.get_header_styles().' nav-stacked',
							'menu_id'        => 'side-menu',
							'depth'          => 3,
							'link_before'    => '<span>',
							'link_after'     => '</span>'
						)
					);
			?>
		</div>
		<div class="col-sm-8">
			<section class="feature-photos">
				<?php echo do_shortcode( '[centerpiece-carousel]' ); ?>
				<?php echo do_shortcode( '[spotlight slug="my-spotlight"]' ); ?>
			</section>

			<section class="feature-blocks">
				<div class="container">
					<?php echo do_shortcode( '[spotlight-list orderby="date" order="DESC" posts_per_page="6"]' ); ?>
				</div>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
