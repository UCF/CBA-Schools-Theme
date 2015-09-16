<?php disallow_direct_load( 'home.php' ); ?>

<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div class="col-md-4">

		</div>
		<div class="col-md-8">
			<section class="feature-photos">
				<?php echo do_shortcode( '[centerpiece-carousel]' ); ?>
			</section>

			<section class="feature-blocks">
				<div class="container">
					<?php echo do_shortcode( '[spotlight-list orderby="date" order="DESC" posts_per_page="6"]' ); ?>
				</div>
			</section>

			<section class="feature-news">
				<div class="container">
					<h2 class="feature-news-heading">Insight <span class="alt">News and Posts</span></h2>
					<?php echo display_news(null, true); ?>
				</div>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
