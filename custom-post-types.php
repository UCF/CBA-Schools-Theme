<?php

/**
 * Abstract class for defining custom post types.
 *
 **/
abstract class CustomPostType {
	public
		$name           = 'custom_post_type',
		$plural_name    = 'Custom Posts',
		$singular_name  = 'Custom Post',
		$add_new_item   = 'Add New Custom Post',
		$edit_item      = 'Edit Custom Post',
		$new_item       = 'New Custom Post',
		$public         = True,  # I dunno...leave it true
		$use_title      = True,  # Title field
		$use_editor     = True,  # WYSIWYG editor, post content field
		$use_excerpt    = False, # Excerpt editor
		$use_revisions  = True,  # Revisions on post content and titles
		$use_thumbnails = False, # Featured images
		$use_order      = False, # Wordpress built-in order meta data
		$use_metabox    = False, # Enable if you have custom fields to display in admin
		$use_shortcode  = False, # Auto generate a shortcode for the post type
								 # (see also objectsToHTML and toHTML methods)
		$taxonomies     = array('post_tag'),
		$built_in       = False,

		# Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,

		# Whether or not the post type uses cloneable fields and requires special saving
		# functionality and nonce handling.
		$cloneable_fields = False,
		$cloneable_field_nonces = array();


	/**
	 * Wrapper for get_posts function, that predefines post_type for this
	 * custom post type.  Any options valid in get_posts can be passed as an
	 * option array.  Returns an array of objects.
	 **/
	public function get_objects($options=array()){

		$defaults = array(
			'numberposts'   => -1,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'post_type'     => $this->options('name'),
		);
		$options = array_merge($defaults, $options);
		$objects = get_posts($options);
		return $objects;
	}


	/**
	 * Similar to get_objects, but returns array of key values mapping post
	 * title to id if available, otherwise it defaults to id=>id.
	 **/
	public function get_objects_as_options($options=array()){
		$objects = $this->get_objects($options);
		$opt     = array();
		foreach($objects as $o){
			switch(True){
				case $this->options('use_title'):
					$opt[$o->post_title] = $o->ID;
					break;
				default:
					$opt[$o->ID] = $o->ID;
					break;
			}
		}
		return $opt;
	}


	/**
	 * Return the instances values defined by $key.
	 **/
	public function options($key){
		$vars = get_object_vars($this);
		return $vars[$key];
	}


	/**
	 * Additional fields on a custom post type may be defined by overriding this
	 * method on an descendant object.
	 **/
	public function fields(){
		return array();
	}


	/**
	 * Using instance variables defined, returns an array defining what this
	 * custom post type supports.
	 **/
	public function supports(){
		#Default support array
		$supports = array();
		if ($this->options('use_title')){
			$supports[] = 'title';
		}
		if ($this->options('use_order')){
			$supports[] = 'page-attributes';
		}
		if ($this->options('use_thumbnails')){
			$supports[] = 'thumbnail';
		}
		if ($this->options('use_editor')){
			$supports[] = 'editor';
		}
		if ($this->options('use_revisions')){
			$supports[] = 'revisions';
		}
		if ($this->options('use_excerpt')){
			$supports[] = 'excerpt';
		}
		return $supports;
	}


	/**
	 * Creates labels array, defining names for admin panel.
	 **/
	public function labels(){
		return array(
			'name'          => __($this->options('plural_name')),
			'singular_name' => __($this->options('singular_name')),
			'add_new_item'  => __($this->options('add_new_item')),
			'edit_item'     => __($this->options('edit_item')),
			'new_item'      => __($this->options('new_item')),
		);
	}


	/**
	 * Creates metabox array for custom post type. Override method in
	 * descendants to add or modify metaboxes.
	 **/
	public function metabox(){
		if ($this->options('use_metabox')){
			return array(
				'id'       => $this->options('name').'_metabox',
				'title'    => __($this->options('singular_name').' Fields'),
				'page'     => $this->options('name'),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}

		return null;
	}


	/**
	 * Registers metaboxes defined for custom post type.
	 **/
	public function register_metaboxes(){
		if ($this->options('use_metabox')){
			$metabox = $this->metabox();

			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				'show_meta_boxes',
				$metabox['page'],
				$metabox['context'],
				$metabox['priority']
			);
		}
	}


