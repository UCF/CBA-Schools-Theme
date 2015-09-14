<?php global $shortcode_tags; ?>

<div id="theme-help" class="i-am-a-fancy-admin">
	<div class="container">
		<h1>Help</h1>

		<?php if ($updated): ?>
		<div class="updated fade"><p><strong><?php print __( 'Options saved' ); ?></strong></p></div>
		<?php endif; ?>

		<div class="sections">
			<ul>
				<li class="section"><a href="#posting">Posting</a></li>
				<li class="section"><a href="#shortcodes">Shortcodes</a></li>
			</ul>
		</div>
		<div class="fields">
			<ul>

				<li class="section" id="posting">
					<h2>Posting</h2>
					<p>Posting is fun, do it.</p>
				</li>

				<li class="section" id="shortcodes">
					<h2>Shortcodes</h2>

					<h3>Built-In WordPress Shortcodes</h3>
					<p>WordPress provides <a href="https://codex.wordpress.org/Shortcode#Built-In_and_Additional_Shortcodes" target="_blank">several built-in shortcodes</a>, mostly for displaying images, video and audio.  Those shortcodes should work as documented by WordPress unless stated otherwise below.</p>

					<h4>gallery</h4>
					<p>The gallery shortcode has been modified in this theme to display a Bootstrap carousel instead of the standard grid of photos WordPress provides.</p>

					<h4>embed</h4>
					<p>Works as usual, but <a href="http://getbootstrap.com/components/#responsive-embed" target="_blank">Bootstrap responsive wrappers</a> are added to iframes, objects, embeds, and videos.</p>
					<p>To add embedded content (such as YouTube or Vimeo videos), use the embed shortcode, or simply paste the url of the video on its own line within your post content.  <a href="https://codex.wordpress.org/Embed_Shortcode" target="_blank">See the WordPress documentation on embeds for more information.</a></p>

					<hr>

					<h3>Theme-Specific Shortcodes</h3>

					<h4>(post type)-list</h4>
					<p>Outputs a list of a given post type filtered by arbitrary taxonomies, for
					example a tag or category.  A default output can be added for when no objects
					matching the criteria are found.  Available attributes:</p>

					<table>
					<tr>
						<th scope="col">Post Type</th>
						<th scope="col">Shortcode Call</th>
						<th scope="col">Available Taxonomy Filters</th>
						<th scope="col">Additional Filters</th>
					</tr>

						<?php
							$custom_post_types = installed_custom_post_types();

							foreach ( $custom_post_types as $custom_post_type ) {
								if ( isset( $shortcode_tags[$custom_post_type->name.'-list'] ) ) {
						?>
					<tr>
						<td><?php print $custom_post_type->singular_name; ?></td>
						<td><?php print $custom_post_type->name; ?>-list</td>

						<td>
							<ul>
							<?php foreach ( $custom_post_type->taxonomies as $tax ) {
								switch ( $tax ) {
									case 'post_tag':
										$tax = 'tags';
										break;
									case 'category':
										$tax = 'categories';
										break;
								}
							?>
								<li style="list-style: disc; margin-left: 15px;"><?php print $tax; ?></li>
							</ul>
							<?php } ?>
						</td>
						<td>
							<ul>
								<?php
								// if more than 1 taxonomy is assigned to the post type, show 'join'
								// as being an available filter:
								if ( count( $custom_post_type->taxonomies ) > 1 ) :
								?>
									<li style="list-style: disc; margin-left: 15px;">join ('and', 'or')</li>
								<?php endif; ?>
									<li style="list-style: disc; margin-left: 15px;">limit (number)</li>
							</ul>
						</td>
					</tr>
						<?php }
						}	?>


				</table>

					<p>Examples:</p>
<pre><code># Output a maximum of 5 Documents tagged 'foo' or 'bar', with a default output.
[document-list tags="foo bar" posts_per_page=5]No Documents were found.[/document-list]

# Output all People categorized as 'foo'
[person-list categories="foo"]

# Output all People matching the terms in the custom taxonomy named 'org_groups'
[person-list org_groups="term list example"]

