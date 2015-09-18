<?php
/**
 * Displays the search form
 **/
function sc_search_form() {
	ob_start();
	?>
	<div class="search">
		<?php get_search_form(); ?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('search_form', 'sc_search_form');


function sc_person_picture_list($atts) {
	$categories     = ($atts['categories']) ? $atts['categories'] : null;
	$org_group      = ($atts['org_group']) ? $atts['org_group'] : null;
	$limit          = ($atts['posts_per_page']) ? (intval($atts['posts_per_page'])) : -1;
	$join           = ($atts['join']) ? $atts['join'] : 'or';

	$term           = get_term_by( 'name', $org_group, 'org_groups' );
	$term_desc      = term_description( $term->term_id, 'org_groups' );

	$people         = sc_object_list(
						array(
							'post_type' => 'person',
							'posts_per_page' => $limit,
							'join' => $join,
							'categories' => $categories,
							'org_groups' => $term->slug,
							'orderby' => 'meta_value',
							'order' => 'ASC',
							'meta_key' => 'person_orderby_name',
						),
						array(
							'objects_only' => True,
						));

	ob_start();
	if ( $org_group ):
	?>
		<div class="row">
			<div class="col-md-12 person-picture-list-heading">
				<h2 class="org-group-title"><?php echo $org_group; ?></h2>
				<?php if ( $term_desc ): ?><div class="org-group-desc"><?php echo $term_desc; ?></div><?php endif; ?>
			</div>
		</div>
	<?php
	endif;

	$count = 0;
	if ($people):
		echo Person::objectsToHTML( $people, 'person-picture-list' );
	endif;

	return ob_get_clean();
}
add_shortcode( 'person-picture-list', 'sc_person_picture_list' );


/**
 * Post search
 *
 * @return string
 * @author Chris Conover
 **/
function sc_post_type_search($params=array(), $content='') {
	$defaults = array(
		'post_type_name'              => 'post',
		'taxonomy'                    => '',
		'taxonomy_term'               => '',
		'show_uncategorized'          => false,
		'uncategorized_term_name'     => 'Uncategorized',
		'show_empty_sections'         => false,
		'non_alpha_section_name'      => 'Other',
		'column_width'                => 'col-md-4',
		'column_count'                => '3',
		'order_by'                    => 'title',
		'order'                       => 'ASC',
		'meta_key'                    => null,
		'show_sorting'                => True,
		'show_sorting'                => True,
		'list_css_classes'            => '',
	);

	$params = ( $params === '' ) ? $defaults : array_merge( $defaults, $params );

	$params['show_empty_sections'] = filter_var( $params['show_empty_sections'], FILTER_VALIDATE_BOOLEAN );
	$params['column_count']        = is_numeric( $params['column_count'] ) ? (int) $params['column_count'] : $defaults['column_count'];
	$params['show_sorting']        = filter_var( $params['show_sorting'], FILTER_VALIDATE_BOOLEAN );
	$params['show_uncategorized']  = filter_var( $params['show_uncategorized'], FILTER_VALIDATE_BOOLEAN );

	// Resolve the post type class
	if(is_null($post_type_class = get_custom_post_type( $params['post_type_name'] ) ) ) {
		return '<p>Invalid post type.</p>';
	}
	$post_type = new $post_type_class;

	// Set default search text if the user didn't
	if( !isset( $params['default_search_text'] ) ) {
		$params['default_search_text'] = 'Find a '.$post_type->singular_name;
	}

	// Register if the search data with the JS PostTypeSearchDataManager
	// Format is array(post->ID=>terms) where terms include the post title
	// as well as all associated tag names
	$search_data = array();
	$search_data_posts = get_posts( array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => $params['order_by'],
		'order'       => $params['order'],
		'meta_key'    => $params['meta_key']
	) );

	// Set up an array of search data to be encoded into json.
	// Note: this will result in a 3-level deep object after being
	// encoded. Post IDs cannot be used as $search_data keys due to
	// json_encode, which requires all keys in numerical order to be
	// available in the array to be properly translated as a javascript
	// array during encoding.  http://stackoverflow.com/a/20912694
	foreach( $search_data_posts as $post ) {
		$post_search_data = array( strtolower( $post->post_title ) );
		foreach( wp_get_object_terms( $post->ID, 'post_tag' ) as $term ) {
			$post_search_data[] = strtolower( $term->name );
		}
		$search_data[] = array( $post->ID => $post_search_data );
	}
	?>
	<script type="text/javascript">
		if(typeof PostTypeSearchDataManager != 'undefined') {
			PostTypeSearchDataManager.register(new PostTypeSearchData(
				<?php echo json_encode( $params['column_count'] ); ?>,
				<?php echo json_encode( $params['column_width'] ); ?>,
				<?php echo json_encode( $search_data ); ?>
			));
		}
	</script>
	<?

	// Split up this post type's posts by term
	$by_term = array();
	$by_term_post_args = array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => $params['order_by'],
		'order'       => $params['order'],
		'meta_key'    => $params['meta_key']
	);
	if ( !empty( $params['taxonomy'] ) ) {
		$terms = array();

		if ( !empty( $params['taxonomy_term'] ) ) {
			$term = get_term_by( 'slug', $params['taxonomy_term'], $params['taxonomy'] );
			$terms = array( $term );
		} else {
			$terms = get_terms( $params['taxonomy'] );
		}

		$term_ids = array();

		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;

			$by_term_post_args['tax_query'] = array(
				array(
					'taxonomy' => $params['taxonomy'],
					'field'    => 'id',
					'terms'    => $term->term_id
				)
			);

			$posts = get_posts( $by_term_post_args );

			if ( count( $posts ) == 0 && $params['show_empty_sections'] ) {
				$by_term[$term->name] = array();
			}
			else {
				$by_term[$term->name] = $posts;
			}
		}

		// Add uncategorized items if parameter is set.
		if ( $params['show_uncategorized'] ) {
			$by_term_post_args = array(
				array(
					'taxonomy' => $params['taxonomy'],
					'field'    => 'id',
					'terms'    => $term_ids,
					'operator' => 'NOT IN'
				)
			);
			$uncategorized = get_posts( $by_term_post_args );

			if ( count( $uncategorized == 0 ) && $params['show_empty_sections'] ) {
				$by_term[$params['uncategorized_term_name']] = array();
			}
			else {
				$by_term[$params['uncategorized_term_name']] = $uncategorized;
			}
		}
	}
	else {
		$by_term[] = array();
		$posts = get_posts( $by_term_post_args );
		if ($posts) {
			$by_term[] = $posts;
		}
	}

	// Split up this post type's posts by the first alpha character
	$by_alpha = array();
	$by_alpha_post_args = array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => $params['order_by'],
		'order'       => 'ASC',
		'meta_key'    => $params['meta_key']
	);
	if ( !empty( $params['taxonomy'] ) ) {
		$terms = array();

		if ( !empty( $params['taxonomy_term'] ) ) {
			$term = get_term_by( 'slug', $params['taxonomy_term'], $params['taxonomy'] );
			$terms = array( $term->term_id );
		} else {
			$terms = get_terms( $params['taxonomy'], array( 'fields' => 'ids' ) );
		}

		$by_alpha_post_args['tax_query'] = array(
			array(
				'taxonomy' => $params['taxonomy'],
				'field'    => 'id',
				'terms'    => $terms
			)
		);

		// Add uncategorized items if parameter is set.
		if ( $params['show_uncategorized'] ) {
			$by_alpha_post_args['tax_query'][] = array(
				'taxonomy' => $params['taxonomy'],
				'field'    => 'id',
				'terms'    => $terms,
				'operator' => 'NOT IN'
			);
			$by_alpha_post_args['tax_query'][] = array(
				'relation' => 'AND'
			);
		}
	}

	$by_alpha_posts = get_posts( $by_alpha_post_args );

	foreach ( $by_alpha_posts as $post ) {
		$search_term = '';
		if ( $params['meta_key'] ) {
			$search_term = get_post_meta( $post->ID, $params['meta_key'], True );
		} else {
			$search_term = $post->post_title;
		}

		if ( preg_match( '/([a-zA-Z])/', $search_term, $matches ) == 1 ) {
			$by_alpha[strtolower($matches[1])][] = $post;
		} else {
			$by_alpha[$params['non_alpha_section_name']][] = $post;
		}
	}

	// Alphabetical lists should always be rendered to the screen,
	// even if empty.
	foreach ( range( 'a', 'z' ) as $letter ) {
		if ( !isset( $by_alpha[$letter] ) ) {
			$by_alpha[$letter] = array();
		}
	}

	ksort( $by_alpha );

	// Always force non_alpha_section to be at the end of $by_alpha
	if ( array_key_exists( $params['non_alpha_section_name'], $by_alpha ) ) {
		$non_alpha_section = $by_alpha[$params['non_alpha_section_name']];
		unset( $by_alpha[$params['non_alpha_section_name']] );
		$by_alpha[$params['non_alpha_section_name']] = $non_alpha_section;
	}

	$sections = array(
		'post-type-search-term'  => $by_term,
		'post-type-search-alpha' => $by_alpha,
	);

	ob_start();
	?>
	<div class="post-type-search">
		<div class="post-type-search-header">
			<form class="post-type-search-form" action="." method="get">
				<label class="sr-only">Search</label>
				<input type="text" class="form-control" placeholder="<?php echo $params['default_search_text']?>" />
			</form>
		</div>
		<?php if($params['show_sorting']): ?>
		<div class="post-type-search-sorting horizontal-scroll-container">
			<a class="horizontal-scroll-toggle left" href="#"><span class="fa fa-chevron-left"></span></a>
			<ul class="sorting-filter-list horizontal-scroll">
				<li class="horizontal-scroll-item">
					<a class="sorting-filter sorting-filter-all" href="#">All</a>
				</li>
				<?php foreach ( $by_alpha as $key => $val ) : ?>
					<li class="horizontal-scroll-item">
						<a class="sorting-filter" href="#<?php echo sanitize_title( $key ); ?>"><?php echo ucwords($key); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="horizontal-scroll-toggle right" href="#"><span class="fa fa-chevron-right"></span></a>
		</div>
		<?php endif; ?>
		<div class="post-type-search-results"></div>

	<?php
	foreach ( $sections as $id => $section ) :
		$hide                      = false;
		$show_section_titles       = true;
		$force_show_empty_sections = false;

		switch ( $id ) {
			case 'post-type-search-alpha':
				$hide = true;
				// Always display empty sections by alpha
				$force_show_empty_sections = true;
				break;
			case 'post-type-search-term':
				// Don't display section titles if no taxonomy is specified
				// or if a specific taxonomy term is passed.
				if ( empty( $params['taxonomy'] ) || !empty( $params['taxonomy_term'] ) ) {
					$show_section_titles = false;
				}
				break;
		}
		?>
		<div class="<?php echo $id; ?>"<?php if ( $hide ) echo ' style="display:none;"'; ?>>
			<?php
			foreach( $section as $section_title => $section_posts ) :
				if( count( $section_posts ) > 0 || $params['show_empty_sections'] || $force_show_empty_sections ) :
				?>
					<div class="post-type-search-section" id="<?php echo sanitize_title($section_title); ?>">
						<?php if ( $show_section_titles ) : ?>
							<h2><?php echo ucwords( esc_html( $section_title ) ); ?></h2>
						<?php endif; ?>
						<div class="row">
						<?php
						if ( count( $section_posts ) > 0 ) :
							$posts_per_column = ceil( count( $section_posts ) / $params['column_count'] );
							foreach ( range( 0, $params['column_count'] - 1 ) as $column_index ) :
								$start = $column_index * $posts_per_column;

								if ( count( $section_posts ) > $start ):
									$column_posts = array_slice( $section_posts, $start, $posts_per_column );
									?>
										<div class="<?php echo $params['column_width']; ?>">
											<?php echo $post_type->objectsToHTML( $column_posts, $params['list_css_classes'] ); ?>
										</div>
									<?php
								endif;
							endforeach;
						else:
						?>
							<div class="col-md-12">
								<p>No results found.</p>
							</div>
						<?php
						endif;
						?>
						</div>
					</div>
				<?php
				endif;
			endforeach;
			?>
		</div>
	<?php endforeach; ?>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode('post-type-search', 'sc_post_type_search');


