		</main>
		<footer class="primary-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-3 col-sm-3">
						<?php
						wp_nav_menu(
							array(
								'theme_location'  => 'footer-menu-1',
								'container'       => 'nav',
								'container_class' => 'primary-subfooter-nav',
								'container_id'    => 'primary-subfooter-nav-1',
								'menu_class'      => 'menu',
								'menu_id'         => 'footer-menu-1',
								'fallback_cb'     => false,
								'depth'           => 1,
							)
						);
						?>
					</div>
					<div class="col-md-3 col-sm-3">
						<?php
						wp_nav_menu(
							array(
								'theme_location'  => 'footer-menu-2',
								'container'       => 'nav',
								'container_class' => 'primary-subfooter-nav',
								'container_id'    => 'primary-subfooter-nav-2',
								'menu_class'      => 'menu',
								'menu_id'         => 'footer-menu-2',
								'fallback_cb'     => false,
								'depth'           => 1,
							)
						);
						?>
					</div>
					<div class="col-md-3 col-sm-3">
						<section class="primary-subfooter-section primary-subfooter-search">
							<?php get_search_form(); ?>
						</section>
					</div>
					<div class="col-md-3 col-sm-3">
						<section class="primary-subfooter-section clearfix">
							<span class="external-link-logo first">
								<?php echo display_coba_pass(); ?>
							</span>
							<span class="external-link-logo">
								<?php echo display_aascb_logo(); ?>
							</span>
						</section>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-sm-6">
						<section class="primary-footer-section" id="primary-footer-section-1">
							<?php if ( !function_exists( 'dynamic_sidebar' ) or !dynamic_sidebar( 'Footer - Column One' ) ) : ?>
								<?php echo display_site_social(false, 'visible-xs'); ?>
								<a class="ignore-external footer-logo" href="http://www.ucf.edu">UCF</a>
								<?php echo display_contact_address(); ?>
							<?php endif;?>
						</section>
					</div>
					<div class="col-md-3 col-sm-2 col-xs-6">
						<section class="primary-footer-section" id="primary-footer-section-2">
							<?php if ( !function_exists( 'dynamic_sidebar' ) or !dynamic_sidebar( 'Footer - Column Two' ) ) : ?>
							<?php endif; ?>
						</section>
					</div>
					<div class="col-md-2 col-sm-2 col-xs-6">
						<section class="primary-footer-section" id="primary-footer-section-3">
							<?php if ( !function_exists( 'dynamic_sidebar' ) or !dynamic_sidebar( 'Footer - Column Three' ) ) : ?>
							<?php endif; ?>
						</section>
					</div>
					<div class="col-md-3 col-sm-2">
						<section class="primary-footer-section" id="primary-footer-section-4">
							<?php if ( !function_exists( 'dynamic_sidebar' ) or !dynamic_sidebar( 'Footer - Column Four' ) ) : ?>
								<?php echo display_site_social(false, 'hidden-xs'); ?>
							<?php endif; ?>
						</section>
					</div>
				</div>
			</div>
		</footer>
	</body>

<!-- js markup -->
<div class="hidden">
	<span class="fa fa-search" id="nav-search-icon"></span>
	<span id="primary-nav-expand-icon" class="primary-nav-expand-icon fa fa-chevron-down"></span>
	<ul class="sub-menu-col-1"></ul>
	<ul class="sub-menu-col-2"></ul>
	<ul class="sub-menu-col-3"></ul>
</div>
<!-- End js markup -->

<script type="text/javascript">
// Twitter timeline script
window.twttr = (function (d, s, id) {
  var t, js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id; js.src= "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
  return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
}(document, "script", "twitter-wjs"));
</script>

<?php print "\n".footer_()."\n"; ?>

</html>