	/**
	 * Registers the custom post type and any other ancillary actions that are
	 * required for the post to function properly.
	 **/
	public function register(){
		$registration = array(
			'labels'     => $this->labels(),
			'supports'   => $this->supports(),
			'public'     => $this->options('public'),
			'taxonomies' => $this->options('taxonomies'),
			'_builtin'   => $this->options('built_in')
		);

		if ($this->options('use_order')){
			$registration = array_merge($registration, array('hierarchical' => True,));
		}

		register_post_type($this->options('name'), $registration);

		if ($this->options('use_shortcode')){
			add_shortcode($this->options('name').'-list', array($this, 'shortcode'));
		}
	}


	/**
	 * Shortcode for this custom post type.  Can be overridden for descendants.
	 * Defaults to just outputting a list of objects outputted as defined by
	 * toHTML method.
	 **/
	public function shortcode($attr){
		$default = array(
			'post_type' => $this->options('name'),
		);
		if (is_array($attr)){
			$attr = array_merge($default, $attr);
		} else {
			$attr = $default;
		}
		return sc_object_list($attr);
	}


	/**
	 * Handles output for a list of objects, can be overridden for descendants.
	 * If you want to override how a list of objects are outputted, override
	 * this, if you just want to override how a single object is outputted, see
	 * the toHTML method.
	 **/
	public function objectsToHTML($objects, $css_classes){
		if (count($objects) < 1){ return '';}

		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;

		ob_start();
		?>
		<ul class="<?php if($css_classes):?><?=$css_classes?><?php else:?><?=$class->options('name')?>-list<?php endif;?>">
			<?php foreach($objects as $o):?>
			<li data-post-id="<?php echo $o->ID; ?>">
				<?=$class->toHTML($o)?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php
		$html = ob_get_clean();
		return $html;
	}


	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 **/
	public function toHTML($object){
		$html = '<a href="' . get_permalink( $object->ID ) . '">' . $object->post_title . '</a>';
		return $html;
	}
}


class Degree extends CustomPostType {
	public
		$name           = 'degree',
		$plural_name    = 'Degrees',
		$singular_name  = 'Degree',
		$add_new_item   = 'Add New Degree',
		$edit_item      = 'Edit Degree',
		$new_item       = 'New Degree',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = True,

		$taxonomies     = array( 'degree_types' );


	public function fields() {
		$prefix = $this->options( 'name' ) . '_';
		return array(
			array(
				'name' => 'Brief description',
				'desc' => 'A short, one-sentence description of this degree.',
				'id' => $prefix.'desc_brief',
				'type' => 'textarea',
			),
			array(
				'name' => 'Apply button link',
				'desc' => 'URL for this degree\'s Apply Online link.  Generally, undergraduate degrees should link to <code>https://apply.ucf.edu/application/</code>, and graduate degrees should link to <code>http://www.graduate.ucf.edu/gradonlineapp/</code>.',
				'id' => $prefix.'apply_url',
				'type' => 'text',
			),
		);
	}

	/**
	 * Only return top-level degrees
	 **/
	public function shortcode($attr) {
		$default = array(
			'post_type' => $this->options( 'name' ),
			'post_parent' => 0
		);
		if ( is_array( $attr ) ){
			$attr = array_merge( $default, $attr );
		} else {
			$attr = $default;
		}
		return sc_object_list( $attr );
	}

