<?php disallow_direct_load( 'single-degree.php' ); ?>

<?php get_header(); the_post(); ?>

<?php
$parent_degree_id = $post->post_parent;
$parent_degree = null;
if ( $parent_degree_id ) {
	$parent_degree = get_post( $parent_degree_id );
}

$apply_url = get_post_meta( $post->ID, 'degree_apply_url', true );
?>

<article id="article-<?php echo $post->post_name; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-9 col-md-push-3 article-content-wrap">
				<div class="row">
					<div class="col-md-9 col-sm-9">
						<h1 class="page-title"><?php the_title(); ?>
							<?php if ( $parent_degree ): ?>
								<small><?php echo $parent_degree->post_title; ?></small>
							<?php endif; ?>
						</h1>
					</div>
					<?php if ( $apply_url ): ?>
					<div class="col-md-3 col-sm-3">
						<a class="btn btn-success btn-lg apply-btn ga-event-link" data-ga-category="Apply Online Links" data-ga-label="<?php the_title(); if ( $parent_degree ) { echo ' - '.$parent_degree->post_title; } ?>" href="<?php echo $apply_url; ?>">
							Apply Online
						</a>
					</div>
					<?php endif; ?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php the_content(); ?>
						<?php
							$terms = wp_get_post_terms( $post->ID, 'degree_types' );
							if ( count( $terms ) == 1) {
								$term_name = $terms[0]->slug;
								echo do_shortcode( '[degree-callout degree_type="'. $term_name . '"]');
							} else {
								echo do_shortcode( '[degree-callout]' );
							}
						?>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-md-pull-9 article-sidebar-wrap">
				<aside>
					<?php echo display_page_side_menu( $post->ID, true ); ?>
				</aside>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
