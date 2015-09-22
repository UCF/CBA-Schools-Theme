<?php disallow_direct_load( 'page.php' ); ?>

<?php get_header(); the_post(); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-push-4">
			<h1><?php the_title(); ?></h1>
			<div class="visible-xs-block">
				<?php
				wp_nav_menu(
						array(
							'theme_location' => 'devos-menu',
							'container'      => 'false',
							'menu_class'     => 'menu '.get_header_styles().' nav-stacked side-menu',
							'menu_id'        => 'side-menu',
							'depth'          => 3,
							'link_before'    => '<span>',
							'link_after'     => '</span>'
						)
					);
				?>
			</div>
			<article id="article-<?php echo $post->post_name; ?>">
				<?php the_content(); ?>
			</article>
		</div>
		<div class="col-sm-4 col-sm-pull-8">
			<?php
				wp_nav_menu(
						array(
							'theme_location' => 'devos-menu',
							'container'      => 'false',
							'menu_class'     => 'menu '.get_header_styles().' nav-stacked side-menu',
							'menu_id'        => 'side-menu',
							'depth'          => 3,
							'link_before'    => '<span>',
							'link_after'     => '</span>'
						)
					);
			?>
			<?php
				$cta = get_post_meta( $post->ID, 'page_cta_markup', true );

				if ( $cta ) : ?>
				<div class="call-to-action">
				<?php echo apply_filters( 'the_content', $cta ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer();?>