# Outputs all People found categorized as 'staff' and in the org_group 'small'.
[person-list posts_per_page=5 join="and" categories="staff" org_groups="small"]</code></pre>


				<?php if ( isset( $shortcode_tags['publication-list'] ) ) { ?>
				<h4>publication-list</h4>
				<p>Outputs a list of Publications.  Available attributes:</p>

				<table>
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Description</th>
						<th scope="col">Default Value</th>
						<th scope="col">Available Values</th>
					</tr>
					<tr>
						<td>default</td>
						<td>
							A fallback message for when no results for the given criteria are found.
						</td>
						<td>No publications found.</td>
						<td></td>
					</tr>
					<tr>
						<td>display</td>
						<td>
							Modifies how the publications list is displayed.  Set a value of "excerpt" to display publications with h3-sized titles and excerpts.  By default, this value is empty, and a basic unordered list of links is displayed.
						</td>
						<td></td>
						<td>
							<ul>
								<li style="list-style: disc; margin-left: 15px;">excerpt</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>order</td>
						<td>
							Determine if posts are ordered from ascending to descending, or vice-versa.
						</td>
						<td>DESC</td>
						<td>ASC (ascending), DESC (descending)</td>
					</tr>
					<tr>
						<td>orderby</td>
						<td>
							How to order results.  See <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">WP Query Orderby params</a> in the Wordpress Codex for more information.
						</td>
						<td>date title</td>
						<td>
							<ul>
								<li style="list-style: disc; margin-left: 15px;">none</li>
								<li style="list-style: disc; margin-left: 15px;">ID</li>
								<li style="list-style: disc; margin-left: 15px;">author</li>
								<li style="list-style: disc; margin-left: 15px;">title</li>
								<li style="list-style: disc; margin-left: 15px;">name</li>
								<li style="list-style: disc; margin-left: 15px;">date</li>
								<li style="list-style: disc; margin-left: 15px;">modified</li>
								<li style="list-style: disc; margin-left: 15px;">parent</li>
								<li style="list-style: disc; margin-left: 15px;">rand</li>
								<li style="list-style: disc; margin-left: 15px;">menu_order</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>person</td>
						<td>
							Filters posts by a related Person.  This value should be the Person post's <strong>slug</strong>.
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>posts_per_page</td>
						<td>
							The number of publications to return.  By default, all available results will be returned.
						</td>
						<td>-1</td>
						<td>(number)</td>
					</tr>
					<tr>
						<td>publication_types</td>
						<td>
							One or more publication_types to filter results by.  Separate multiple terms with spaces.  If multiple terms are listed, results must belong to <em>all</em> terms listed.  Terms should be listed by slug.
						</td>
						<td></td>
						<td>
							<ul>
							<?php
							$terms = get_terms( 'publication_types' );
							foreach ( $terms as $term ):
							?>
								<li style="list-style: disc; margin-left: 15px;"><?php echo $term->slug; ?></li>
							<?php endforeach; ?>
							</ul>
						</td>
					</tr>
				</table>

				<p>Examples:</p>
				<pre><code># Output a maximum of 5 Publications.
[publication-list posts_per_page=5]

# Output featured Publications that have a publication_type of "Faculty News", and display with large titles and excerpt.
[publication-list publication_types="faculty-news featured" display="excerpt"]

# Output the 5 most recent featured Publications with a publication_type of "Faculty Research", associated with "John Doe".
[publication-list publication_types="faculty-news featured" posts_per_page="5" person="john-doe"]</code></pre>

				<?php } ?>

				<?php
				if ( isset( $shortcode_tags['person-picture-list'] ) ) { ?>

				<h4>person-picture-list</h4>
				<p>Outputs a list of People with thumbnails, person names, and job titles.  If a person's description is available, a link to the person's profile will be outputted.  If a thumbnail for the person does not exist, a default 'No Photo Available' thumbnail will display.  Uses all filters available to the <strong>person-list</strong> shortcode.</p>

				<p>Example:</p>
<pre><code># Output all People (default to 5 columns.)
[person-picture-list]

# Output all People in 4 columns.
[person-picture-list row_size=4]

# Output People in org_group 'staff' in 6 columns.
[person-picture-list org_groups="staff" row_size=6]
</code></pre>

				<?php } ?>


				<?php if ( isset( $shortcode_tags['post-type-search'] ) ) { ?>
				<h4>post-type-search</h4>
				<p>Returns a list of posts of a given post type that are searchable through a generated search field.  Posts are searchable by post title and any associated tags.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>post_type_name</td>
							<td>The post type to retrieve posts for</td>
							<td>post</td>
							<td>
								<ul>
								<?php
									foreach ( $custom_post_types as $custom_post_type ) {
										print '<li style="list-style: disc; margin-left: 15px;">'.$custom_post_type->name.'</li>';
									}
								?>
								</ul>
							</td>
						</tr>
						<tr>
							<td>taxonomy</td>
							<td>A taxonomy by which posts can be organized</td>
							<td></td>
							<td>Depends on the post type chosen and its available taxonomies</td>
						</tr>
						<tr>
							<td>taxonomy_term</td>
							<td>A taxonomy term by which posts can be further filtered.  Requires 'taxonomy' to be set.</td>
							<td></td>
							<td>Depends on the post type chosen and its available taxonomies and terms</td>
						</tr>
						<tr>
							<td>show_empty_sections</td>
							<td>Determines whether or not empty taxonomy terms will be displayed within the results.  Note that alphabetical sections will always be rendered to the screen, even if they are empty.</td>
							<td>false</td>
							<td>true, false</td>
						</tr>
						<tr>
							<td>non_alpha_section_name</td>
							<td>Changes the name of the section in which non-alphabetical post results are stored in the alphabetical sort (posts that start with 0-9, etc.)</td>
							<td>Other</td>
							<td></td>
						</tr>
						<tr>
							<td>column_width</td>
							<td>Determines the width of the columns of results.  Intended for use with Bootstrap scaffolding (<a href="http://twitter.github.com/bootstrap/scaffolding.html">see here</a>), but will accept any CSS class name.</td>
							<td>col-md-4</td>
							<td></td>
						</tr>
						<tr>
							<td>column_count</td>
							<td>The number of columns that will be created with the set column_width.</td>
							<td>3</td>
							<td></td>
						</tr>
						<tr>
							<td>order_by</td>
							<td>How to order results by term.  Note that this does not affect alphabetical results.  See <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">WP Query Orderby params</a> in the Wordpress Codex for more information.</td>
							<td>title</td>
							<td>
								<ul>
									<li style="list-style: disc; margin-left: 15px;">none</li>
									<li style="list-style: disc; margin-left: 15px;">ID</li>
									<li style="list-style: disc; margin-left: 15px;">author</li>
									<li style="list-style: disc; margin-left: 15px;">title</li>
									<li style="list-style: disc; margin-left: 15px;">name</li>
									<li style="list-style: disc; margin-left: 15px;">date</li>
									<li style="list-style: disc; margin-left: 15px;">modified</li>
									<li style="list-style: disc; margin-left: 15px;">parent</li>
									<li style="list-style: disc; margin-left: 15px;">rand</li>
									<li style="list-style: disc; margin-left: 15px;">menu_order</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>order</td>
							<td>Determine if posts are ordered from ascending to descending, or vice-versa.</td>
							<td>ASC</td>
							<td>ASC (ascending), DESC (descending)</td>
						</tr>
						<tr>
							<td>show_sorting</td>
							<td>Whether or not to display the alphabetical filtering links.</td>
							<td>true</td>
							<td>true, false</td>
						</tr>
						<tr>
							<td>default_search_text</td>
							<td>Sets the post search field placeholder text.  Note that placeholders are not supported by older browsers without Javascript in IE8.</td>
							<td>Find a (post type name)</td>
							<td></td>
						</tr>
						<tr>
							<td>list_css_classes</td>
							<td>CSS Class(es) to pass to the post type's objectsToHTML() method which generates section lists.  If the post type supports it, the method will append the specified classes to the wrapper element for the list items.</td>
							<td></td>
							<td></td>
						</tr>
					</table>

					<p>Examples:</p>
<pre style="white-space: pre-line;"><code># Generate a Post search, organized by category, with empty sections visible.  Generates one column of results with CSS class .col-md-3.
[post-type-search column_width="col-md-3" column_count="1" show_empty_sections=true default_search_text="Find Something"]

# Generate a Person search, organized by Organizational Groups (that have People assigned to them.)
[post-type-search post_type_name="person" taxonomy="org_groups"]
</code></pre>
				<?php } ?>

					<h4>lead</h4>
					<p>Wraps arbitrary text in a paragraph with class 'lead' to apply special styles.  Does not support line breaks.</p>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code>[lead]Lorem ipsum dolor sit amet...[/lead]</code></pre>

					<h4>left-col, right-col</h4>
					<p>Creates a custom two-column layout.  <strong>left-col must always be immediately followed by right-col, and they must always be used in order to work correctly.</strong></p>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code>[left-col]
