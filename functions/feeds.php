<?php

function get_article_image($article){
	$image = $article->get_enclosure();
	if ($image){
		return ($image->get_thumbnail()) ? $image->get_thumbnail() : $image->get_link();
	}else{
		$matches = array();
		$found   = preg_match('/<img[^>]+src=[\'\"]([^\'\"]+)[\'\"][^>]+>/i',  $article->get_content(), $matches);
		if($found){
			return $matches[1];
		}
	}
	return null;
}


/**
 * Check to see if an external image exists (via curl.)
 * Alternative to getimagesize() that allows us to specify a timeout.
 * via http://stackoverflow.com/questions/1363925/check-whether-image-exists-on-remote-url
 *
 * @return bool
 **/
function check_remote_file($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url); // specify URL
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, FEED_FETCH_TIMEOUT); // specify timeout
	curl_setopt($ch, CURLOPT_NOBODY, 1); // don't download content
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch) !== FALSE) {
		return true;
	}
	return false;
}


/**
 * Handles fetching and processing of feeds.  Currently uses SimplePie to parse
 * retrieved feeds, and automatically handles caching of content fetches.
 * Multiple calls to the same feed url will not result in multiple parsings, per
 * request as they are stored in memory for later use.
 **/
class FeedManager{
	static private
		$feeds        = array(),
		$cache_length = 0xD2F0;

	/**
	 * Provided a URL, will return an array representing the feed item for that
	 * URL.  A feed item contains the content, url, simplepie object, and failure
	 * status for the URL passed.  Handles caching of content requests.
	 *
	 * @return array
	 * @author Jared Lang
	 **/
	static protected function __new_feed($url){
		$timer = Timer::start();
		require_once( ABSPATH.'/wp-includes/class-simplepie.php' );

		$simplepie = null;
		$failed    = False;
		$cache_key = 'feedmanager-'.md5($url);
		$content   = get_site_transient($cache_key);

		if ($content === False){
			$content = wp_remote_retrieve_body( wp_remote_get( $url ) );
			if ( empty( $content ) ) {
				$failed  = True;
				$content = null;
				error_log('FeedManager failed to fetch data using url of '.$url);
			}else{
				set_site_transient($cache_key, $content, self::$cache_length);
			}
		}

		if ($content){
			$simplepie = new SimplePie();
			$simplepie->set_raw_data($content);
			$simplepie->init();
			$simplepie->handle_content_type();

			if ($simplepie->error){
				error_log($simplepie->error);
				$simplepie = null;
				$failed    = True;
			}
		}else{
			$failed = True;
		}

		$elapsed = round($timer->elapsed() * 1000);
		debug("__new_feed: {$elapsed} milliseconds");
		return array(
			'content'   => $content,
			'url'       => $url,
			'simplepie' => $simplepie,
			'failed'    => $failed,
		);
	}


	/**
	 * Returns all the items for a given feed defined by URL
	 *
	 * @return array
	 * @author Jared Lang
	 **/
	static protected function __get_items($url){
		if (!array_key_exists($url, self::$feeds)){
			self::$feeds[$url] = self::__new_feed($url);
		}
		if (!self::$feeds[$url]['failed']){
			return self::$feeds[$url]['simplepie']->get_items();
		}else{
			return array();
		}

	}


	/**
	 * Retrieve the current cache expiration value.
	 *
	 * @return void
	 * @author Jared Lang
	 **/
	static public function get_cache_expiration(){
		return self::$cache_length;
	}


	/**
	 * Set the cache expiration length for all feeds from this manager.
	 *
	 * @return void
	 * @author Jared Lang
	 **/
	static public function set_cache_expiration($expire){
		if (is_number($expire)){
			self::$cache_length = (int)$expire;
		}
	}


	/**
	 * Returns all items from the feed defined by URL and limited by the start
	 * and limit arguments.
	 *
	 * @return array
	 * @author Jared Lang
	 **/
	static public function get_items($url, $start=null, $limit=null){
		if ($start === null){$start = 0;}

		$items = self::__get_items($url);
		$items = array_slice($items, $start, $limit);
		return $items;
	}
}


