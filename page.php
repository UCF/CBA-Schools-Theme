<?php disallow_direct_load( 'page.php' ); ?>

<?php get_header(); the_post(); ?>
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<?php echo display_page_side_menu( $post->ID, TRUE ); ?>
			<?php if ( is_active_sidebar( 'sidebar' ) ) { dynamic_sidebar( 'sidebar' ); } ?>
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
