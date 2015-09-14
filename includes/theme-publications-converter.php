<form method="post" id="theme-publications-converter" class="i-am-a-fancy-admin">
	<?php settings_fields(THEME_OPTIONS_GROUP); ?>
		<div class="container">
			<h2>Publications Converter</h2>
			<?php if ($_POST['submit_button']) {
				require_once(THEME_JOBS_DIR.'/publications-converter.php');
				print '<br /><br /><hr /><br />';
			}
			?>
			<p>
				Click the button below to convert the News and Research Publications for each Person into Publication objects.
			</p>
			<div class="submit">
				<input type="submit" class="btn-primary" name="submit_button" value="<?php echo __('Run Converter'); ?>" />
			</div>
		</div>
</form>