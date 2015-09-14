<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?php echo "\n".header_()."\n"; ?>
		<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<?php if ( GA_ACCOUNT or CB_UID ): ?>
		<script>
			<?php if ( GA_ACCOUNT ): ?>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo GA_ACCOUNT; ?>', 'auto');
			ga('send', 'pageview');
			<?php endif; ?>

			<?php if ( CB_UID ): ?>
			var CB_UID      = '<?php echo CB_UID; ?>';
			var CB_DOMAIN   = '<?php echo CB_DOMAIN; ?>';
			<?php endif; ?>
		</script>
		<?php endif;?>

		<?php
		$post_type = get_post_type( $post->ID );
		if ( ( $stylesheet_id = get_post_meta( $post->ID, $post_type.'_stylesheet', True ) ) !== False
			&& ( $stylesheet_url = wp_get_attachment_url( $stylesheet_id ) ) !== False):
		?>
		<link rel="stylesheet" href="<?php echo $stylesheet_url; ?>">
		<?php endif; ?>

		<script>
			var PostTypeSearchDataManager = {
				'searches' : [],
				'register' : function(search) {
					this.searches.push(search);
				}
			};
			var PostTypeSearchData = function(column_count, column_width, data) {
				this.column_count = column_count;
				this.column_width = column_width;
				this.data         = data;
			};
		</script>

	</head>
	<body ontouchstart class="<?php echo body_classes(); ?>" <?php if ( is_home() ): ?>id="body-home"<?php endif; ?>>
		<div id="primary-nav-menu-pulldown"></div>
		<header class="primary-header">
			<nav class="primary-header-nav">
				<div class="nav-name-col">
					<?php if ( is_home() ): ?>
						<h1 class="site-title">
					<?php else: ?>
						<span class="h1 site-title">
					<?php endif; ?>
							<a href="<?php echo get_home_url(); ?>">
								<?php if ( $tagline = get_theme_option( 'site_tagline' ) ): ?>
									<?php echo $tagline; ?>
									<span class="alt"><?php echo get_bloginfo('name'); ?></span>
								<?php else: ?>
									<?php echo get_bloginfo('name'); ?>
								<?php endif; ?>
							</a>
					<?php if ( is_home() ): ?>
						</h1>
					<?php else: ?>
						</span>
					<?php endif; ?>
					<?php echo display_site_social(true); ?>
				</div>
				<div class="nav-menu-col">
					<a id="header-menu-mobile-toggle" href="#">
						<span class="fa fa-navicon"></span>
						Menu
					</a>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'header-menu',
							'container'      => 'false',
							'menu_class'     => 'menu '.get_header_styles(),
							'menu_id'        => 'header-menu',
							'depth'          => 3,
							'link_before'    => '<span>',
							'link_after'     => '</span>'
						)
					);
					?>
				</div>
			</nav>
		</header>
		<main>
