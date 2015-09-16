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
		$use_excerpt    = True,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = True,

		$taxonomies     = array();

	public function Fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => __('Additional Content'),
				'desc' => __('Additional content that will appear below the name, featured image and excerpt.'),
				'id'   => $prefix.'additional_content',
				'type' => 'textarea'
			)
		);
	}


	public function toHTML( $object ) {
		$additional_content = get_post_meta( $object->ID, 'spotlight_additional_content', TRUE );
		ob_start();
	?>
		<div class="spotlight">
			<h2><?php echo $object->post_title; ?></h2>
			<?php echo get_the_post_thumbnail( $object->ID ); ?>
			<p><?php echo $object->post_excerpt; ?></p>
			<hr>
			<a href="<?php echo get_permalink( $object->ID );?>">
				Learn More...
			</a>
			<?php if ( $additional_content ) : ?>
				<div class="spotlight-additional-content">
					<?php echo $additional_content; ?>
				</div>
			<?php endif; ?>
		</div>
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

class Centerpiece extends CustomPostType {
	public
		$name           = 'centerpiece',
		$plural_name    = 'Centerpieces',
		$singular_name  = 'Centerpiece',
		$add_new_item   = 'Add New Centerpiece',
		$edit_item      = 'Edit Centerpiece',
		$new_item       = 'New Cenrterpiece',
		$public         = True,
		$use_editor     = False,
		$use_excerpt    = False,
		$use_thumbnails = False,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = False;

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
				array(
					'name' => 'Image',
					'desc' => 'The image to show on this centerpiece.',
					'id'   => $prefix.'image',
					'type' => 'file',
				),
				array(
					'name' => __( 'Call to Action Title' ),
					'desc' => __( 'The title to appear in the Call to Action' ),
					'id'   => $prefix.'cta_title',
					'type' => 'text',
				),
				array(
					'name' => __( 'Call to Action Content' ),
					'desc' => __( 'The content to appear in the Call to Action' ),
					'id'   => $prefix.'cta_content',
					'type' => 'textarea',
				),
				array(
					'name' => __( 'Call to Action Button Text' ),
					'desc' => __( 'The text to appear in the Call to Action Button' ),
					'id'   => $prefix.'cta_button_text',
					'type' => 'text',
				),
				array(
					'name' => __( 'Call to Action Button Link' ),
					'desc' => __( 'The link to use for the Call to Action Button' ),
					'id'   => $prefix.'cta_button_link',
					'type' => 'text',
				),
			);
	}
}

?>