/**
 * Wrap arbitrary text in .lead paragraph
 **/
function sc_lead($attr, $content='') {
	return '<p class="lead">' . $content . '</p>';
}
add_shortcode('lead', 'sc_lead');


/**
 * Generates a left-hand column.  Must be used immediately
 * before [right-col].
 **/
function sc_left_col($attr, $content='') {
	ob_start();
?>
	<div class="row">
		<div class="left-col">
			<?php echo apply_filters( 'the_content', $content ); ?>
		</div>
<?php
	return ob_get_clean();
}
add_shortcode('left-col', 'sc_left_col');


/**
 * Generates a right-hand column.  Must be used
 * immediately after [left-col].
 **/
function sc_right_col($attr, $content='') {
	ob_start();
?>
		<div class="right-col">
			<?php echo apply_filters( 'the_content', $content ); ?>
		</div>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode('right-col', 'sc_right_col');


/**
 * Create a Bootstrap button.
 **/
function sc_button_link($attr, $content='') {
	$classes    = $attr['classes'] ? $attr['classes'] : 'btn-primary';
	$url        = $attr['url'];
	$new_window = $attr['new_window'] && $attr['new_window'] == strtolower('true') ? $attr['new_window'] : false;
	$center     = $attr['center'] && $attr['center'] == strtolower('true') ? $attr['center'] : false;

	ob_start();
?>
	<?php if ($center): ?>
	<div class="text-center">
	<?php endif; ?>
		<a class="btn <?php echo $classes; ?>" href="<?php echo $url; ?>"<?php if ($new_window): ?> target="_blank"<?php endif; ?>>
			<?php echo do_shortcode( $content ); ?>
		</a>
	<?php if ( $center ) : ?>
	</div>
	<?php endif; ?>
<?php
	return ob_get_clean();
}
add_shortcode('button-link', 'sc_button_link');


/**
 * Create FontAwesome/Glyphicon icon markup.
 **/
function sc_icon($attr) {
	$classes = $attr['classes'];
	ob_start();
?>
	<span class="<?php echo $classes; ?>"></span>
<?php
	return ob_get_clean();
}
add_shortcode('icon', 'sc_icon');


/**
 * Displays a Twitter feed, using the Primary Widget ID provided
 * in Theme Options as a default.
 **/
function sc_twitter_timeline($attr) {
	$widget_id     = $attr['widget_id'] ? $attr['widget_id'] : get_theme_mod_or_default( 'twitter_primary_widget_id' );
	$url           = $attr['url'] ? $attr['url'] : get_theme_mod_or_default( 'twitter_url' );
	$fallback_text = $attr['fallback_text'] ? $attr['fallback_text'] : 'UCF College of Business Tweets';

	if (!$widget_id) { return; }

	ob_start();
?>
	<a data-widget-id="<?php echo $widget_id; ?>" href="<?php echo $url; ?>" class="twitter-timeline" data-theme="light">
		<?php echo $fallback_text; ?>
	</a>
<?php
	return ob_get_clean();
}
add_shortcode('twitter-timeline', 'sc_twitter_timeline');


/**
 * Displays a simple RSS feed by URL.
 **/
function sc_rss_feed( $attr ) {
	$url   = $attr['url'];
	$start = $attr['start'] ? intval( $attr['start'] ) : 0;
	$limit = $attr['limit'] ? intval( $attr['limit'] ) : 5;
	$extended = false;
	$images = false;
	$image_classes = $attr['image_classes'] ? $attr['image_classes'] : 'rss-feed-item-image';
	if ( isset( $attr['extended'] ) ) {
		$extended = true;
	}
	if ( isset( $attr['images'] ) ) {
		$images = true;
	}

	if ( !$url ) { return; }

	if ( $extended ) {
		return display_rss_feed_extended( $url, $start, $limit, $images, $image_classes );
	}

	return display_rss_feed( $url, $start, $limit );
}
add_shortcode( 'rss-feed', 'sc_rss_feed' );


/**
 * Displays News articles.
 **/
function sc_news_feed( $attr ) {
	$tag = $attr['tag'];
	$start = $attr['start'] ? intval( $attr['start'] ) : 0;
	$limit = $attr['limit'] ? intval( $attr['limit'] ) : 2;

	return display_news( null, true, $start, $limit, $tag );
}
add_shortcode( 'news-feed', 'sc_news_feed' );


/**
 * Displays Events.
 **/
function sc_events_feed( $attr ) {
	$url   = $attr['url'];
	$start = $attr['start'] ? intval( $attr['start'] ) : 0;
	$limit = $attr['limit'] ? intval( $attr['limit'] ) : 3;

	return display_events( null, $start, $limit, $url );
}
add_shortcode( 'events-feed', 'sc_events_feed' );


/**
 * Displays content inside a Bootstrap well.
 **/
function sc_well($attr, $content='') {
	$classes = $attr['classes'];
	ob_start();
?>
	<div class="well <?php echo $classes; ?>">
		<?php echo apply_filters( 'the_content', $content ); ?>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode('well', 'sc_well');


/**
 * Displays callout box (well) for degrees.
 **/
function sc_degree_callout( $attr ) {
	$taxonomy = 'undergraduate-programs';

	if ( isset( $attr[ 'degree_type' ] ) ) {
		$taxonomy = $attr[ 'degree_type' ];
	}
	$tax_prefix = str_replace( '-', '_', $taxonomy );

	$title = get_theme_mod_or_default( $tax_prefix.'_callout_title' );
	$desc  = get_theme_mod_or_default( $tax_prefix.'_callout_content' );

	ob_start();
?>
	<div class="well well-lg degree-callout">
		<h2 class="alt"><?php echo $title; ?></h2>
		<?php echo apply_filters( 'the_content', $desc ); ?>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode( 'degree-callout', sc_degree_callout );


/**
 * Overrides WordPress's default [gallery] shortcode with our
 * own, which creates a Bootstrap slideshow instead.
 *
 * Based on http://wordpress.stackexchange.com/a/145378
 **/
function sc_bootstrap_slideshow( $attr ) {
	global $post;
	static $instance = 0;
	$instance++;

	// Override Wordpress default gallery template to return nothing
	apply_filters( 'post_gallery', '' );

	// Set and sanitize various $attr's:
	if ( !empty( $attr['ids'] ) ) {
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] ) {
			unset( $attr['orderby'] );
		}
	}

	if ( isset( $attr['order'] ) && $attr['order'] === 'RAND' ) {
		$attr['orderby'] = 'none';
	}

	// Merge default attrs and user-provided attrs.
	// Assign all attrs to variables by key.
	extract( shortcode_atts(
		array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => '',
			'icontag'    => '',
			'captiontag' => '',
			'columns'    => 1,
			'size'       => 'slideshow-photo',
			'include'    => '',
			'link'       => '',
			'exclude'    => ''
		),
		$attr
	) );

	$id = intval( $id );

	$attachment_args = array(
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => $order,
		'orderby'        => $orderby
	);

	if ( !empty( $include ) ) {
		$_attachments = get_posts(
			array_merge(
				$attachment_args,
				array( 'include' => $include, )
			)
		);

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty( $exclude ) ) {
		$attachments = get_posts(
			array_merge(
				$attachment_args,
				array(
					'post_parent' => $id,
					'exclude' => $exclude,
				)
			)
		);
	} else {
		$attachments = get_posts(
			array_merge(
				$attachment_args,
				array( 'post_parent' => $id )
			)
		);
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	// Don't use Bootstrap slideshow for feeds (display list of
	// attachment links instead)
	if ( is_feed() ) {
		$output = '\n';
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $size, true ) . '\n';
		}
		return $output;
	}


	ob_start();

	$gallery_id = 'gallery-'.$instance;
