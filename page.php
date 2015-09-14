<?php disallow_direct_load( 'page.php' ); ?>

<?php get_header(); the_post(); ?>

<?php $breadcrumb_parent = get_page_breadcrumb_parent( $post->ID ); ?>

<article id="article-<?php echo $post->post_name; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-9 col-md-push-3 article-content-wrap <?php echo $breadcrumb_parent ? 'subpage' : '' ?>">
				<?php if ( $breadcrumb_parent ) : ?>
					<div class="page-breadcrumb"><a href="<?php echo $breadcrumb_parent->url; ?>">< <?php echo $breadcrumb_parent->title; ?></a></div>
				<?php endif; ?>

				<?php if ( !is_front_page() ): ?>
					<h1 class="page-title page-title-child"><?php the_title(); ?></h1>
				<?php endif; ?>
				<?php the_content(); ?>
			</div>
			<div class="col-md-3 col-md-pull-9 article-sidebar-wrap">
				<aside>
					<?php
						echo display_page_side_menu( $post->ID );
						echo display_custom_side_menu( $post->ID );
					?>
				</aside>
			</div>
		</div>
	</div>
</article>

<?php get_footer();?>
