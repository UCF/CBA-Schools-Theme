<?php disallow_direct_load( 'page.php' ); ?>

<?php get_header(); the_post(); ?>
<div class="container">
	<div class="row">
		<div class="col-md-4">
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
		<div class="col-md-8">
			<h1><?php the_title(); ?></h1>
			<article id="article-<?php echo $post->post_name; ?>">
				<?php the_content(); ?>
			</article>
		</div>
	</div>
</div>

<?php get_footer();?>