Left-hand column content here.  Supports HTML and other shortcodes.
[/left-col]
[right-col]
Right-hand column content here.  Supports HTML and other shortcodes.
[/right-col]</code></pre>

					<h4>button-link</h4>
					<p>Generates Bootstrap button link markup.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>classes</td>
							<td>CSS classes to add to the button.  Note that class 'btn' is already added for you.</td>
							<td>btn-primary</td>
							<td>See Bootstrap docs for available classes</td>
						</tr>
						<tr>
							<td>url</td>
							<td>URL for the button to link to</td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>new_window</td>
							<td>Whether or not the link should open in a new window.</td>
							<td>false</td>
							<td>true, false</td>
						</tr>
						<tr>
							<td>center</td>
							<td>Whether or not to center the button horizontally.</td>
							<td>false</td>
							<td>true, false</td>
						</tr>
					</table>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code># Creates a large green (success) centered button.
[button-link classes="btn-success btn-lg" url="#" center="true"]</code></pre>

					<h4>icon</h4>
					<p>Generates an empty span element that FontAwesome and Glyphicon icon classes can be passed to.</p>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code>[icon classes="fa fa-star"]</code></pre>

					<h4>twitter-timeline</h4>
					<p>Displays a Javascript-based Twitter timeline.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>widget_id</td>
							<td>ID of the Twitter timeline widget to use.  See <a href="https://dev.twitter.com/web/embedded-timelines" target="_blank">Twitter's docs</a> for more information.</td>
							<td>(Primary Twitter Widget ID set in Theme Options)</td>
							<td></td>
						</tr>
						<tr>
							<td>url</td>
							<td>URL for the widget's fallback link</td>
							<td>(Twitter URL set in Theme Options)</td>
							<td></td>
						</tr>
						<tr>
							<td>fallback_text</td>
							<td>Text to display for the widget's fallback link</td>
							<td>UCF College of Business Tweets</td>
							<td></td>
						</tr>
					</table>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code>[twitter-timeline]</code></pre>

					<h4>rss-feed</h4>
					<p>Displays a simple list of links from an RSS feed.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>url</td>
							<td>URL for the widget's fallback link</td>
							<td>(Twitter URL set in Theme Options)</td>
							<td></td>
						</tr>
						<tr>
							<td>start</td>
							<td>Starting position of posts to fetch (from top of results list).</td>
							<td>0</td>
							<td></td>
						</tr>
						<tr>
							<td>limit</td>
							<td>Maximum number of results to display.</td>
							<td>5</td>
							<td>1-10</td>
						</tr>
					</table>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code># Display a list of blog posts from the Office of Professional Development's blog.