?>

	<div class="carousel slide" id="<?php echo $gallery_id; ?>" data-ride="carousel">
		<ol class="carousel-indicators">
			<?php
			$indicatorcount = 0;

			foreach ( $attachments as $id => $attachment ):
				$css_class = '';
				if ( $indicatorcount == 1 ) {
					$css_class = 'active';
				}
			?>
				<li data-target="#<?php echo $gallery_id; ?>" data-slide-to="<?php echo $indicatorcount; ?>" class="active"></li>
			<?php
				$indicatorcount++;
			endforeach;
			?>
		</ol>
		<div class="carousel-inner" role="listbox">
			<?php
			$i = 0;

			// Begin counting slides to set the first one as the active class
			$slidecount = 1;
			foreach ( $attachments as $id => $attachment ):
				$link_url = trim( get_post_meta( $id, "_media_link", true ) );
				$image_src_url = wp_get_attachment_image_src( $id, $size );
				$image_src_url = $image_src_url[0];
				$excerpt = wptexturize( trim( $attachment->post_excerpt ) );

				$css_class = 'item';
				if ( $slidecount == 1 ) {
					$css_class .= ' active';
				}

				// Add a link to the image if a link exists.
				$is_empty_link_url = empty( $link_url );
			?>
				<div class="<?php echo $css_class; ?>">
					<?php echo ( !$is_empty_link_url ? '<a href="' . $link_url . '">' : '' ); ?>
					<img src="<?php echo $image_src_url; ?>" alt="<?php echo $excerpt; ?>" title="<?php echo $excerpt; ?>">
					<?php echo ( !$is_empty_link_url ? '</a>' : '' ); ?>
					<?php if ( $excerpt ): ?>
					<div class="carousel-caption">
						<?php echo $excerpt; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php
				$slidecount++;
			endforeach;
			?>
		</div>
		<a class="left carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="prev">
			<span class="icon-left fa fa-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="next">
		    <span class="icon-right fa fa-chevron-right" aria-hidden="true"></span>
		    <span class="sr-only">Next</span>
		</a>
	</div>

