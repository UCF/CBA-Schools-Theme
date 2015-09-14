<?php
ob_implicit_flush(true);
ob_end_flush();
ini_set('max_execution_time', 180);

$args = array(
	'posts_per_page' => -1,
	'post_type' => 'person',
);

print '<p>Starting...</p>';

print '<p>Creating publication types...</p>';

$terms = get_terms('publication_types', array( 'fields' => 'ids', 'hide_empty' => false ) );

foreach( $terms as $value ) {
	wp_delete_term( $value, 'publication_types' );
	clean_term_cache( $value, 'publication_types', true );
}

wp_cache_flush();

wp_insert_term(
	'Faculty News',
	'publication_types',
	array(
		'slug' => 'faculty-news'
	)
);

print '<p>Created Faculty News publication type.</p>';

wp_insert_term(
	'Faculty Research',
	'publication_types',
	array(
		'slug' => 'faculty-research'
	)
);

print '<p>Created Faculty Research publication type.</p>';

wp_insert_term(
	'Featured',
	'publication_types',
	array(
		'slug' => 'featured'
	)
);

print '<p>Created Featured publication type.</p>';

$people = get_posts($args);

$publications = array();
$created_count = 0;
$update_count = 0;

$existing_posts_array = get_posts(array(
	'post_type' => 'publication',
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'fields' => 'ids'
));

foreach ($existing_posts_array as $key=>$val) {
	// Give us data that is easier to work with.
	$existing_posts_array[intval($val)] = intval($val);
	unset($existing_posts_array[$key]);
}


// Add existing Person News/Research article meta fields to $publications

foreach ($people as $person) {
	$news_articles     = get_post_meta( $person->ID, 'person_news', true );
	$research_articles = get_post_meta( $person->ID, 'person_research', true );

	if ($news_articles) {
		foreach ($news_articles as $article) {
			$post = array(
				'post_data' => array(
					'post_title'    => $article['title'],
					'post_excerpt'  => $article['summary'],
					'post_status'   => 'publish',
					'post_date'     => get_the_date('Y-m-d H:i:s', $person->ID),
					'post_author'   => 1,
					'post_type'     => 'publication'
				),
				'post_meta' => array(
					'publication_people' => array($person->ID),
					'_links_to' => $article['link'],
					'_links_to_target' => '_blank',
				),
				'post_terms' => array(
					'publication_types' => 'Faculty News'
				),
			);

			$publications[] = $post;
		}
	}

	if ($research_articles) {
		foreach($research_articles as $article) {
			$post = array(
				'post_data' => array(
					'post_title'    => $article['title'],
					'post_excerpt'  => $article['summary'],
					'post_status'   => 'publish',
					'post_date'     => get_the_date('Y-m-d H:i:s', $person->ID),
					'post_author'   => 1,
					'post_type'     => 'publication'
				),
				'post_meta' => array(
					'publication_people' => array($person->ID),
					'_links_to' => $article['link'],
					'_links_to_target' => '_blank',
				),
				'post_terms' => array(
					'publication_types' => 'Faculty Research'
				),
			);

			$publications[] = $post;
		}
	}
}


// Add existing Faculty News posts to $publications
// TODO can this be combined with Faculty Research post fetching below?

$query_args = array(
	'posts_per_page' => -1,
	'category_name' => 'Faculty News',
);

$posts = get_posts($query_args);

foreach ($posts as $post) {
	$faculty_members = array();
	foreach($people as $person) {
		$match = preg_match('/'.$person->post_title.'/', $post->post_content);
		if ($match == 1) {
			$faculty_members[] = $person->ID;
		}
	}

	$post = array(
		'post_data' => array(
			'post_title'    => $post->post_title,
			'post_content'  => $post->post_content,
			'post_excerpt'  => $post->post_excerpt,
			'post_status'   => 'publish',
			'post_date'     => get_the_date('Y-m-d H:i:s', $post->ID),
			'post_author'   => 1,
			'post_type'     => 'publication'
		),
		'post_meta' => array(
			'publication_people' => $faculty_members
		),
		'post_terms' => array(
			'publication_types' => array(
				'Faculty News',
				'Featured'
			)
		),
	);

	$publications[] = $post;
}


// Add existing Faculty Research posts to $publications

$query_args = array(
	'post_per_page' => -1,
	'category_name' => 'Faculty Research'
);

$posts = get_posts($query_args);

