<?php disallow_direct_load( 'home.php' ); ?>

<?php get_header(); ?>

<section class="feature-photos">
	<?php echo display_homepagefeatures(); ?>
	<?php echo display_home_alt_cta(); ?>
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

<?php get_footer(); ?>