[rss-feed url="http://careerpros.wordpress.com/feed/"]</code></pre>

					<h4>news-feed</h4>
					<p>Displays a list of UCF Today stories with thumbnails and excerpts, using the Today feed URL defined in Theme Options.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>tag</td>
							<td>A tag by which posts should be filtered.  Note that if the Today URL specified in Theme Options refers to a specific section (i.e. '.../section/business'), that section will be ignored (posts are *only* fetched by tag).</td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>start</td>
							<td>Starting position of posts to fetch (from top of results list).</td>
							<td>0</td>
							<td></td>
						</tr>
						<tr>
							<td>limit</td>
							<td>Maximum number of results to display.</td>
							<td>2</td>
							<td>1-8</td>
						</tr>
					</table>

					<h4>events-feed</h4>
					<p>Displays a list of events from events.ucf.edu, using the Events feed URL defined in Theme Options.  Available attributes:</p>

					<table>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Default Value</th>
							<th>Available Values</th>
						</tr>
						<tr>
							<td>url</td>
							<td>The URL from which events should be fetched.  If specified, will override the default Events feed url specified in Theme Options.  <strong>Make sure URLs end in "/feed.rss"</strong>.</td>
							<td>(Events feed URL set in Theme Options)</td>
							<td></td>
						</tr>
						<tr>
							<td>start</td>
							<td>Starting position of posts to fetch (from top of results list).</td>
							<td>0</td>
							<td></td>
						</tr>
						<tr>
							<td>limit</td>
							<td>Maximum number of results to display.</td>
							<td>2</td>
							<td>1-5</td>
						</tr>
					</table>

					<h4>well</h4>
					<p>Wraps content inside of a Bootstrap well.  Pass in extra classes to modify the well.</p>

					<p>Example:</p>
					<pre style="white-space: pre-line;"><code>[well classes="well-lg"]Large well content here...[/well]</code></pre>

				</li>
			</ul>
		</div>
	</div>
</div>
