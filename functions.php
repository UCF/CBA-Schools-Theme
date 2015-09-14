<?php
require_once('functions/base.php');   			# Base theme functions
require_once('functions/feeds.php');			# Where functions related to feed data live
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

// Add theme-specific functions here.


/**
 * Returns a theme option value or NULL if it doesn't exist
 **/
function get_theme_option( $key ) {
	global $theme_options;
	return isset( $theme_options[$key] ) ? $theme_options[$key] : NULL;
}


/**
 * Disable comments and trackbacks/pingbacks on this site.
 **/
update_option( 'default_ping_status', 'off' );
update_option( 'default_comment_status', 'off' );


/**
 * Hide unused admin tools (Links, Comments, etc)
 **/
function hide_admin_links() {
	remove_menu_page( 'link-manager.php' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'hide_admin_links' );


/**
 * Display COBA social buttons (non-post-specific)
 **/
function display_site_social($alt=false, $classes=null) {
	$googleplus_url = get_theme_option( 'googleplus_url' );
	$linkedin_url   = get_theme_option( 'linkedin_url' );
	$twitter_url    = get_theme_option( 'twitter_url' );
	$facebook_url   = get_theme_option( 'facebook_url' );
	$instagram_url  = get_theme_option( 'instagram_url' );
	$youtube_url    = get_theme_option( 'youtube_url' );

	$googleplus_class = 'btn-googleplus';
	$linkedin_class   = 'btn-linkedin';
	$twitter_class    = 'btn-twitter';
	$facebook_class   = 'btn-facebook';
	$instagram_class  = 'btn-instagram';
	$youtube_class    = 'btn-youtube';

	if ( $alt == true ) {
		$googleplus_class = $googleplus_class.'-alt';
		$linkedin_class   = $linkedin_class.'-alt';
		$twitter_class    = $twitter_class.'-alt';
		$facebook_class   = $facebook_class.'-alt';
		$instagram_class  = $instagram_class.'-alt';
		$youtube_class    = $youtube_class.'-alt';
	}

	ob_start();
?>
<div class="social <?php if ($classes) : echo $classes; endif; ?>">
	<?php if ( $googleplus_url ) : ?>
	<a class="<?php echo $googleplus_class; ?> ga-event-link" target="_blank" href="<?php echo $googleplus_url; ?>">Follow us on Google+</a>
	<?php endif; ?>
	<?php if ( $linkedin_url ) : ?>
	<a class="<?php echo $linkedin_class; ?> ga-event-link" target="_blank" href="<?php echo $linkedin_url; ?>">View our LinkedIn page</a>
	<?php endif; ?>
	<?php if ( $twitter_url ) : ?>
	<a class="<?php echo $twitter_class; ?> ga-event-link" target="_blank" href="<?php echo $twitter_url; ?>">Follow us on Twitter</a>
	<?php endif; ?>
	<?php if ( $facebook_url ) : ?>
	<a class="<?php echo $facebook_class; ?> ga-event-link" target="_blank" href="<?php echo $facebook_url; ?>">Like us on Facebook</a>
	<?php endif; ?>
	<?php if ( $instagram_url ) : ?>
	<a class="<?php echo $instagram_class; ?> ga-event-link" target="_blank" href="<?php echo $instagram_url; ?>">Find us on Instagram</a>
	<?php endif; ?>
	<?php if ( $youtube_url ) : ?>
	<a class="<?php echo $youtube_class; ?> ga-event-link" target="_blank" href="<?php echo $youtube_url; ?>">Follow us on YouTube</a>
	<?php endif; ?>
</div>
<?php
	return ob_get_clean();
}


/**
 * Display COBA address information (from theme options)
 **/
function display_contact_address() {
	$address = get_theme_option( 'organization_address' );
	if ( !empty( $address ) ) {
		ob_start();
	?>
	<address>
		<?php echo nl2br( $address ); ?>
	</address>
	<?php
		return ob_get_clean();
	}
	return;
}


/**
 * Display a homepagefeature and its CTA.
 **/
function display_homepagefeatures() {
	$args = array(
		'post_type' => 'homepagefeature',
	);

	// Get the homepagefeatures specified in theme options;
	// if none are set, get the most recent 5:
	$feature_ids = array_filter(array(
		get_theme_option( 'home_feature_1' ),
		get_theme_option( 'home_feature_2' ),
		get_theme_option( 'home_feature_3' ),
		get_theme_option( 'home_feature_4' ),
		get_theme_option( 'home_feature_5' ),
	));

	if (empty($feature_ids)) {
		$args['numberposts'] = 5;
	}
	else {
		$post_in = array();
		foreach ($feature_ids as $feature_id) {
			if ($feature_id) {
				$post_in[] .= $feature_id;
			}
		}
		$args['post__in'] = $post_in;
		$args['orderby'] = 'post__in';
	}

	$features = get_posts($args);
	if ( !$features ) { return; }

	ob_start();

	$count = 0;
	foreach ( $features as $feature ) {
		$photo = wp_get_attachment_image_src( get_post_thumbnail_id( $feature->ID ), 'homepagefeature-photo' );
		if ( $photo ) { $photo = $photo[0]; }
		$title = get_post_meta( $feature->ID, 'homepagefeature_link_text', true );
		$subtitle = get_post_meta( $feature->ID, 'homepagefeature_link_subtext', true );
		$content_text = get_post_meta( $feature->ID, 'homepagefeature_link_content', true );
		if ( strlen( $content_text ) > 240 ) {
			$content_text = substr( $content_text, 0, 240 );
			// Truncate last whole word
			$content_text = substr( $content_text, 0, strrpos( $content_text, ' ' ) ) . '&hellip;';
		}
		$link = get_permalink( $feature->ID );
		$css_class = $count !== 0 ? '' : 'active';
	?>
		<div class="feature-photo <?php echo $css_class; ?>">
			<img class="feature-photo-img" src="<?php echo $photo; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>">
			<a class="feature-photo-cta ga-event-link" href="<?php echo $link; ?>" data-ga-category="Home Page Feature Links" data-ga-label="<?php echo $title; ?><?php if ( $subtitle ) { echo ' - '.$subtitle; } ?>">
				<h2 class="feature-photo-cta-title"><?php echo $title; ?></h2>
				<span class="feature-photo-cta-subtitle"><?php echo $subtitle; ?></span>
				<p class="feature-photo-cta-content"><?php echo $content_text; ?></p>
			</a>
		</div>
	<?php
		$count++;
	}

	return ob_get_clean();
}


/**
 * Display COBA Pass button.
 * Also creates a shortcode that executes this function.
 **/
function display_coba_pass() {
	$url = get_theme_option( 'coba_pass_url' );
	ob_start();
?>
	<a class="coba-pass" href="<?php echo $url; ?>">COBA Pass</a>
<?php
	return ob_get_clean();
}
add_shortcode('coba-pass', 'display_coba_pass');


function display_aascb_logo() {
	$url = get_theme_option( 'aascb_url' );
	ob_start();
?>
	<a class="aascb-logo" href="<?php echo $url; ?>">AASCB Accredidation</a>
<?php
	return ob_get_clean();
}
add_shortcode( 'aascb_logo', 'display_aascb_logo' );

/**
 * Displays an alternate CTA image+link, if one is set in Theme Options.
 **/
function display_home_alt_cta() {
	$cta_isset  = get_theme_option( 'enable_home_cta_alt' ) == 1 ? true : false;
	$cta_url    = get_theme_option( 'home_cta_alt_url' );
	$cta_img_id = get_theme_option( 'home_cta_alt_image' );

	if ( $cta_isset && !empty( $cta_url ) && !empty( $cta_img_id ) ) {
		$cta_img = wp_get_attachment_image( intval( $cta_img_id ), 'full' );
	}

	if ( empty( $cta_img ) ) { return; }

	$cta_alttext = get_post_meta( $cta_img_id, '_wp_attachment_image_alt', true );
	if ( empty( $cta_alttext ) ) {
		$cta_alttext = 'Alternate promoted content link';
	}

	ob_start();
?>
	<a class="home-alt-cta ga-event-link" href="<?php echo $cta_url; ?>" data-ga-category="Homepage Alternate Call-To-Action" data-ga-label="<?php echo $cta_alttext; ?>">
		<?php echo $cta_img; ?>
	</a>
<?php
	return ob_get_clean();
}


/**
 * Returns all header-menu menu items.
 * Returns array of WP_Posts of type 'nav_menu_item'.
 **/
function get_header_menu_items() {
	$menu_items = null;
	$locations = get_nav_menu_locations();

	if ( array_key_exists( 'header-menu', $locations ) ) {
		$menu = wp_get_nav_menu_object( $locations['header-menu'] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );
	}

	return $menu_items;
}


/**
 * Attempts to return a post's header menu nav item by post ID.
 * Returns WP_Post object of type 'nav_menu_item', or null if nothing
 * is found.
 *
 * NOTE: if the object with $post_id exists in the header-menu more
 * than once, this function will return null (we do not know which menu
 * item to prioritize!)
 **/
function get_post_header_nav_item( $post_id ) {
	$post_menu_item = null;
	$locations = get_nav_menu_locations();

	if ( array_key_exists( 'header-menu', $locations ) ) {
		// Get the current menu item relating to this page
		$post_menu_item_array = get_posts( array(
			'post_type' => 'nav_menu_item',
			'meta_key' => '_menu_item_object_id',
			'meta_value' => $post_id,
			'tax_query' => array(
				array(
					'taxonomy' => 'nav_menu',
					'field' => 'term_id',
					'terms' => $locations['header-menu']
				)
			)
		));

		// Check if this post has a menu item and is unique in the menu
		if ( $post_menu_item_array && sizeof( $post_menu_item_array ) == 1 ) {
			$post_menu_item = $post_menu_item_array[0];

			// Decorate with nav_menu_item meta values for convenience
			$post_menu_item = wp_setup_nav_menu_item( $post_menu_item );
		}
	}

	return $post_menu_item;
}


/**
 * Returns a nav_menu_item WP_Post object by ID, decorated with
 * meta values for convenience.
 **/
function get_nav_item_by_id( $nav_menu_item_id ) {
	$nav_item = null;

	$nav_item_post = get_post( $nav_menu_item_id );
	if ( $nav_item_post ) {
		$nav_item = wp_setup_nav_menu_item( $nav_item_post );
	}

	return $nav_item;
}


/**
 * Returns a parent nav_menu_item WP_Post object by the
 * child's ID.
 **/
function get_nav_item_parent( $nav_menu_item_id ) {
	$nav_item_parent = null;

	$parent_id = get_post_meta( $nav_menu_item_id, '_menu_item_menu_item_parent', true );
	if ( $parent_id ) {
		$nav_item_parent = get_nav_item_by_id( $parent_id );
	}

	return $nav_item_parent;
}


/**
 * Get the top most parent menu item ID based on a post ID
 */
function get_highest_parent_menu_item_id( $the_id ) {
	$parent_post = get_nav_item_parent( $the_id );
	if ( $parent_post ) {
		return get_highest_parent_menu_item_id( $parent_post->ID );
	}

	return $the_id;
}


/**
 * Attempts to fetch the nav item's children associated with
 * the given nav_menu_item ID from the header-menu menu.
 *
 * Returns array of WP_Posts of the menu items' represented post types
 * (NOT nav_menu_item).
 **/
function get_header_nav_item_children( $nav_item_id ) {
	$nav_item = get_nav_item_by_id( $nav_item_id );
	$child_ids = array();
	$children = null;

	$locations = get_nav_menu_locations();
	if ( array_key_exists( 'header-menu', $locations ) ) {
		$menu = wp_get_nav_menu_object( $locations['header-menu'] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

		// Loop thru all $menu_items and return those that represent a post
		// (aren't external links) and whose parent ID is $nav_item_id
		if ( $menu_items ) {
			foreach ( $menu_items as $menu_item ) {
				if ( intval( $menu_item->menu_item_parent ) == $nav_item_id && $menu_item->object_id ) {
					$child_ids[] = intval( $menu_item->object_id );
				}
			}
		}
	}

	if ($child_ids) {
		$children = get_posts( array(
			'post_type' => 'any',
			'post__in' => $child_ids,
			'numberposts' => -1,
			'orderby' => 'post__in'
		) );
	}

	return $children;
}


/**
 * Get a page's breadcrumb parent.
 * Returns WP_Post object of type 'nav_menu_item'.
 **/
function get_page_breadcrumb_parent( $page_id ) {
	$breadcrumb_parent = null;
	$page_nav_item = get_post_header_nav_item( $page_id );

	if ( $page_nav_item ) {
		$breadcrumb_parent = get_nav_item_parent( $page_nav_item->ID );
	}

	return $breadcrumb_parent;
}


/**
 * Displays a page's sidebar menu.
 **/
function display_page_side_menu( $post_id, $show_heading=false ) {
	$menu_items = get_header_menu_items();
	$post_menu_item = get_post_header_nav_item( $post_id );

	ob_start();

	if ( $post_menu_item ) {
		$menu_parent_id     = null;
		$menu_parent        = get_nav_item_parent( $post_menu_item->ID );
		if ( $menu_parent ) {
			$menu_parent_id = $menu_parent->ID;
		}
		$menu_top_parent_id = get_highest_parent_menu_item_id( $post_menu_item->ID );
		$menu_top_parent    = get_nav_item_by_id( $menu_top_parent_id );

		if ( !$menu_parent_id ) {
			// the post menu item is the parent menu
			$menu_parent_id = $post_menu_item->ID;
		}
	?>
		<?php if ( $show_heading ): ?>
		<h3 class="side-menu-title">
			<a href="<?php echo $menu_top_parent->url; ?>">
				<?php echo $menu_top_parent->title; ?>
			</a>
		</h3>
		<?php endif; ?>
		<ul class="side-menu">
		<?php
			foreach ( $menu_items as $k => $menu_item ):
				// Display all the menus items that have the same parent menu item
				if ( intval( $menu_item->menu_item_parent ) == $menu_top_parent_id ):
		?>
					<li><a class="<?php if ( intval( $menu_item->object_id ) == $post_id ) : ?>active<?php endif; ?>" href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->title; ?></a>

					<?php
					// Test for children menu items
					$is_child_menu = False;

					// Display sub menu if on the child page and it has children
					// or if on childrens' children page.
					// Otherwise don't display the submenu (roll it up)
					for ( $sub_menu_key = $k + 1; $sub_menu_key < count( $menu_items ) && ( ( intval( $menu_items[$sub_menu_key]->menu_item_parent ) == $menu_item->ID && $menu_item->ID == $post_menu_item->ID ) || ( $menu_parent_id != $menu_top_parent_id && $menu_parent_id == intval( $menu_items[$sub_menu_key]->menu_item_parent ) ) ); $sub_menu_key++ ):

						if ( $k + 1 == $sub_menu_key ):
							$is_child_menu = True;
							?>
							<ul class="sub-menu">
							<?php
						endif;

						$child = $menu_items[$sub_menu_key];

					?>
							<li>
								<a class="<?php if ( intval( $child->object_id ) == $post_id ) : ?>active<?php endif; ?>" href="<?php echo $child->url; ?>">
									<?php echo $child->title; ?>
								</a>
					<?php
					endfor;

					// Close child menu if it exists
					if ( $is_child_menu ):
					?>
						</ul>
					<?php endif; ?>
					</li>
		<?php
				endif;
			endforeach;
		?>
			</ul>
<?php
	}

	return ob_get_clean();
}


/**
 * Display additional more information menu if assigned to the post
 */
function display_custom_side_menu( $post_id ) {
	ob_start();

	$more_info_nav_val 			= get_post_meta( $post_id, 'page_widget_l_moreinfo', TRUE );
	$more_info_nav_val_title 	= get_post_meta( $post_id, 'page_widget_l_moreinfo_title', TRUE );

	if ( $more_info_nav_val ) {
		if ( !empty( $more_info_nav_val_title ) ) {
?>
		<h3 class="side-menu-title" id="sidebar-l-moreinfo" ><?php echo $more_info_nav_val_title; ?>:</h3>
<?php
		}

		$args = array(
			'menu' => $more_info_nav_val,
			'container' => 'false',
			'menu_class' => 'side-menu',
		);
		wp_nav_menu( $args );
	}

	return ob_get_clean();
}


/**
 * Displays a .post-grid-list of child pages, based on the
 * header nav menu's page grouping.
 **/
function pages_grid_list( $pages, $css_classes, $is_child_list=false ) {
	$parent_heading = 'h2';
	if ( !$css_classes ) {
		$css_classes = 'post-grid-list';
	}
	if ( $is_child_list ) {
		$parent_heading = 'h3';
	}

	ob_start();

	if ( $pages ) :
	?>
		<ul class="<?php echo $css_classes; ?>">
			<?php
			foreach( $pages as $page ):
				$desc_brief = get_post_meta( $page->ID, 'page_desc_brief', true );
			?>
			<li class="post-grid-item item-no-children" data-post-id="<?php echo $page->ID; ?>">
				<div class="item-details">
					<<?php echo $parent_heading; ?> class="item-title">
						<a href="<?php echo get_permalink($page->ID); ?>">
							<?php echo $page->post_title; ?>
						</a>
					</<?php echo $parent_heading; ?>>
					<?php if ( !empty( $desc_brief ) ): ?>
						<div class="item-desc">
							<?php echo $desc_brief; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="item-cta">
					<a class="btn btn-success btn-block" href="<?php echo get_permalink( $page->ID ); ?>">
						Learn More<span class="sr-only"> about <?php echo $page->post_title; ?></span>
						<span class="fa fa-chevron-right"></span>
					</a>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php
	endif;

	return ob_get_clean();
}


/**
 * Shortcode for displaying a grid of child pages.
 * Needs to be defined here so we have access to nav_menu_item-related
 * functions.
 * Optionally specify a page by title; defaults to the current page.
 **/
function sc_child_page_list( $attr ) {
	global $post;
	$page = !empty( $attr['page'] ) ? get_page_by_title( $attr['page'] ) : $post;
	$nav_item = get_post_header_nav_item( $page->ID );
	$child_pages = get_header_nav_item_children( $nav_item->ID );

	// Die here if no child pages are found
	if ( !$child_pages ) {
		return;
	}

	$child_column_count = 2;
	$pages_per_col = round( count( $child_pages ) / $child_column_count );
	$grouped_child_degrees = array_chunk( $child_pages, $pages_per_col, true );

	ob_start();
?>
	<div class="post-grid-list">
<?php
	$count = 0;
	foreach ( $grouped_child_degrees as $child_col ) {
		$child_css_classes = 'post-grid-list-children';
		if ( $count == 0 ) {
			$child_css_classes .= ' first';
		}
		echo pages_grid_list( $child_col, $child_css_classes, $is_child_list=true );
		$count++;
	}
?>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode( 'child-page-list', 'sc_child_page_list' );


/**
 * Uses Wordpress's built-in embed shortcode to return
 * markup for a video embed by URL.
 * https://wordpress.org/support/topic/call-function-called-by-embed-shortcode-direct
 **/
function get_embed_html( $media_url ) {
	global $wp_embed;
	return $wp_embed->run_shortcode( '[embed]' . $media_url . '[/embed]' );
}


/**
 * Adding our custom fields to the $form_fields array
 *
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function url_image_attachment_fields_to_edit( $form_fields, $post ) {
	// $form_fields is a special array of fields to include in the attachment form
	// $post is the attachment record in the database
	//     $post->post_type == 'attachment'
	// (attachments are treated as posts in Wordpress)

	// add our custom field to the $form_fields array
	// input type="text" name/id="attachments[$attachment->ID][media_link]"
	$form_fields["media_link"] = array(
		"label" => __("Link URL"),
		"input" => "text", // this is default if "input" is omitted
		"value" => get_post_meta( $post->ID, "_media_link", true )
	);

	return $form_fields;
}
// attach our function to the correct hook
add_filter( "attachment_fields_to_edit", "url_image_attachment_fields_to_edit", null, 2 );


/**
 * @param array $post
 * @param array $attachment
 * @return array
 */
function url_image_attachment_fields_to_save( $post, $attachment ) {
	// $attachment part of the form $_POST ($_POST[attachments][postID])
	// $post attachments wp post array - will be saved after returned
	//     $post['post_type'] == 'attachment'
	if( isset( $attachment['media_link'] ) ){
		// update_post_meta(postID, meta_key, meta_value);
		update_post_meta( $post['ID'], '_media_link', $attachment['media_link'] );
	}
	return $post;
}
add_filter( 'attachment_fields_to_save','url_image_attachment_fields_to_save', null, 2);

class Social_Icons_Widget extends WP_Widget {
	/*
	 * Register Widget with WordPress
	*/
	function __construct() {

		$widget_ops = array( 'description' => __('Displays all registered social media icons.' ) );

		parent::__construct(
			'social_icon_widget',
			__('Social Icons','text_domain'),
			$widget_ops
		);
	}

	/*
	 * Front end display code.
	 */
	public function widget( $args, $instance ) {
		echo display_site_social(false, 'hidden-xs');
	}
}

function register_social_icons_widget() {
	register_widget('Social_Icons_Widget');
}

add_action( 'widgets_init', 'register_social_icons_widget' );


/**
 * Adds post meta data values as $post object properties for convenience.
 * Excludes WordPress' internal custom keys (prefixed with '_').
 **/
function attach_post_metadata_properties( $post ) {
	$metas = get_post_meta( $post->ID );
	foreach ( $metas as $key => $val ) {
		if ( substr( $key, 0, 1 ) !== '_' ) {
			$val = is_array( $val ) ? maybe_unserialize( $val[0] ) : maybe_unserialize( $val );
			$post->$key = $val;
		}
	}
	return $post;
}


/**
 * Return's a term's custom meta value by key name.
 * Assumes that term data are saved as options using the naming schema
 * 'tax_TAXONOMY-SLUG_TERMID'
 **/
function get_term_custom_meta( $term_id, $taxonomy, $key ) {
	if ( empty( $term_id ) || empty( $taxonomy ) || empty( $key ) ) {
		return false;
	}

	$term_meta = get_option( 'tax_' + $taxonomy + '_' + $term_id );
	if ( $term_meta && isset( $term_meta[$key] ) ) {
		$val = $term_meta[$key];
	}
	else {
		$val = false;
	}
	return $val;
}


/**
 * Adds a URL field for Department taxonomy terms.
 **/

// Prints label for department url field.
function departments_url_label() {
	ob_start();
?>
	<label for="term_meta[department_links_to_page]"><?php echo __( 'Links To Page' ); ?></label>
<?php
	return ob_get_clean();
}

// Prints label for department url field.
function departments_url_field( $value=null ) {
	ob_start();
?>
	<select class="department-links-to-autocomplete" name="term_meta[department_links_to_page]" id="term_meta[department_links_to_page]">
		<option value="">--</option>
		<?php
		$page = new Page;
		$options = $page->get_objects_as_options();
		if ( $options ):
			foreach ( $options as $page_name => $page_id ):
		?>
			<option value="<?php echo $page_id; ?>" <?php if ( $value && $value == $page_id ) { ?>selected<?php } ?>><?php echo $page_name; ?></option>
		<?php
			endforeach;
		endif;
		?>
	</select>
	<p class="description"><?php echo __( 'Specify a page this term should link to when listed within the site.  If no page is selected, no link will be rendered (just the Department name is displayed).' ); ?></p>
<?php
	return ob_get_clean();
}

// Adds field to Add Department form.
function departments_add_url_field() {
?>
	<div class="form-field term-meta-page_link-wrap">
		<?php echo departments_url_label(); ?>
		<?php echo departments_url_field(); ?>
	</div>
<?php
}
add_action( 'departments_add_form_fields', 'departments_add_url_field', 10, 2 );

// Adds field to Edit Department form.
function departments_edit_url_field( $term ) {
	$term_id = $term->term_id;
	$page_id = get_term_custom_meta( $term_id, 'departments', 'department_links_to_page' );
?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<?php echo departments_url_label(); ?>
		</th>
		<td>
			<?php echo departments_url_field( $page_id ); ?>
		</td>
	</tr>
<?php
}
add_action( 'departments_edit_form_fields', 'departments_edit_url_field', 10, 2 );

// Saves Department url field value.
function departments_save_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$term_id = $term_id;
		$term_meta = get_option( 'tax_departments_' + $term_id );
		$term_keys = array_keys( $_POST['term_meta'] );
		foreach ( $term_keys as $key ) {
			if ( isset( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( 'tax_departments_' + $term_id, $term_meta );
	}
}
add_action( 'edited_departments', 'departments_save_custom_meta', 10, 2 );
add_action( 'create_departments', 'departments_save_custom_meta', 10, 2 );

// Adds column to existing Departments term list.
function departments_add_columns( $columns ) {
	$new_columns = array(
	    'cb' => '<input type="checkbox" />',
	    'name' => __('Name'),
	    'description' => __('Description'),
	    'slug' => __('Slug'),
	    'links_to_page' => __('Links to Page'),
	    'posts' => __('Posts')
	);
	return $new_columns;
}
add_filter( 'manage_edit-departments_columns', 'departments_add_columns' );

// Adds content to Departments columns
function departments_render_columns( $out, $name, $term_id ) {
    switch ( $name ) {
        case 'links_to_page':
        	$page_id = get_term_custom_meta( $term_id, 'departments', 'department_links_to_page' );
        	if ( $page_id ) {
        		$page = get_post( $page_id );
        		$out .= '<a href="'. get_edit_post_link( $page_id ) .'">'. $page->post_title .'</a>';
        	}
        	else {
        		$out .= '&mdash;';
        	}
            break;
        default:
            break;
    }
    return $out;
}
add_filter( 'manage_departments_custom_column', 'departments_render_columns', 10, 3);


/**
 * Add columns to wp admin that display extra information for Publications.
 **/

// Add columns to existing Publication admin list
function publication_add_columns( $columns ) {
	$columns['people'] = __('People');
	$columns['publication_types'] = __('Publication Types');
	return $columns;
}
add_filter( 'manage_publication_posts_columns', 'publication_add_columns' );

// Add content to new Publication columns
function publication_render_columns( $name, $post_id ) {
	switch ( $name ) {
		case 'people':
			$output = '';
			$people = get_post_meta( $post_id, 'publication_people', true );
			if ( $people ) {
				$people_posts = get_posts( array(
					'numberposts' => -1,
					'post__in' => $people,
					'post_type' => 'person',
				) );
				if ( $people_posts ) {
					$total = count( $people_posts );
					$count = 1;
					foreach ( $people_posts as $person_post ) {
						$output .= '<a href="'. get_edit_post_link( $person_post->ID ) . '">'. $person_post->post_title .'</a>';
						if ( $count < $total ) {
							$output .= ', ';
						}
						$count++;
					}
				}
				else {
					$output = '&mdash;';
				}
			}
			else {
				$output = '&mdash;';
			}
			echo $output;
			break;
		case 'publication_types':
			$output = '';
			$publication_types = wp_get_post_terms( $post_id, 'publication_types' );
			if ( $publication_types ) {
				$total = count( $publication_types );
				$count = 1;
				foreach ( $publication_types as $type ) {
					$output .= '<a href="'. get_edit_term_link( $type->term_id, 'publication_types', 'publication' ) .'">'. $type->name .'</a>';
					if ( $count < $total ) {
						$output .= ', ';
					}
					$count++;
				}
			}
			else {
				$output = '&mdash;';
			}
			echo $output;
			break;
	}
}
add_filter( 'manage_publication_posts_custom_column', 'publication_render_columns', 10, 2 );

?>