function display_events( $header=null, $start, $limit, $url=null ) {
	$options       = get_option( THEME_OPTIONS_NAME );
	$default_count = $options['events_max_items'] ? $options['events_max_items'] : 5;
	$start         = $start ? $start : 0;
	$limit         = $limit ? $limit : $default_count;
	$events        = get_events( $start, $limit, $url );

	ob_start();

	if ( count( $events ) ):
?>
	<?php if ( !empty( $header ) ): ?>
		<<?php echo $header; ?>>
			<a href="<?php echo $events[0]->get_feed()->get_link(); ?>">
				<?php echo $events[0]->get_feed()->get_title(); ?>
			</a>
		</<?php echo $header; ?>>
	<?php endif; ?>
		<ul class="events-list">
			<?php foreach ( $events as $item ): ?>
			<li class="item">
				<a class="event-link" href="<?php echo $item->get_link(); ?>">
					<?php
						$year  = $item->get_date( 'Y' );
						$month = $item->get_date( 'M' );
						$day   = $item->get_date( 'j' );
					?>
					<span class="date">
						<span class="day"><?php echo $day; ?></span>
						<span class="month-year"><?php echo $month; ?> <?php echo $year; ?></span>
					</span>
					<h3 class="title">
						<?php echo $item->get_title(); ?>
					</h3>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php else:?>
		<p>No events found.</p>
	<?php endif;?>
<?php
	return ob_get_clean();
}


function display_news( $header=null, $photos=false, $start=null, $limit=null, $tag=null ) {
	$default_count = get_theme_mod_or_default('news_max_items') ? get_theme_mod_or_default('news_max_items') : 3;
	$start         = $start ? $start : 0;
	$limit         = $limit ? $limit : $default_count;
	$news          = get_news( $start, $limit, $tag );

	ob_start();

	if ( count( $news ) ):
		if ( !empty( $header ) ):
		?>
			<<?php echo $header; ?>>
				<a href="<?php echo $news[0]->get_feed()->get_link(); ?>">
					<?php echo $news[0]->get_feed()->get_title(); ?>
				</a>
			</<?php echo $header; ?>>
		<?php endif; ?>

		<?php if ( $photos ): ?>
		<ul class="news-photo-list">
			<?php
			foreach ( $news as $key => $item ):
				$desc = $item->get_description();
				$desc = wp_trim_words( str_replace( '[...]', '', $desc ), 25 );
				$image = get_article_image($item);

				// get_article_image() ignores the 150x150 version of the image
				// provided. So, check for a (smaller) alternative here:
				if (!($image)) {
					$image = 'http://today.ucf.edu/widget/thumbnail.png';
				}
				else {
					if (preg_match('/\.jpeg$/i', $image)) {
						$end_of_str_length = 5;
					}
					else {
						// assume .jpeg is the only potential 5-character file extension being used
						$end_of_str_length = 4;
					}
					// Grab Today's 66x66px thumbnails if they're available
					$parsed_image = parse_url($image);
					$parsed_image['query'] = '';
					$image = $parsed_image['scheme'].'://'.$parsed_image['host'].$parsed_image['path'];
					$image_small = substr($image, 0, (strlen($image) - $end_of_str_length)).'-66x66'.substr($image, (strlen($image) - $end_of_str_length));
					$image = check_remote_file($image_small) !== false ? $image_small : $image;
				}
			?>
			<li>
				<div class="news-story">
					<a href="<?php echo $item->get_link(); ?>" class="ignore-external news-link ga-event-link" data-ga-category="News and Posts Links" data-ga-label="<?php echo $item->get_title(); ?>">
						<img class="news-photo hidden-sm alignleft" src="<?php echo $image; ?>" alt="Thumbnail for <?php echo $item->get_title(); ?>" title="Thumbnail for <?php echo $item->get_title(); ?>">
						<p class="news-date"><?php echo $item->get_date('M j Y'); ?></p>
						<h3 class="news-title">
								<?php echo $item->get_title(); ?>
						</h3>
					</a>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $feed_url = get_theme_mod_or_default( 'news_link' ); ?>
		<div class="clearfix">
			<a class="more-news pull-right" href="<?php echo $feed_url; ?>">
				Read More <span class="fa fa-chevron-right"></span>
			</a>
		</div>

		<?php else: ?>
		<ul class="news-list row">
			<?php foreach ( $news as $key => $item ): ?>
			<li class="news-story col-md-3 col-sm-4 col-xs-6">
				<h3 class="news-title">
					<a href="<?php echo $item->get_link(); ?>" class="ignore-external title ga-event-link" data-ga-category="News and Posts Links" data-ga-label="<?php echo $item->get_title(); ?>">
						<?php echo $item->get_title(); ?>
					</a>
				</h3>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	<?php else: ?>
		<p>No news articles found.</p>
	<?php endif; ?>
<?php
	return ob_get_clean();
}


