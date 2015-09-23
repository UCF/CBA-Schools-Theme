<?php disallow_direct_load( 'page.php' ); ?>

<?php get_header(); the_post(); append_person_metadata( $post ); ?>
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
				<div class="row">
					<div class="col-sm-3">
						<?php echo $post->thumbnail; ?>
					</div>
					<div class="col-sm-9">
						<dl class="person dl-horizontal">
						<?php if ( $post->hometown ) : ?>
							<dt>Hometown:</dt>
							<dd><?php echo $post->hometown; ?></dd>
						<?php endif; ?>
						<?php if ( $post->undergrad_institution ) : ?>
							<dt>Undergraduate Institution</dt>
							<dd><?php echo $post->undergrad_institution; ?></dd>
						<?php endif; ?>
						<?php if ( $post->undergrad_degree ) : ?>
							<dt>Undergraduate Degree</dt>
							<dd><?php echo $post->undergrad_degree; ?></dd>
						<?php endif; ?>
						<?php if ( $post->postgrad_degree ) : ?>
							<dt>Postgraduate Degree</dt>
							<dd><?php echo $post->postgrad_degree; ?></dd>
						<?php endif; ?>
						<?php if ( $post->internships ) : ?>
							<dt>Assistantships and Internships</dt>
							<dd><?php echo $post->internships; ?></dd>
						<?php endif; ?>
						<?php if ( $post->outreach ) : ?>
							<dt>Community Outreach</dt>
							<dd><?php echo $post->outreach; ?></dd>
						<?php endif; ?>
						<?php if ( $post->career ) : ?>
							<dt>Career Aspirations</dt>
							<dd><?php echo $post->career; ?></dd>
						<?php endif; ?>
						</dl>
					</div>
				</div>
				<?php the_content(); ?>
			</article>
		</div>
		<div class="col-sm-4 col-sm-pull-8">
			<div class="hidden-xs">
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