<?php
	return ob_get_clean();
}
remove_shortcode( 'gallery', 'gallery_shortcode' );
add_shortcode( 'gallery', 'sc_bootstrap_slideshow' );


/**
 * Returns a list of publications.
 **/
function publication_list( $attr ) {
	extract( shortcode_atts(
		array(
			'default'           => 'No publications found.',
			'display'           => '',
			'order'             => 'DESC',
			'orderby'           => 'date title',
			'person'            => '',
			'posts_per_page'    => -1,
			'publication_types' => ''
		),
		$attr
	) );

	if ( !empty( $person ) ) {
		$person_post = get_posts( array(
			'posts_per_page' => 1,
			'post_type' => 'person',
			'name' => $person
		) );
		if ( $person_post ) {
			$person_id = $person_post[0]->ID;
		}
	}
	$publication_types = trim( preg_replace( '/\s+/', ' ', $publication_types ) );
	$publication_types = explode( ' ', $publication_types );
	$publication_types = array_filter( $publication_types );

	$output = '';
	$args = array(
		'order' => $order,
		'orderby' => $orderby,
		'post_type' => 'publication',
		'posts_per_page' => $posts_per_page,
	);

	if ( !empty( $person_id ) ) {
		// publication_people is stored as a serialized array in the db.
		// Check for serialized ID matches, as both an int and str, since the
		// saved db values can vary.
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => 'publication_people',
				'value' => serialize( strval( $person_id ) ),
				'compare' => 'LIKE'
			),
			array(
				'key' => 'publication_people',
				'value' => serialize( $person_id ),
				'compare' => 'LIKE'
			)
		);
	}
	if ( !empty( $publication_types ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'publication_types',
				'field' => 'slug',
				'terms' => $publication_types,
				'operator' => 'AND'
			)
		);
	}

	$publications = get_posts( $args );

	if ( $publications ) {
		$p = new Publication;

		switch ( $display ) {
			case 'excerpt':
				$output .= $p->objectsToHTML( $publications, 'publication-excerpt-list' );
				break;
			default:
				$output .= $p->objectsToHTML( $publications, '' );
				break;
		}
	}
	else {
		$output = '<span class="publication-list">'. $default .'</span>';
	}

	return $output;
}
add_shortcode( 'publication-list', 'publication_list' );