	public function objectsToHTML( $degrees, $css_classes, $is_child_list=false ) {
		$parent_heading = 'h2';
		$child_column_count = 2;
		if ( !$css_classes ) {
			$css_classes = 'post-grid-list';
		}
		if ( $is_child_list ) {
			$parent_heading = 'h3';
		}

		ob_start();

		if ( $degrees ) :
		?>
			<ul class="<?php echo $css_classes; ?>">
				<?php
				foreach( $degrees as $degree ):
					if ( $child_degrees = Degree::get_objects( array( 'post_parent' => $degree->ID ) ) ):
						$is_child_list = true;
						$degrees_per_col = round( count( $child_degrees ) / $child_column_count );
						$grouped_child_degrees = array_chunk( $child_degrees, $degrees_per_col, true );
					?>
						<li class="post-grid-item item-has-children" data-post-id="<?php echo $degree->ID; ?>">
							<?php echo Degree::toHTML( $degree, true, $parent_heading ); ?>
							<?php
							$count = 0;
							foreach ( $grouped_child_degrees as $child_degrees_col ) :
								$child_css_classes = 'post-grid-list-children';
								if ( $count == 0 ) {
									$child_css_classes .= ' first';
								}
								echo Degree::objectsToHTML( $child_degrees_col, $child_css_classes, $is_child_list );
								$count++;
							endforeach;
							?>
					<?php else : ?>
						<li class="post-grid-item item-no-children" data-post-id="<?php echo $degree->ID; ?>">
							<?php echo Degree::toHTML( $degree, false, $parent_heading ); ?>
					<?php endif; ?>
					</li>
					<?php
				endforeach;
				?>
			</ul>
		<?php
		endif;

		return ob_get_clean();
	}

	public function toHTML( $object, $has_children=false, $heading ) {
		if ( empty( $heading ) ) {
			$heading = 'h2';
		}
		$desc_brief = get_post_meta( $object->ID, 'degree_desc_brief', true );
		ob_start();
	?>
		<div class="item-details">
			<<?php echo $heading; ?> class="item-title">
				<a href="<?php echo get_permalink($object->ID); ?>">
					<?php echo $object->post_title; ?>
				</a>
			</<?php echo $heading; ?>>
			<?php if ( !empty( $desc_brief ) ): ?>
				<div class="item-desc">
					<?php echo $desc_brief; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( !$has_children ) : ?>
		<div class="item-cta">
			<a class="btn btn-success btn-block" href="<?php echo get_permalink( $object->ID ); ?>">
				Learn More<span class="sr-only"> about the <?php echo $object->post_title; ?> degree</span>
				<span class="fa fa-chevron-right"></span>
			</a>
		</div>
	<?php endif; ?>
	<?php
		return ob_get_clean();
	}
}


class HomePageFeature extends CustomPostType {
	public
		$name           = 'homepagefeature',
		$plural_name    = 'Home Page Features',
		$singular_name  = 'Home Page Feature',
		$add_new_item   = 'Add New Home Page Feature',
		$edit_item      = 'Edit Home Page Feature',
		$new_item       = 'New Home Page Feature',
		$public         = True,
		$use_editor     = False,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = False,

		$taxonomies     = array();

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Link text',
				'desc' => '',
				'id' => $prefix.'link_text',
				'type' => 'text',
			),
			array(
				'name' => 'Link subtext',
				'desc' => '',
				'id' => $prefix.'link_subtext',
				'type' => 'text',
			),
			array(
				'name' => 'Link content',
				'desc' => 'Additional text to display below subtitle. Limited to 240 characters.',
				'id' => $prefix.'link_content',
				'type' => 'textarea',
			),
		);
	}
}


class Page extends CustomPostType {
	public
		$name            = 'page',
		$plural_name     = 'Pages',
		$singular_name   = 'Page',
		$add_new_item    = 'Add New Page',
		$edit_item       = 'Edit Page',
		$new_item        = 'New Page',
		$public          = True,
		$use_editor      = True,
		$use_thumbnails  = False,
		$use_order       = True,
		$use_title       = True,
		$use_metabox     = True,
		$built_in        = True;

