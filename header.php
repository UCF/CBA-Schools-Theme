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
		<?php get_parent_site_header(); ?>
		<main>
			<div class="school-header container">
				<div class="row">
					<div class="col-sm-4">
						<?php if ( is_home() ) : ?>
							<h1><?php echo get_bloginfo( 'name' ); ?></h1>
						<?php else: ?>
							<span class="h1"><?php echo get_bloginfo( 'name' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="school-contact-info col-sm-8">
							Office: <?php echo get_theme_mod_or_default( 'office' ); ?><br>
							Hours: <?php echo get_theme_mod_or_default( 'office_hours' ); ?><br>
							Phone: <?php echo display_phone( 'contact_phone' ); ?><br>
							Email: <?php echo display_email( 'contact_email' ); ?></br>
							<?php if ( !function_exists( 'dynamic_sidebar' ) or !dynamic_sidebar( 'Footer - Column Four' ) ) : ?>
								<?php echo display_site_social(false, 'hidden-xs'); ?>
							<?php endif; ?>
					</div>
				</div>
			</div>
