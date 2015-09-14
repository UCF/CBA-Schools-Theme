<?php disallow_direct_load( 'search.php' ); ?>

<?php
$options = get_option( THEME_OPTIONS_NAME );
if ( $options['enable_google'] or $options['enable_google'] === null ):
	$domain  = $options['search_domain'];
	$limit   = (int)$options['search_per_page'];
	$start   = ( is_numeric( $_GET['start'] ) ) ? (int)$_GET['start'] : 0;
	$results = get_search_results( $_GET['s'], $start, $limit, $domain );
?>

	<?php get_header(); ?>

	<article id="article-search">
		<div class="container">
			<div class="row">
				<div class="col-md-12 article-content-wrap">
					<h1>Search Results</h1>

					<?php if ( count( $results['items'] ) ) : ?>
						<ul class="result-list">
							<?php foreach ( $results['items'] as $result ) : ?>
								<li class="item">
									<h2>
										<a class="<?php echo mimetype_to_application( ( $result['mime'] ) ? $result['mime'] : 'text/html' ) ?>" href="<?php echo $result['url']; ?>">
											<?php
											if ( $result['title'] ) {
												echo $result['title'];
											} else {
												echo substr( $result['url'], 0, 45 ).'...';
											}
											?>
										</a>
									</h2>
									<a href="<?php echo $result['url']; ?>" class="ignore-external url sans"><?php echo $result['url']; ?></a>
									<div class="snippet">
										<?php echo str_replace( '<br>', '', $result['snippet'] ); ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php if ( $start + $limit < $results['number'] ) : ?>
							<a class="button more" href="./?s=<?php echo $_GET['s']; ?>&amp;start=<?php echo $start + $limit; ?>">More Results</a>
						<?php endif; ?>

					<?php else: ?>
						<p>No results found for "<?php echo htmlentities( $_GET['s'] ); ?>".</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</article>

	<?php get_footer();?>

<?php else:?>

	<?php get_header(); the_post(); ?>

	<article id="article-search">
		<div class="container">
			<div class="row">
				<div class="col-md-12 article-content-wrap">
					<h1>Search Results</h1>
					<?php if ( have_posts() ): ?>
						<ul class="result-list">
						<?php while ( have_posts() ): the_post(); ?>
							<li class="item">
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a>
								<div class="snippet">
									<?php the_excerpt(); ?>
								</div>
							</li>
						<?php endwhile; ?>
						</ul>
					<?php else: ?>
						<p>No results found for "<?php echo htmlentities( $_GET['s'] ); ?>".</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</article>

	<?php get_footer(); ?>

<?php endif; ?>