	public static function get_menus() {
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$menu_array = array();
		foreach ( $menus as $menu ) {
			$menu_array[$menu->name] = $menu->term_id;
		}
		return $menu_array;
	}

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Brief Description',
				'desc' => 'A brief description of this page, used when this page is listed via <code>[child-page-list]</code>',
				'id' => $prefix.'desc_brief',
				'type' => 'textarea',
			),
			array(
				'name' => 'Stylesheet',
				'desc' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
			),
			array(
				'name' => '<strong>Left Sidebar:</strong> More Information Widget',
				'desc' => '(Optional) Display a More Information widget in the <strong>left-hand sidebar</strong> that contains a given menu. Useful for adding links that are directly related to the page\'s content. Menus can be created in the <a href="'.get_admin_url().'nav-menus.php">menu editor</a>.',
				'id' => $prefix.'widget_l_moreinfo',
				'type' => 'select',
				'options' => $this->get_menus(),
			),
			array(
				'name' => '<strong>Left Sidebar:</strong> More Information Widget Title',
				'desc' => '(Optional) Title for the More Information widget designated above.  Default is "More Information".',
				'id' => $prefix.'widget_l_moreinfo_title',
				'type' => 'text',
			),
		);
	}
}

/**
 * Describes a staff member
 *
 * @author Chris Conover
 **/
class Person extends CustomPostType {
	public
		$name            = 'person',
		$plural_name     = 'People',
		$singular_name   = 'Person',
		$add_new_item    = 'Add Person',
		$edit_item       = 'Edit Person',
		$new_item        = 'New Person',
		$public          = True,
		$use_shortcode   = True,
		$use_metabox     = True,
		$use_thumbnails  = True,
		$use_order       = True,
		$default_orderby = 'person_orderby_name',
		$default_order   = 'DESC',

		$taxonomies     = array('org_groups', 'employee_types', 'departments', 'post_tag'),

		$cloneable_fields = True,
		$cloneable_field_nonces = array(
			'class_schedule_noncename',
			'person_news_noncename',
			'person_research_noncename',
			'person_media_noncename'
		);