foreach ($posts as $post) {
	$faculty_members = array();
	foreach($people as $person) {
		$match = preg_match('/'.$person->post_title.'/', $post->post_content);
		if ($match == 1) {
			$faculty_members[] = $person->ID;
		}
	}

	$post = array(
		'post_data' => array(
			'post_title'    => $post->post_title,
			'post_content'  => $post->post_content,
			'post_excerpt'  => $post->post_excerpt,
			'post_status'   => 'publish',
			'post_date'     => get_the_date('Y-m-d H:i:s', $post->ID),
			'post_author'   => 1,
			'post_type'     => 'publication'
		),
		'post_meta' => array(
			'publication_people' => $faculty_members
		),
		'post_terms' => array(
			'publication_types' => array(
				'Faculty Research',
				'Featured'
			)
		),
	);

	$publications[] = $post;
}


// Save/update publications

foreach ($publications as $publication) {
	print '<dl>';
	if (empty($publication['post_data'])) {
		print '<dt>Publication is empty. Continuing...</dt>';
		continue;
	}
	$post_data = $publication['post_data'];
	$post_meta = $publication['post_meta'];
	$post_terms = $publication['post_terms'];
	$post_id = null;

	$existing_post = get_posts(
		array(
			'name' => sanitize_title($post_data['post_title']),
			'post_type' => $post_data['post_type'],
			'post_content' => $post_data['post_content'],
			'posts_per_page' => 1
		)
	);

	$existing_post = empty($existing_post) ? false : $existing_post[0];

	if ($existing_post !== false) {
		$post_id = $existing_post->ID;
		$post_data['ID'] = $post_id;
		wp_update_post($post_data);
		unset($existing_posts_array[$post_data['ID']]);

		print '<dt><Strong>Updated</strong> content of existing post '.$post_data['post_title'].' with ID '.$post_data['ID'].'.</dt>';
		$updated_count++;
	} else {
		$post_id = wp_insert_post($publication['post_data']);

		print '<dt><strong>Saved new post</strong> '.$post_data['post_title'].'.</dt>';
		$created_count++;
	}

	if (is_array($post_meta)) {
		foreach($post_meta as $meta_key=>$meta_val) {
			$updated = update_post_meta($post_id, $meta_key, $meta_val);

			if ($update == true) {
				if ($meta_val) {
					print '<dd>Updated post meta field '.$meta_key.' with value '.$meta_val.'.</dd>';
				}
				else {
					print '<dd>Post meta field '.$meta_key.' was set to an empty value.</dd>';
				}
			} else if (is_numeric($updated) && $updated > 1) {
				print '<dd>Meta with ID '.$updated.' does not exist.</dd>';
			} else if ($updated == false) {
				if ($meta_val) {
					print '<dd>Post meta field '.$meta_key.' with value '.$meta_val.' left unchanged.</dd>';
				}
				else {
					print '<dd>Post meta field '.$meta_key.' with empty value left unchanged.</dd>';
				}
			}
		}
	}

	if (is_array($post_terms)) {
		$term_ids = array();
		foreach ($post_terms as $tax=>$term) {
			if (is_array($term)) {
				foreach($term as $t) {
					$existing_term = term_exists($t, $tax);
					if (!empty($existing_term) && is_array($existing_term)) {
						$term_id = $existing_term['term_id'];
						$term_ids[] = $term_id;
					}
				}
			}
			else if (!empty($term)) {
				// Check for existing. Make a new term if necessary.
				// Return a term_id.
				$existing_term = term_exists($term, $tax);
				if (!empty($existing_term) && is_array($existing_term)) {
					$term_id = $existing_term['term_id'];
					$term_ids[] = $term_id;
				}
			}
			else {
				$term_id = NULL;
				$term_ids[] = $term_id;
			}
		}
		// Actually set the term for the post. Unset existing term if $term_id is null.
		if (is_array($term_ids)) {
			wp_set_post_terms($post_id, $term_ids, 'publication_types'); // replaces existing terms
		}
		else {
			wp_delete_object_term_relationships($post_id, 'publication_types');
		}
	}
	// Done.
	print '<dd>Finished processing post '.$post_data['post_title'].'.</dd>';
	print '</dl>';
}

print '<p><strong>Created '.$created_count.' posts.</strong></p>';
print '<p><strong>Update '.$updated_count.' posts.</strong></p>';

?>
