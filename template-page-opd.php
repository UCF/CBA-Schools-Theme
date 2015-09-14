<?php
/*
Template Name: Office of Professional Development page
*/
?>
<?php disallow_direct_load( 'template-page-opd.php' ); ?>

<?php get_header(); the_post(); ?>

<article id="article-opd">
	<div class="container">
		<div class="row">
			<div class="col-md-12 article-content-wrap">
				<h1 class="page-title">
					Get Hired.
					<span class="alt">
						The Office of <strong>Professional Development</strong>
					</span>
				</h1>
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer();?>
