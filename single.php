<?php disallow_direct_load( 'single.php' ); ?>

<?php get_header(); the_post(); ?>

<article id="article-<?php echo $post->post_name; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-12 article-content-wrap">
				<?php if ( !is_front_page() ): ?>
					<h1><?php the_title(); ?></h1>
				<?php endif; ?>
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer();?>
