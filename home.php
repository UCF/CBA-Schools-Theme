<?php disallow_direct_load( 'home.php' ); ?>

<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-push-4">
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
			<section class="home-page-banner">
				<?php
					$image = get_theme_mod_or_default( 'home_page_banner' );
					$link = get_theme_mod_or_default( 'home_page_banner_link' );
					if ( $image ) :
				?>
				<?php if ( $link ) { echo '<a href="' . $link . '">'; } ?>
				<img src="<?php echo $image; ?>" alt="home page banner" class="img-responsive">
				<?php if ( $link ) { echo '</a>'; } ?>
				<?php endif; ?>
			</section>
			<section class="feature-photos">
				<?php echo display_home_centerpieces(); ?>
			</section>
			<div class="row">
				<div class="col-sm-8">
					<?php echo get_embed_html( get_theme_mod_or_default( 'home_page_video_url' ) ); ?>
					<?php
						$video_caption = get_theme_mod_or_default( 'home_page_video_caption' );
						if ( $video_caption ) :
					?>
						<p class="video-caption"><?php echo wptexturize( $video_caption ); ?></p>
					<?php
						endif;
					?>
				</div>
				<div class="col-sm-4">
					<?php
						echo do_shortcode( '[publication id="' . get_theme_mod_or_default( 'home_page_publication' ) . '"]' );
					?>
				</div>
			</div>
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
			<section id="facebook">
				<h3>DeVos on FACEBOOK</h3>
				<?php echo do_shortcode( '[facebook_posts]' ); ?>
				<a href="<?php echo get_theme_mod_or_default( 'facebook_url' ) ?>" class="all-posts">View More</a>
			</section>
			<section class="feature-news">
				<?php
					$twitter_feed = get_theme_mod_or_default( 'twitter_timeline_widget' );
					if ( $twitter_feed ) :
				?>
					<?php echo wptexturize( $twitter_feed ); ?>
				<?php
					endif;
				?>
			</section>
			<section>
				<?php
					echo do_shortcode( '[spotlight id="' . get_theme_mod_or_default( 'home_page_spotlight' ) . '"]' );
				?>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