function sc_centerpiece_carousel( $attr, $content='' ) {
	// Get the most recent centerpieces
	extract( shortcode_atts(
			array(
				'default'        => 'No centerpieces to display.',
				'posts_per_page' => 3
			), $attr
		)
	);

	$posts = get_posts(
		array(
			'posts_per_page' => $attr['posts_per_page'],
			'post_type' => 'centerpiece'
		)
	);

	foreach ( $posts as $post ) {
		append_centerpiece_metadata( $post );
	}

	ob_start();

	?>
		<div id="centerpiece-carousel" class="carousel slide centerpiece" data-rise="carousel">
			<ol class="carousel-indicators">
				<?php foreach ( $posts as $idx=>$post ) : ?>
				<li data-target="#centerpiece-carousel" data-slide-to="<?php echo $idx; ?>"<?php if ( $idx == 0 ) : ?> class="active"<?php endif; ?>>
					<img src="<?php echo $post->thumbnail; ?>" alt="">
				</li>
				<?php endforeach; ?>
			</ol>
			<div class="carousel-inner" role="listbox">
				<?php foreach ( $posts as $idx=>$post) : ?>
					<div class="item<?php if ( $idx == 0 ) : ?> active<?php endif; ?>">
						<img src="<?php echo $post->image; ?>" alt="">
						<div class="carousel-caption">
							<h2><?php echo $post->cta_title; ?></h2>
							<p><?php echo $post->cta_content; ?></p>
							<a href="<?php echo $post->cta_button_link; ?>" class="btn btn-lg btn-cta">
								<?php echo $post->cta_button_text; ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<a class="left carousel-control" href="#centerpiece-carousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#centerpiece-carousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'centerpiece-carousel', 'sc_centerpiece_carousel' );

function sc_spotlight( $attr, $content='' ) {
	extract( shortcode_atts( array(
			'id' => ''
		), $attr
	) );

	if ( $attr['id'] ) {

		$post = get_post( $attr['id'] );

		if ( $post ) {
			echo Spotlight::toHTML( $post );
		}
	}
}
add_shortcode( 'spotlight', 'sc_spotlight' );

function sc_publication( $attr, $content='' ) {
	extract( shortcode_atts( array(
			'id' => ''
		), $attr
	) );

	if ( $attr['id'] ) {

		$post = get_post( $attr['id'] );

		if ( $post ) {
			echo Publication::toHTML( $post );
		}
	}
}
add_shortcode( 'publication', 'sc_publication' );

?>
