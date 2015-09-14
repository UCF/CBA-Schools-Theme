<?php
	# Check for settings updated or updated, varies between wp versions
	$updated  = (bool)( $_GET['settings-updated'] or $_GET['updated'] );
	$settings = array_filter( Config::$theme_settings, 'is_array' );
	$misc     = array_filter( Config::$theme_settings, 'is_object' );
	if ( count( $misc ) ) { $settings['Miscellaneous'] = $misc; }

	$sections = array_keys( $settings );
?>

<form method="post" action="options.php" id="theme-options" class="i-am-a-fancy-admin">
    <?php settings_fields( THEME_OPTIONS_GROUP ); ?>
	<div class="container">
		<h1><?php print __( THEME_OPTIONS_PAGE_TITLE ); ?></h1>

		<?php if ( $updated ) : ?>
		<div class="updated fade"><p><strong><?php print __( 'Options saved' ); ?></strong></p></div>
		<?php endif; ?>

		<div class="sections">
			<ul>
				<?php foreach( $sections as $key=>$section ) : ?>
				<li class="section"><a href="#<?php print slug( $section ); ?>"><?php print $section; ?></a></li>
				<?php endforeach;?>
			</ul>
		</div>
		<div class="fields">
			<ul>
				<?php foreach ( $settings as $section=>$fields ) : ?>
				<li class="section" id="<?=slug($section)?>">
					<h2><?php print $section; ?></h2>
					<table class="form-table">
						<?php foreach ( $fields as $field ) : ?>
						<tr valign="top">
							<th scope="row"><?php print $field->label_html(); ?></th>
							<td class="field">
								<?php print $field->input_html(); ?>
								<?php print $field->description_html(); ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</li>
				<?php endforeach; ?>
			</ul>
			<div class="submit">
				<input type="submit" class="button-primary" value="<?php print __('Save Options'); ?>" />
			</div>
		</div>
	</div>
</form>