	/**
	 * Replacement for fields() method for this post type.
	 **/
	public function metabox_details() {
		return array(
			$this->options('name').'_metabox' => array(
				'title'         => __($this->options('singular_name').' Fields'),
				'callback'      => 'show_meta_boxes',
				'callback_args' => array('metabox_id' => $this->options('name').'_metabox'),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('Title Prefix'),
										'desc'    => '',
										'id'      => $this->options('name').'_title_prefix',
										'type'    => 'text',
									),
									array(
										'name'    => __('Title Suffix'),
										'desc'    => __('Be sure to include leading comma or space if neccessary.'),
										'id'      => $this->options('name').'_title_suffix',
										'type'    => 'text',
									),
									array(
										'name'    => __('Job Title'),
										'desc'    => __(''),
										'id'      => $this->options('name').'_jobtitle',
										'type'    => 'text',
									),
									array(
										'name'    => __('Phone'),
										'desc'    => __('Separate multiple entries with commas.'),
										'id'      => $this->options('name').'_phones',
										'type'    => 'text',
									),
									array(
										'name'    => __('Email'),
										'desc'    => __(''),
										'id'      => $this->options('name').'_email',
										'type'    => 'text',
									),
									array(
										'name'    => __('Office Location'),
										'desc'    => __(''),
										'id'      => $this->options('name').'_room',
										'type'    => 'text',
									),
									array(
										'name'    => __('Office Map URL'),
										'desc'    => __('URL that displays the Office Location on a map; e.g. http://map.ucf.edu/?show=45'),
										'id'      => $this->options('name').'_room_url',
										'type'    => 'text',
									),
									array(
										'name'    => __('CV'),
										'desc'    => __('Upload a CV for this person.'),
										'id'      => $this->options('name').'_cv',
										'type'    => 'file',
									),
									array(
										'name'    => __('Order By Name'),
										'desc'    => __('Name used for sorting. Leaving this field blank may lead to an unexpected sort order.'),
										'id'      => $this->options('name').'_orderby_name',
										'type'    => 'text',
									),
								),
			),
			$this->options('name').'_class_schedule_metabox' => array(
				'title'         => 'Class Schedule',
				'callback'      => array($this, 'class_schedule_meta_callback'),
				'callback_args' => array(),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('Class Schedule'),
										'id'      => $this->options('name').'_class_schedule',
										'type'    => 'text',
									),
							),
			),
			$this->options('name').'_office_hours_metabox' => array(
				'title'         => 'Office Hours',
				'callback'      => 'show_meta_boxes',
				'callback_args' => array('metabox_id' => $this->options('name').'_office_hours_metabox'),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('Monday'),
										'id'      => $this->options('name').'_office_hours_m',
										'type'    => 'text',
									),
									array(
										'name'    => __('Tuesday'),
										'id'      => $this->options('name').'_office_hours_tu',
										'type'    => 'text',
									),
									array(
										'name'    => __('Wednesday'),
										'id'      => $this->options('name').'_office_hours_w',
										'type'    => 'text',
									),
									array(
										'name'    => __('Thursday'),
										'id'      => $this->options('name').'_office_hours_th',
										'type'    => 'text',
									),
									array(
										'name'    => __('Friday'),
										'id'      => $this->options('name').'_office_hours_f',
										'type'    => 'text',
									),
									array(
										'name'    => __('Saturday'),
										'id'      => $this->options('name').'_office_hours_sa',
										'type'    => 'text',
									),
									array(
										'name'    => __('Sunday'),
										'id'      => $this->options('name').'_office_hours_su',
										'type'    => 'text',
									),
							),
			),
			$this->options('name').'_news_metabox' => array(
				'title'         => 'News Articles',
				'callback'      => array($this, 'news_meta_callback'),
				'callback_args' => array(),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('News Articles'),
										'id'      => $this->options('name').'_news',
										'type'    => 'text',
									),
							),
			),
			$this->options('name').'_research_metabox' => array(
				'title'         => 'Research and Publications',
				'callback'      => array($this, 'research_meta_callback'),
				'callback_args' => array(),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('Research and Publications'),
										'id'      => $this->options('name').'_research',
										'type'    => 'text',
									),
							),
			),
			$this->options('name').'_media_metabox' => array(
				'title'         => 'Video and Media',
				'callback'      => array($this, 'media_meta_callback'),
				'callback_args' => array(),
				'page'          => $this->options('name'),
				'context'       => 'normal',
				'priority'      => 'high',
				'fields'        => array(
									array(
										'name'    => __('Video and Media'),
										'id'      => $this->options('name').'_media',
										'type'    => 'text',
									),
							),
			),
		);
	}

	public function get_metabox($metabox_id) {
		$metaboxes = $this->metabox();
		$metabox = null;
		if ($metaboxes[$metabox_id]) {
			$metabox = $metaboxes[$metabox_id];
		}
		return $metabox;
	}

	public function metabox(){
		if ($this->options('use_metabox')) {
			return $this->metabox_details();
		}

		return null;
	}

	public function register_metaboxes(){
		if ($this->options('use_metabox')){
			$metaboxes = $this->metabox();

			foreach ($metaboxes as $id=>$metabox) {
				add_meta_box(
					$id,
					$metabox['title'],
					$metabox['callback'],
					$metabox['page'],
					$metabox['context'],
					$metabox['priority'],
					$metabox['callback_args']
				);
			}
		}
	}


	/**
	 * Content for Class Schedule Meta Box
	 *
	 * @author RJ Bruneel
	 **/

	public function class_schedule_meta_callback( $post ) {

		global $post;

		// Use nonce for verification
		wp_nonce_field( 'class_schedule_noncename', 'class_schedule_noncename' );
		?>

		<div id="meta_inner">
			<?php
				//get the saved meta as an array
				$class_schedule = get_post_meta($post->ID, 'person_class_schedule', true);
				$count = 0;

				ob_start();
			?>
				<tr>
					<td>
						<input type="text" name="person_class_schedule[%1$s][title]" value="%2$s">
					</td>
					<td>
						<input type="text" name="person_class_schedule[%1$s][room]" value="%3$s">
					</td>
					<td>
						<input type="text" name="person_class_schedule[%1$s][semester]" value="%4$s">
					</td>
					<td>
						<span class="remove-class button">Remove</span>
					</td>
				</tr>
			<?php
				$fields = ob_get_clean();
			?>
				<table class="class-schedule-table">
					<tr>
						<th>Class Title</th>
						<th>Room</th>
						<th>Semester</th>
						<th></th>
					</tr>
			<?php

				foreach( $class_schedule as $class ) {
					printf( $fields, $count, $class['title'], $class['room'], $class['semester'] );
					$count = $count + 1;
				}

				// Add empty set of fields at the end
				printf( $fields, $count, '', '', '' );

			?>
			</table>

			<span class="add-class button button-primary" data-ucf-count="<?php echo $count; ?>">Add New Item</span>
		</div>
		<?php
	}


	/**
	 * Content for News Meta Box
	 *
	 * @author RJ Bruneel
	 **/

	public function news_meta_callback( $post ) {

		global $post;

		// Use nonce for verification
		wp_nonce_field( 'person_news_noncename', 'person_news_noncename' );
		?>

		<div id="meta_inner">
			<div class="error">
				<p>
					"News Articles" fields are no longer used! Please create a <a href="<?php echo get_admin_url( get_current_blog_id(), 'post-new.php?post_type=publication'); ?>">Publication</a> and set Publication Type to "Faculty News".
				</p>
			</div>
			<?php
				//get the saved meta as an array
				$person_news = get_post_meta($post->ID, 'person_news', true);
				$count = 0;

				ob_start();
			?>
				<div class="item-container">
					<div class="form-group">
						<label>Title</label>
						<input type="text" class="form-control" name="person_news[%1$s][title]" value="%2$s">
					</div>
					<div class="form-group">
						<label>Link</label>
						<input type="text" class="form-control" name="person_news[%1$s][link]" value="%3$s">
					</div>
					<div class="form-group">
						<label>Summary</label>
						<textarea name="person_news[%1$s][summary]">%4$s</textarea>
					</div>
					<span class="remove-item button">Remove Item</span>
				</div>
			<?php
				$fields = ob_get_clean();

				foreach( $person_news as $article ) {
					printf( $fields, $count, $article['title'], $article['link'], $article['summary'] );
					$count = $count + 1;
				}

				// Add empty set of fields at the end
				printf( $fields, $count, '', '', '' );
			?>

			<div class="article-container"></div>
			<span class="add-item button button-primary" data-ucf-count="<?php echo $count; ?>">Add New Item</span>
		</div>
		<?php
	}


	/**
	 * Content for Research Meta Box
	 *
	 * @author RJ Bruneel
	 **/

	public function research_meta_callback( $post ) {

		global $post;

		// Use nonce for verification
		wp_nonce_field( 'person_research_noncename', 'person_research_noncename' );
		?>

		<div id="meta_inner">
			<div class="error">
				<p>
					"Research and Publications" fields are no longer used! Please create a <a href="<?php echo get_admin_url( get_current_blog_id(), 'post-new.php?post_type=publication'); ?>">Publication</a> and set Publication Type to "Faculty Research".
				</p>
			</div>
			<?php
				//get the saved meta as an array
				$person_research = get_post_meta($post->ID, 'person_research', true);
				$count = 0;

				ob_start();
			?>
				<div class="item-container">
					<div class="form-group">
						<label>Title</label>
						<input type="text" class="form-control" name="person_research[%1$s][title]" value="%2$s">
					</div>
					<div class="form-group">
						<label>Link</label>
						<input type="text" class="form-control" name="person_research[%1$s][link]" value="%3$s">
					</div>
					<div class="form-group">
						<label>Summary</label>
						<textarea name="person_research[%1$s][summary]">%4$s</textarea>
					</div>
					<span class="remove-item button">Remove Item</span>
				</div>
			<?php
				$fields = ob_get_clean();

				foreach( $person_research as $article ) {
					printf($fields, $count, $article['title'], $article['link'], $article['summary'] );
					$count = $count + 1;
				}

				// Add empty set of fields at the end
				printf( $fields, $count, '', '', '' );
			?>

			<div class="article-container"></div>
			<span class="add-item button button-primary" data-ucf-count="<?php echo $count; ?>">Add New Item</span>
		</div>
		<?php
	}


	/**
	 * Content for Media Meta Box
	 *
	 * @author RJ Bruneel
	 **/

	public function media_meta_callback( $post ) {

		global $post;

		// Use nonce for verification
		wp_nonce_field( 'person_media_noncename', 'person_media_noncename' );
		?>

		<div id="meta_inner">
			<?php
				//get the saved meta as an array
				$person_media = get_post_meta($post->ID, 'person_media', true);
				$count = 0;

				ob_start();
			?>
				<div class="item-container">
					<div class="form-group">
						<label>Title</label>
						<input type="text" class="form-control" name="person_media[%1$s][title]" value="%2$s">
					</div>
					<div class="form-group">
						<label>Video/Media URL</label>
						<input type="text" class="form-control" name="person_media[%1$s][link]" value="%3$s">
					</div>
					<div class="form-group">
						<label>Date</label>
						<input type="text" class="form-control" name="person_media[%1$s][date]" value="%4$s">
					</div>
					<span class="remove-item button">Remove Item</span>
				</div>
			<?php
				$fields = ob_get_clean();

				foreach( $person_media as $article ) {
					printf( $fields, $count, $article['title'], $article['link'], $article['date'] );
					$count = $count + 1;
				}

				// Add empty set of fields at the end
				printf( $fields, $count, '', '', '' );
			?>

			<div class="article-container"></div>
			<span class="add-item button button-primary" data-ucf-count="<?php echo $count; ?>">Add New Item</span>
		</div>
		<?php
	}

	public function get_objects($options=array()){
		$options['order']    = 'ASC';
		$options['orderby']  = 'person_orderby_name';
		$options['meta_key'] = 'person_orderby_name';
		return parent::get_objects($options);
	}

	public static function get_name($person) {
		$prefix = get_post_meta($person->ID, 'person_title_prefix', True);
		$suffix = get_post_meta($person->ID, 'person_title_suffix', True);
		$name = $person->post_title;
		return $prefix.' '.$name.$suffix;
	}

	public static function get_phones($person) {
		$phones = get_post_meta($person->ID, 'person_phones', True);
		return ($phones != '') ? explode(',', $phones) : array();
	}

	public static function get_photo( $person ) {
		$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $person->ID ), 'person-thumb' );
		if ( $image_url ) {
			$image_url = $image_url[0];
		}
		else {
			$image_url = get_bloginfo('stylesheet_directory').'/static/img/no-photo.jpg';
		}
		return $image_url;
	}

	public static function get_office_hours( $person ) {

		// Get all meta for person.
		$meta = get_post_meta($person->ID);

		// Get only keys that start with person_office_hours_
		$matched_meta = array_intersect_key($meta, array_flip(preg_grep('/^person_office_hours_/', array_keys($meta))));

		$hours = array();

		foreach($matched_meta as $key=>$office_hours) {
			$split_key = explode('_', $key);
			switch($split_key[3]) {
				case 'm':
					$hours['Sunday'] = $office_hours[0];
					break;
				case 'tu':
					$hours['Tuesday'] = $office_hours[0];
					break;
				case 'w':
					$hours['Wednesday'] = $office_hours[0];
					break;
				case 'th':
					$hours['Thursday'] = $office_hours[0];
					break;
				case 'f':
					$hours['Friday'] = $office_hours[0];
					break;
				case 'sa':
					$hours['Saturday'] = $office_hours[0];
					break;
				default:
					break;
			}
		}

		return $hours;
	}

	public function objectsToHTML($people, $css_classes) {
		$count = 0;
		$is_picture_list = strpos($css_classes, 'person-picture-list') !== false;
		$child_css_classes = '';

		if ($is_picture_list) {
			$child_css_classes = 'person-picture-wrap';
		}

		ob_start();

		if ($people):
		?>
			<ul class="<?php if ( $css_classes ) { echo $css_classes; } ?>">
				<?php
				foreach( $people as $person ):
				?>
					<li class="<?php echo $child_css_classes; ?>" data-post-id="<?php echo $person->ID; ?>">
						<?php echo Person::toHTML( $person, $child_css_classes ); ?>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		<?php
		endif;

		return ob_get_clean();
	}

	public function toHTML($person, $css_classes) {
		$link = ($person->post_content != '') ? True : False;
		$is_picture_list = strpos( $css_classes, 'person-picture-wrap' ) !== false;

		if ($is_picture_list) {
			$image_url = Person::get_photo( $person );
		}

		ob_start();
	?>
		<?php if($link): ?>
			<a href="<?php echo get_permalink($person->ID); ?>">
		<?php endif; ?>
			<?php if ($is_picture_list): ?>
				<img class="photo" src="<?php echo $image_url; ?>" />
			<?php endif;?>
				<span class="name"><?php echo Person::get_name( $person ); ?></span>
				<span class="title"><?php echo get_post_meta( $person->ID, 'person_jobtitle', True ); ?></span>
		<?php if($link): ?>
			</a>
		<?php endif; ?>
	<?php
		return ob_get_clean();
	}
}


