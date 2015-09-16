		</main>
		<footer class="primary-footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-2 col-line-right">
						<h3>Program Overview</h3>
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
						<h3>Academic &amp; Admission Information</h3>
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
					<div class="col-sm-2">
						<h3>Students</h3>
						<?php
						wp_nav_menu(
							array(
								'theme_location'  => 'footer-menu-3',
								'container'       => 'nav',
								'container_class' => 'primary-subfooter-nav',
								'container_id'    => 'primary-subfooter-nav-3',
								'menu_class'      => 'menu',
								'menu_id'         => 'footer-menu-3',
								'fallback_cb'     => false,
								'depth'           => 1,
							)
						);
						?>
						<h3>Alumni</h3>
						<?php
						wp_nav_menu(
							array(
								'theme_location'  => 'footer-menu-4',
								'container'       => 'nav',
								'container_class' => 'primary-subfooter-nav',
								'container_id'    => 'primary-subfooter-nav-4',
								'menu_class'      => 'menu',
								'menu_id'         => 'footer-menu-4',
								'fallback_cb'     => false,
								'depth'           => 1,
							)
						);
						?>
						<h3>Contact</h3>
					</div>
					<div class="col-sm-2 col-line-left">
						<h3>Contact</h3>
						<div class="organization-name"><?php echo get_theme_option( 'organization_name' ); ?></div>
						<?php echo display_contact_address(); ?>
						<p>
							Office: <?php echo get_theme_option( 'office' ); ?><br>
							Hours: <?php echo get_theme_option( 'office_hours' ); ?><br>
							Phone: <?php echo display_phone( 'site_phone' ); ?><br>
							Fax: <?php echo display_phone( 'site_fax' ); ?><br>
							Email: <?php echo display_email( 'site_contact' ); ?>
						</p>
					</div>
					<div class="col-sm-6">
						<h3>Alumni Map</h3>
					</div>
				</div>
				<div class="row footer-bottom">
					<div class="col-sm-6">
						<section class="program-name primary-footer-section">
							DeVos Sports Business<br>Management Program
						</section>
					</div>
					<div class="col-sm-6">
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
