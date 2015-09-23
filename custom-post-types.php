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
				'name' => 'Call to Action Markup',
				'desc' => 'The content of this field will be displayed under the side menu. Accepts HTML markup.',
				'id'   => $prefix.'cta_markup',
				'type' => 'textarea'
			),
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

class Person extends CustomPostType {
	public
		$name           = 'person',
		$plural_name    = 'People',
		$singular_name  = 'Person',
		$add_new_item   = 'Add New Person',
		$edit_item      = 'Edit Person',
		$new_item       = 'New Person',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = True,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$taxonomies     = array('cohorts');

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Hometown',
				'desc' => 'The hometown of the student',
				'id'   => $prefix.'hometown',
				'type' => 'text'
			),
			array(
				'name' => 'Undergraduate Institution',
				'desc' => 'The student\'s undergraduate institution',
				'id'   => $prefix.'undergrad_institution',
				'type' => 'text'
			),
			array(
				'name' => 'Undergraduate Degree',
				'desc' => 'The student\'s undergraduate degree',
				'id'   => $prefix.'undergrad_degree',
				'type' => 'text'
			),
			array(
				'name' => 'PostGraduate Degree',
				'desc' => '',
				'id'   => $prefix.'postgrad_degree',
				'type' => 'text'
			),
			array(
				'name' => 'Assistantships and Interships',
				'desc' => 'The student\'s past internships and assistantships',
				'id'   => $prefix.'internships',
				'type' => 'text'
			),
			array(
				'name' => 'Community Outreach',
				'desc' => '',
				'id'   => $prefix.'outreach',
				'type' => 'text'
			),
			array(
				'name' => 'Career Aspirations',
				'desc' => '',
				'id'   => $prefix.'career',
				'type' => 'text'
			)
		);
	}

	public function toHTML( $object ) {
		$thumbnail  = get_the_post_thumbnail( $object->ID, 'thumbnail', array( 'class' => 'img-responsive img-rounded' ) );

		ob_start();

		?>
		<div class="col-sm-4">
			<a href="<?php echo get_permalink( $object->ID );?>">
				<div class="thumbnail person">
				<?php echo $thumbnail; ?>
				<div class="caption">
					<h3><?php echo $object->post_title; ?></h3>
				</div>
				</div>
			</a>
		</div>
		<?php

		return ob_get_clean();
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return ''; }

		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;

		ob_start();

		?>
		<div class="row person-list">
		<?php 
			$person = new Person();
			foreach( $objects as $object ) {
				echo $person::toHTML( $object );
			}
		?>
		</div>
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
			<h3><?php echo $object->post_title; ?></h3>
			<div class="row">
				<div class="col-sm-6">
					<?php echo get_the_post_thumbnail( $object->ID ); ?>
				</div>
				<div class="col-sm-6">
					<p class="main"><?php echo $object->post_excerpt; ?></p>
					<a href="<?php echo get_permalink( $object->ID );?>" class="learn-more">
						Learn More
					</a>
				</div>
			</div>
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
		$use_editor     = False,
		$use_excerpt    = True,
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = False;


	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
			array(
				'name' => 'Subtitle',
				'desc' => 'The subtitle of the publication.',
				'id'   => $prefix.'subtitle',
				'type' => 'text',
			),
			array(
				'name' => 'File',
				'desc' => 'The uploaded file for this publication.',
				'id' => $prefix.'file',
				'type' => 'file',
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
		$links_to = get_post_meta( $object->ID, '_links_to', true );
		$subtitle = get_post_meta( $object->ID, 'publication_subtitle', true );
		$file = wp_get_attachment_url( get_post_meta( $object->ID, 'publication_file', true ) );
		ob_start();
	?>
		<div class="publication">
			<h3>Publications</h3>
			<?php if ( empty( $links_to ) && ! empty( $file ) ): ?>
				<a href="<?php echo $file; ?>" alt="<?php echo $object->post_title; ?>" class="publication-image">
			<?php else: ?>
				<a href="<?php echo $links_to; ?>" alt="<?php echo $object->post_title; ?>" class="publication-image">
			<?php endif; ?>
					<?php echo get_the_post_thumbnail( $object->ID ); ?>
				</a>
			<a href="publications/" class="more-publications">More Publications</a>
		</div>
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
		$use_thumbnails = True,
		$use_order      = False,
		$use_title      = True,
		$use_metabox    = True,
		$use_shortcode  = False;

	public function fields() {
		$prefix = $this->options( 'name' ).'_';
		return array(
				array(
					'name'    => 'Image',
					'desc'    => 'The image to show on this centerpiece.',
					'id'      => $prefix.'image',
					'type'    => 'file',
				),
				array(
					'name'    => 'Expires',
					'desc'    => 'The expiration date of the centerpiece.',
					'id'      => $prefix.'expires',
					'type'    => 'date'
				),
				array(
					'name'    => __( 'Call to Action Title' ),
					'desc'    => __( 'The title to appear in the Call to Action' ),
					'id'      => $prefix.'cta_title',
					'type'    => 'text',
				),
				array(
					'name'    => __( 'Call to Action Content' ),
					'desc'    => __( 'The content to appear in the Call to Action' ),
					'id'      => $prefix.'cta_content',
					'type'    => 'textarea',
				),
				array(
					'name'    => __( 'Call to Action Button Text' ),
					'desc'    => __( 'The text to appear in the Call to Action Button' ),
					'id'      => $prefix.'cta_button_text',
					'type'    => 'text',
				),
				array(
					'name'    => __( 'Call to Action Button Link' ),
					'desc'    => __( 'The link to use for the Call to Action Button' ),
					'id'      => $prefix.'cta_button_link',
					'type'    => 'text',
				),
			);
	}
}

?>