function display_rss_feed( $url, $start=null, $limit=null ) {
	$rss = get_rss_feed( $url, $start, $limit );
	ob_start();

	if ( !empty( $rss ) ):
?>
		<ul class="rss-feed">
		<?php foreach ( $rss as $item ): ?>
			<li>
				<a href="<?php echo $item->get_link();?>"><?php echo $item->get_title(); ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
	<p>No results found.</p>
<?php
	endif;
	return ob_get_clean();
}

/*
 * Displays RSS feed with dates and excerpts included.
 */
function display_rss_feed_extended( $url, $start=null, $limit=null, $images=false, $image_classes=null ) {
	$rss = get_rss_feed( $url, $start, $limit );
	ob_start();

	if ( !empty( $rss )) :
		foreach ( $rss as $item ) :
?>
			<article class="rss-feed-item">
				<a href="<?php echo $item->get_link(); ?>">
					<h3><?php echo $item->get_title(); ?></h3>
				</a>
			<?php 
				if ( $images ) {
					if ( $enclosures = $item->get_enclosures() ) {
						foreach ($enclosures as $enclosure) {
							if ( $enclosure->get_length() ) : ?>
				<a href="<?php echo $item->get_link(); ?>" class="<?php echo $image_classes; ?>">
					<img width="150" height="150" src="<?php echo $enclosure->get_link(); ?>" alt="<?php echo $item->get_title(); ?>">
				</a>
			<?php
							endif; // end $enclosure->get_length()
						} // end foreach
					} // end if $enclosures
				} // end if $images

			?>
				<p><?php echo $item->get_description(); ?></p>
			</article>
<?php
		endforeach;
	endif;
	return ob_get_clean();
}

function get_events( $start=null, $limit=null, $url=null ) {
	$options = get_option( THEME_OPTIONS_NAME );
	$url     = $url ? $url : $options['events_url'];

	$events  = array_reverse( FeedManager::get_items( $url ) );
	$events  = array_slice( $events, $start, $limit );
	return $events;
}


function get_news( $start=null, $limit=null, $tag=null ) {
	$url     = get_theme_mod_or_default('news_url');
	if ( !$url ) {
		return;
	}

	if ( $tag ) {
		// Get the root today.ucf.edu url to make it easier to manipulate
		$url = parse_url( $url, PHP_URL_SCHEME ). '://' .parse_url( $url, PHP_URL_HOST );

		$tag = sanitize_title( $tag );
		$url .= '/topic/' . $tag;

		$url = $url . '/feed/';
	}

	$news = FeedManager::get_items( $url, $start, $limit );
	return $news;
}


function get_rss_feed( $url, $start=0, $limit=5 ) {
	if ($limit > 10) { $limit = 10; } // enforce max limit of 10
	$rss = FeedManager::get_items( $url, $start, $limit );
	return $rss;
}


?>
