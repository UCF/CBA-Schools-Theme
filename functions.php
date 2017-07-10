<?php
require_once('functions/base.php');   			# Base theme functions
require_once('functions/feeds.php');			# Where functions related to feed data live
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

// Add theme-specific functions here.

function get_theme_mod_or_default( $mod, $fallback='' ) {
	return get_theme_mod( $mod, get_setting_default( $mod, $fallback ) );
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
function display_site_social($color='', $classes=null) {

	$googleplus_url = get_theme_mod_or_default( 'googleplus_url' );
	$linkedin_url   = get_theme_mod_or_default( 'linkedin_url' );
	$twitter_url    = get_theme_mod_or_default( 'twitter_url' );
	$facebook_url   = get_theme_mod_or_default( 'facebook_url' );
	$instagram_url  = get_theme_mod_or_default( 'instagram_url' );
	$youtube_url    = get_theme_mod_or_default( 'youtube_url' );
	$wordpress_url  = get_theme_mod_or_default( 'wordpress_url' );

	$googleplus_class = 'fa fa-google-plus-square fa-2x ' . $color;
	$linkedin_class   = 'fa fa-linkedin-square fa-2x ' . $color;
	$twitter_class    = 'fa fa-twitter-square fa-2x ' . $color;
	$facebook_class   = 'fa fa-facebook-square fa-2x ' . $color;
	$instagram_class  = 'fa fa-instagram fa-2x ' . $color;
	$youtube_class    = 'fa fa-youtube-square fa-2x ' . $color;
	$wordpress_class  = 'fa fa-wordpress fa-2x ' . $color;

	ob_start();
?>
<div class="social <?php if ($classes) : echo $classes; endif; ?>">
	<?php if ( $googleplus_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $googleplus_url; ?>">
		<i class="<?php echo $googleplus_class; ?>"></i>
		<span class="sr-only">Follow us on Google+</span>
	</a>
	<?php endif; ?>
	<?php if ( $linkedin_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $linkedin_url; ?>">
		<i class="<?php echo $linkedin_class; ?>"></i>
		<span class="sr-only">View our LinkedIn page</span></a>
	<?php endif; ?>
	<?php if ( $twitter_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $twitter_url; ?>">
		<i class="<?php echo $twitter_class; ?>"></i>
		<span class="sr-only">Follow us on Twitter</span></a>
	<?php endif; ?>
	<?php if ( $facebook_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $facebook_url; ?>">
		<i class="<?php echo $facebook_class; ?>"></i>
		<span class="sr-only">Like us on Facebook</span></a>
	<?php endif; ?>
	<?php if ( $instagram_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $instagram_url; ?>">
		<i class="<?php echo $instagram_class; ?>"></i>
		<span class="sr-only">Find us on Instagram</span></a>
	<?php endif; ?>
	<?php if ( $youtube_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $youtube_url; ?>">
		<i class="<?php echo $youtube_class; ?>"></i>
		<span class="sr-only">Follow us on YouTube</span></a>
	<?php endif; ?>
	<?php if ( $wordpress_url ) : ?>
	<a class="ga-event-link" target="_blank" href="<?php echo $wordpress_url; ?>">
		<i class="<?php echo $wordpress_class; ?>"></i>
		<span class="sr-only">Follow us on our WordPress Blog</span></a>
	<?php endif; ?>
</div>
<?php
	return ob_get_clean();
}


/**
 * Get Facebook Posts
 **/
function get_facebook_posts() {
	// Example: https://graph.facebook.com/v2.5/devosprogram/feed?access_token=[ACCESS_CODE]&limit=3&fields=link%2Cpicture%2Cmessage%2Ccreated_time
	$url = get_theme_mod_or_default( 'facebook_api_url' );
	ob_start();

	$opts = array(
		'http' => array(
			'timeout' => 15,
		)
	);

	$context = stream_context_create( $opts );

	$json = file_get_contents( $url, false, $context );
	$obj = json_decode( $json );
	ob_start();

	foreach ( $obj->data as $post ) {
		$message = 'View Post';
		if( !empty( $post->message ) )  {
			$message = $post->message;
		}
	?>
		<div class="row">
			<a href="<?php echo $post->link; ?>">
				<div class="col-xs-3">
					<img src="<?php echo $post->picture; ?>" width="100%">
				</div>
				<div class="col-xs-9">
					<h4><?php echo date_format( date_create( $post->created_time ), 'M d \a\t g:i A' ); ?></h4>
					<p><?php echo $post->message; ?></p>
				</div>
			</a>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<hr>
			</div>
		</div>
	<?php
	}

	return ob_get_clean();
}


/**
 * Display address information (from theme options)
 **/
function display_contact_address() {
	$address = get_theme_mod_or_default( 'organization_address' );
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
 * Display phone information (from theme options)
 **/
function display_phone($option) {
	$phone = get_theme_mod_or_default( $option );
	if ( !empty( $phone ) ) {
		ob_start();
	?>
	<a href="tel:<?php echo preg_replace("/[^0-9,.]/", "", $phone); ?>">
		<?php echo $phone; ?></a><?php
		return ob_get_clean();
	}
	return;
}


/**
 * Display email (from theme options)
 **/
function display_email($option) {
	$email = get_theme_mod_or_default( $option );
	if ( !empty( $email ) ) {
		ob_start();
	?>
	<a href="mailto:<?php echo $email; ?>">
		<?php echo $email; ?></a><?php
		return ob_get_clean();
	}
	return;
}


/**
 * Display footer feature image (from theme options)
 **/
function display_footer_feature_image() {
	$image = get_theme_mod_or_default( 'footer_feature_image' );
	if ( !empty( $image ) ) {
		ob_start();
	?>
	<img src="<?php echo $image; ?>">
	<?php
		return ob_get_clean();
	}
	return;
}


/**
 * Display footer feature cta (from theme options)
 **/
function display_footer_feature_cta() {
	$cta = get_theme_mod_or_default( 'footer_feature_cta' );
	if ( !empty( $cta ) ) {
		ob_start();
	?>
	<?php echo nl2br( $cta ); ?>
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
		get_theme_mod_or_default( 'home_feature_1' ),
		get_theme_mod_or_default( 'home_feature_2' ),
		get_theme_mod_or_default( 'home_feature_3' ),
		get_theme_mod_or_default( 'home_feature_4' ),
		get_theme_mod_or_default( 'home_feature_5' ),
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
 * Displays an alternate CTA image+link, if one is set in Theme Options.
 **/
function display_home_alt_cta() {
	$cta_isset  = get_theme_mod_or_default( 'enable_home_cta_alt' ) == 1 ? true : false;
	$cta_url    = get_theme_mod_or_default( 'home_cta_alt_url' );
	$cta_img_id = get_theme_mod_or_default( 'home_cta_alt_image' );

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
		echo display_site_social('', 'hidden-xs');
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

function append_centerpiece_metadata( $post ) {

	$post->image = wp_get_attachment_url( get_post_meta( $post->ID, 'centerpiece_image', TRUE ) );
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) );
	if( is_array( $thumbnail ) ) {
		$post->thumbnail = $thumbnail[0];
	}
	$post->cta_title = get_post_meta( $post->ID, 'centerpiece_cta_title', TRUE );
	$post->cta_content = get_post_meta( $post->ID, 'centerpiece_cta_content', TRUE );
	$post->cta_button_link = get_post_meta( $post->ID, 'centerpiece_cta_button_link', TRUE );
	$post->cta_button_text = get_post_meta( $post->ID, 'centerpiece_cta_button_text', TRUE );

	return $post;
}

function append_person_metadata( $post ) {
	$post->thumbnail           = get_the_post_thumbnail( $post->ID, 'thumbnail', array( 'class' => 'img-responsive img-rounded' ) );
	$post->hometown            = get_post_meta( $post->ID, 'person_hometown', TRUE );
	$post->undergrad_institute = get_post_meta( $post->ID, 'person_undergrad_insitution', TRUE );
	$post->undergrad_degree    = get_post_meta( $post->ID, 'person_undergrad_degree', TRUE );
	$post->postgrad_degree     = get_post_meta( $post->ID, 'person_postgrad_degree', TRUE );
	$post->internships         = get_post_meta( $post->ID, 'person_internships', TRUE );
	$post->outreach            = get_post_meta( $post->ID, 'person_outreach', TRUE );
	$post->career              = get_post_meta( $post->ID, 'person_career', TRUE );

	if ( ! $post->thumbnail ) {
		$post->thumbnail = '<img src="'. get_theme_mod_or_default( 'people_default_image', '' ) .'" height="150" width="150" class="img-responsive img-rounded">';
	}

	return $posts;
}

function display_home_centerpieces() {
	$date = date('m/d/Y');
	$args = array(
		'post_type' => 'centerpiece',
		'posts_per_page' => 3,
		'meta_query' => array(
			array(
				'key'     => 'centerpiece_expires',
				'value'   => $date,
				'compare' => '>='
			)
		)
	);

	$posts = get_posts( $args );

	if ( count( $posts ) > 0 ) {
		foreach( $posts as $post ) {
			append_centerpiece_metadata( $post );
		}
	} else {
		$post_id = get_theme_mod_or_default( 'home_page_default_centerpiece' );
		$post = get_post( $post_id );
		append_centerpiece_metadata( $post );
		$posts[] = $post;
	}

	$count = count( $posts );

	ob_start();

	?>

	<div id="centerpiece-carousel" class="centerpiece slide">
		<?php if ( $count > 1 ) : ?>
		<ol class="carousel-indicators">
		<?php foreach( $posts as $idx=>$post ) : ?>
			<li data-target="#centerpiece-carousel" data-slide-to="<?php echo $idx; ?>"<?php if ( $idx == 0 ) : ?> class="active"<?php endif; ?>>
				<img src="<?php echo $post->thumbnail; ?>" alt="">
			</li>
		<?php endforeach; ?>
		</ol>
		<?php endif; ?>
		<div class="carousel-inner" role="listbox">
		<?php foreach ( $posts as $idx=>$post ) : ?>
			<div class="item<?php echo ( $idx == 0 ) ? ' active' : ''; ?>">
				<img src="<?php echo $post->image; ?>" alt="">
				<div class="carousel-cta row">
					<h3><?php echo $post->cta_title; ?></h3>
					<p><?php echo $post->cta_content; ?></p>
					<a href="<?php echo $post->cta_button_link; ?>" class="btn btn-lg btn-cta btn-block">
						<?php echo $post->cta_button_text; ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>

	<?php

	return ob_get_clean();
}


function get_remote_menu_feed( ) {
	global $wp_customize;
	$customizing    = isset( $wp_customize );
	$feed_url       = get_theme_mod_or_default( 'parent_site_menu_url' ); // "http://localhost/business/wp-json/ucf-rest-menus/v1/menus/203";
	$transient_name = 'business_menu_json';
	$result         = get_transient( $transient_name );
	if ( empty( $result ) || $customizing ) {
		$response = wp_remote_get( $feed_url, array( 'timeout' => 15 ) );
		if ( is_array( $response ) ) {
			$result = json_decode( wp_remote_retrieve_body( $response ) );
		}
		else {
			$result = false;
		}
		if ( ! $customizing ) {
			set_transient( $transient_name, $result, (60 * 60 * 24) );
		}
	}
	return $result;
}

function get_nav_links() {
	$menu_items = get_remote_menu_feed()->items;
	$nav_links = "";

	// var_dump($menu_items);

	foreach( $menu_items as $index=>$item ) {
		if( $item->parent === 0 ) {
			if($index > 0 ) {
				$nav_links .= '</li></ul></li>';

			}
			$nav_links .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $item->title . '<span class="caret"></span></a><ul class="dropdown-menu">';
		} else {
			$nav_links .= '<li><a href="' . $item->url . '">' . $item->title . '</a></li>';
		}
	}

	return $nav_links;
}

?>
