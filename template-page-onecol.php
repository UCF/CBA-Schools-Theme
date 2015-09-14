<?php
/*
Template Name: One column page
*/
?>
<?php disallow_direct_load( 'template-page-onecol.php' ); ?>

<?php get_header(); the_post(); ?>

<article id="article-<?php echo $post->post_name; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-12 article-content-wrap">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer();?>
