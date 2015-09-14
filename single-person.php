<?php disallow_direct_load( 'single-person.php' ); ?>

<?php get_header(); the_post(); ?>

<?php
$post = attach_post_metadata_properties( $post );

$thumbnail       = Person::get_photo( $post );
if ( $post->person_cv ) {
	$post->person_cv_url = wp_get_attachment_url( intval( $post->person_cv ) );
	$post->person_cv_mime = mimetype_to_application( get_post_mime_type( intval( $post->person_cv ) ) );
}
$office_hours = Person::get_office_hours( $post );
$departments = wp_get_post_terms( $post->ID, 'departments' );
$media_offset = 'col-md-offset-1 ';
?>


<article id="article-<?php echo $post->post_name; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-4 col-xs-12 col-md-push-8 col-sm-push-8 person-contact-container">

				<h1 class="h3 person-title visible-xs">
					<?php echo $post->person_title_prefix; ?>
					<?php the_title(); ?><?php echo $post->person_title_suffix; ?>
				</h1>

				<?php if ( $post->person_jobtitle ): ?>
					<span class="h4 person-job-title visible-xs"><?php echo $post->person_jobtitle; ?></span>
				<?php endif; ?>

				<aside>

					<?php if ( $thumbnail ): ?>
						<img src="<?php echo $thumbnail; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" class="img-responsive person-photo">
					<?php endif; ?>

					<ul class="person-contact clearfix">

						<?php if ( $post->person_email ): ?>
							<li>
								<span class="person-contact-heading person-contact-heading-aligned">E-mail:</span>
								<a href="mailto:<?php echo $post->person_email; ?>" class="person-email person-contact-content-aligned">
									<?php echo $post->person_email; ?>
								</a>
							</li>
						<?php endif; ?>

						<?php if ( $post->person_phones ): ?>
							<li>
								<span class="person-contact-heading person-contact-heading-aligned">Phone:</span>
								<a href="tel:<?php echo $post->person_phones; ?>" class="person-tel person-contact-content-aligned">
									<?php echo $post->person_phones; ?>
								</a>
							</li>
						<?php endif; ?>

						<?php if ( !empty( $departments ) ): ?>
							<li>
								<span class="person-contact-heading person-contact-heading-aligned visible-sm">Department:</span>
								<span class="person-contact-heading person-contact-heading-aligned hidden-sm">Dept:</span>
								<ul class="person-department-list person-contact-content-aligned">
								<?php foreach ( $departments as $dept ): ?>
									<li>
										<?php
										$page_id = get_term_custom_meta( $dept->term_id, 'departments', 'department_links_to_page' );
										if ( $page_id ):
										?>
											<a href="<?php echo get_permalink( intval( $page_id ) ); ?>"><?php echo $dept->name; ?></a>
										<?php else: ?>
											<?php echo $dept->name; ?>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								</ul>
							</li>
						<?php endif; ?>

						<?php if ( $post->person_room ): ?>
							<li>
								<span class="person-contact-heading person-contact-heading-aligned">Office:</span>
								<?php if ( $post->person_room_url ): ?>
									<a class="person-contact-content-aligned" href="<?php echo $post->person_room_url; ?>">
										<?php echo $post->person_room; ?>
									</a>
								<?php else: ?>
									<span class="person-contact-content-aligned">
										<?php echo $post->person_room; ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endif; ?>

					</ul>

					<?php if ( !empty( $office_hours ) ): ?>
						<h2 class="h5 person-contact-heading office-hours-heading">Office Hours:</h2>
						<table class="office-hours">
							<tbody>
							<?php foreach ( $office_hours as $day => $hours ): ?>
								<tr>
									<th><?php echo $day; ?></th>
									<td><?php echo $hours; ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<?php if ( $post->person_class_schedule ): ?>
						<h2 class="h5 person-contact-heading course-schedule-heading">Course Schedule:</h2>

						<?php foreach ( $post->person_class_schedule as $class ): ?>
							<ul class="course-schedule">
								<li class="course-schedule-title"><?php echo $class['title']; ?></li>
								<li class="course-schedule-room"><?php echo $class['room']; ?></li>
								<li class="course-schedule-semester"><?php echo $class['semester']; ?></li>
							</ul>
						<?php endforeach; ?>
					<?php endif; ?>

				</aside>

			</div>

			<div class="col-md-7 col-md-offset-1 col-sm-8 col-xs-12 col-md-pull-3 col-sm-pull-4">

				<?php // START Biography ?>
				<h1 class="person-title hidden-xs">
					<?php echo $post->person_title_prefix; ?>
					<?php the_title(); ?><?php echo $post->person_title_suffix; ?>
				</h1>

				<div class="person-content">
					<?php if ( $post->person_jobtitle ): ?>
						<span class="h3 person-job-title hidden-xs"><?php echo $post->person_jobtitle; ?></span>
					<?php endif; ?>

					<h2 class="h4 person-subheading">Biography</h4>
					<?php the_content(); ?>
					<?php if ( $post->person_cv_url ): ?>
						<p><strong>View CV:</strong> <a class="<?php echo $post->person_cv_mime; ?>" href="<?php echo $post->person_cv_url; ?>">Download</a></p>
					<?php endif; ?>
				</div>
				<?php // END Biography ?>

				<?php // START News ?>
				<?php $output = do_shortcode('[publication-list publication_types="faculty-news" person="'.$post->post_name.'" display="excerpt" default="" posts_per_page="4"]');
					  if (!empty($output)): ?>
						<section class="person-news">
							<h2 class="h4 person-subheading">In the News</h2>
							<?php echo $output; ?>
						</section>
				<?php endif; ?>
				<?php // END News ?>

			</div>
		</div>

		<div class="row">

			<?php // START Research ?>
				<?php
					$output = do_shortcode('[publication-list publication_types="faculty-research" person="'.$post->post_name.'" display="excerpt" default="" posts_per_page="15"]');
					if (!empty($output)):
							$media_offset = '';
				?>
					<div class="col-md-offset-1 col-md-5">
						<section class="person-research">
							<h2 class="h4 person-subheading">Research and Publications</h2>
							<?php echo $output; ?>
						</section>
					</div>
				<?php endif; ?>
			<?php // END Research ?>

			<?php // START Media ?>
			<?php if ( $post->person_media ): ?>
				<div class="<?php echo $media_offset; ?> col-md-5">
					<section class="person-media">
						<h2 class="h4 person-subheading">Video and Media</h2>

						<?php foreach ( $post->person_media as $item ): ?>
							<article>
								<div class="person-media-embed embed-responsive embed-responsive-16by9">
								  <?php echo get_embed_html( $item['link'] ); ?>
								</div>

								<?php if( $item['title'] ): ?>
									<h3 class="h4 person-subheading person-media-title"><?php echo $item['title']; ?></h3>
								<?php endif; ?>

								<?php if( $item['date'] ): ?>
									<p class="person-media-content"><?php echo $item['date']; ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</section>
				</div>
			<?php endif; ?>
			<?php // END Media ?>

		</div>
	</div>
</article>


<?php get_footer(); ?>