class Post extends CustomPostType {
	public
		$name           = 'post',
		$plural_name    = 'Posts',
		$singular_name  = 'Post',
		$add_new_item   = 'Add New Post',
		$edit_item      = 'Edit Post',
		$new_item       = 'New Post',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array('post_tag', 'category'),
		$built_in       = True;

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Stylesheet',
				'desc' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
			),
		);
	}
}


class Spotlight extends CustomPostType {
	public
		$name           = 'spotlight',
		$plural_name    = 'Spotlights',
		$singular_name  = 'Spotlight',
		$add_new_item   = 'Add New Spotlight',
		$edit_item      = 'Edit Spotlight',
		$new_item       = 'New Spotlight',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = False,
		$use_shortcode  = True,

		$taxonomies     = array();


	public function toHTML( $object ) {
		ob_start();
	?>
		<a class="ga-event-link" href="<?php echo get_permalink( $object->ID ); ?>" data-ga-category="Spotlight Links" data-ga-label="<?php echo $object->post_title; ?>">
			<?php echo get_the_post_thumbnail( $object->ID, 'spotlight-thumb' ); ?>
		</a>
	<?php
		return ob_get_clean();
	}
}


class Publication extends CustomPostType {
	public
		$name           = 'publication',
		$plural_name    = 'Publications',
		$singular_name  = 'Publication',
		$add_new_item   = 'Add New Publication',
		$edit_item      = 'Edit Publication',
		$new_item       = 'New Publication',
		$public         = True,
		$use_editor     = True,
		$use_excerpt    = True,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = False,

		$taxonomies     = array('publication_types');


	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'People',
				'desc' => 'People associated with this publication.',
				'id' => $prefix.'people',
				'type' => 'multiselect',
				'options' => $this->get_objects_as_options(array('post_type' => 'person')),
			),
		);
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if (count($objects) < 1){ return '';}

		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;

		ob_start();
		?>
		<ul class="<?php if ( $css_classes ) { echo $css_classes; } else { ?><?php echo $class->options('name'); ?>-list<?php } ?>">
			<?php foreach( $objects as $o ): ?>
			<li class="publication-list-item" data-post-id="<?php echo $o->ID; ?>">
				<article>
					<?php echo $class->toHTML( $o ); ?>
				</article>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public function toHTML( $object ) {
		$excerpt = trim( $object->post_excerpt );
		$post_content = trim( $object->post_content );
		$links_to = get_post_meta( $object->ID, '_links_to', true );
		if ( empty( $excerpt ) ) {
			$excerpt = wp_trim_words( $post_content );
		}
		ob_start();
	?>
		<?php if ( empty( $links_to ) && empty( $post_content ) ): ?>
			<span class="publication-title"><?php echo $object->post_title; ?></span>
		<?php else: ?>
			<a class="publication-title" href="<?php echo get_permalink( $object->ID ); ?>"><?php echo $object->post_title; ?></a>
		<?php endif; ?>
		<div class="publication-excerpt"><?php echo $excerpt; ?></div>
	<?php
		return ob_get_clean();
	}
}

?>
