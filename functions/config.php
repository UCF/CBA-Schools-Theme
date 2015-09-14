<?php

/**
 * Responsible for running code that needs to be executed as wordpress is
 * initializing.  Good place to register scripts, stylesheets, theme elements,
 * etc.
 *
 * @return void
 * @author Jared Lang
 **/
function __init__(){
	add_theme_support('menus');
	add_theme_support('post-thumbnails');

	// Custom Image Sizes
	add_image_size('homepagefeature-photo', 1200, 640, True);
	add_image_size('person-thumb', 170, 170, True);
	add_image_size('spotlight-thumb', 195, 195, True);
	add_image_size('slideshow-photo', 1140, 641);

	// Custom Menus
	register_nav_menu('header-menu', __('Header Menu'));
	register_nav_menu('footer-menu-1', __('Footer Menu 1'));
	register_nav_menu('footer-menu-2', __('Footer Menu 2'));

	// Custom Sidebars
	register_sidebar(array(
		'name'          => __('Sidebar'),
		'id'            => 'sidebar',
		'description'   => 'Sidebar found on two column page templates and search pages',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	));
	register_sidebar(array(
		'name' => __('Footer - Column One'),
		'id' => 'footer-one',
		'description' => 'Far left column in footer on the bottom of pages. If left empty, this column will contain a UCF logo and your organization\'s address.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	));
	register_sidebar(array(
		'name' => __('Footer - Column Two'),
		'id' => 'footer-two',
		'description' => 'Second column from the left in footer, on the bottom of pages.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	));
	register_sidebar(array(
		'name' => __('Footer - Column Three'),
		'id' => 'footer-three',
		'description' => 'Third column from the left in footer, on the bottom of pages.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	));
	register_sidebar(array(
		'name' => __('Footer - Column Four'),
		'id' => 'footer-four',
		'description' => 'Far right in footer on the bottom of pages. If left empty, this column will contain social media icons.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	));
	foreach(Config::$styles as $style){Config::add_css($style);}
	foreach(Config::$scripts as $script){Config::add_script($script);}

	global $timer;
	$timer = Timer::start();

	wp_deregister_script('l10n');
	set_defaults_for_options();
}
add_action('after_setup_theme', '__init__');



# Set theme constants
#define('DEBUG', True);                  # Always on
#define('DEBUG', False);                 # Always off
define('DEBUG', isset($_GET['debug'])); # Enable via get parameter
define('THEME_URL', get_bloginfo('stylesheet_directory'));
define('THEME_ADMIN_URL', get_admin_url());
define('THEME_DIR', get_stylesheet_directory());
define('THEME_INCLUDES_DIR', THEME_DIR.'/includes');
define('THEME_STATIC_URL', THEME_URL.'/static');
define('THEME_COMPONENTS_URL', THEME_STATIC_URL.'/bower_components');
define('THEME_IMG_URL', THEME_STATIC_URL.'/img');
define('THEME_JS_URL', THEME_STATIC_URL.'/js');
define('THEME_JS_ADMIN_URL', THEME_STATIC_URL.'/js/admin');
define('THEME_CSS_URL', THEME_STATIC_URL.'/css');
define('THEME_CSS_ADMIN_URL', THEME_STATIC_URL.'/css/admin');
define('THEME_OPTIONS_GROUP', 'settings');
define('THEME_OPTIONS_NAME', 'theme');
define('THEME_OPTIONS_PAGE_TITLE', 'Theme Options');
define('THEME_JOBS_DIR', THEME_DIR.'/jobs');

$theme_options = get_option(THEME_OPTIONS_NAME);
define('GA_ACCOUNT', $theme_options['ga_account']);
define('CB_UID', $theme_options['cb_uid']);
define('CB_DOMAIN', $theme_options['cb_domain']);

define( 'FEED_FETCH_TIMEOUT', 8 ); // seconds


/**
 * Set config values including meta tags, registered custom post types, styles,
 * scripts, and any other statically defined assets that belong in the Config
 * object.
 **/
Config::$custom_post_types = array(
	'Degree',
	'HomePageFeature',
	'Page',
	'Person',
	'Post',
	'Spotlight',
	'Publication'
);

Config::$custom_taxonomies = array(
	'OrganizationalGroups',
	'EmployeeTypes',
	'Departments',
	'DegreeTypes',
	'PublicationTypes'
);

Config::$body_classes = array();


$homepagefeature = new HomePageFeature();
/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions/base.php
 **/
Config::$theme_settings = array(
	'Analytics' => array(
		new TextField(array(
			'name'        => 'Google WebMaster Verification',
			'id'          => THEME_OPTIONS_NAME.'[gw_verify]',
			'description' => 'Example: <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
			'default'     => null,
			'value'       => $theme_options['gw_verify'],
		)),
		new TextField(array(
			'name'        => 'Google Analytics Account',
			'id'          => THEME_OPTIONS_NAME.'[ga_account]',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'default'     => null,
			'value'       => $theme_options['ga_account'],
		)),
	),
	'Events' => array(
		new SelectField(array(
			'name'        => 'Events Max Items',
			'id'          => THEME_OPTIONS_NAME.'[events_max_items]',
			'description' => 'Maximum number of events to display whenever outputting event information.',
			'value'       => $theme_options['events_max_items'],
			'default'     => 4,
			'choices'     => array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
			),
		)),
		new TextField(array(
			'name'        => 'Events RSS Feed URL',
			'id'          => THEME_OPTIONS_NAME.'[events_url]',
			'description' => 'URL for the calendar feed you wish to use from events.ucf.edu. We recommend using the "Upcoming" feed for your organization\'s calendar.<br>Example: <em>http://events.ucf.edu/calendar/123/my-calendar/upcoming/feed.rss</em>',
			'value'       => $theme_options['events_url'],
			'default'     => 'http://events.ucf.edu/calendar/73/ucf-college-of-business-administration/upcoming/feed.rss',
		)),
	),
	'News' => array(
		new SelectField(array(
			'name'        => 'News Max Items',
			'id'          => THEME_OPTIONS_NAME.'[news_max_items]',
			'description' => 'Maximum number of articles to display when outputting news information.',
			'value'       => $theme_options['news_max_items'],
			'default'     => 8,
			'choices'     => array(
				'4' => 4,
				'8' => 8,
			),
		)),
		new TextField(array(
			'name'        => 'News RSS Feed',
			'id'          => THEME_OPTIONS_NAME.'[news_url]',
			'description' => 'URL for the UCF Today RSS feed.<br>Example: <em>http://today.ucf.edu/feed/</em>',
			'value'       => $theme_options['news_url'],
			'default'     => 'http://today.ucf.edu/section/business/feed/',
		)),
	),
	'Search' => array(
		new RadioField(array(
			'name'        => 'Enable Google Search',
			'id'          => THEME_OPTIONS_NAME.'[enable_google]',
			'description' => 'Enable to use the google search appliance to power the search functionality.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_google'],
	    )),
		new TextField(array(
			'name'        => 'Search Domain',
			'id'          => THEME_OPTIONS_NAME.'[search_domain]',
			'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example: <em>some.domain.com</em>',
			'default'     => null,
			'value'       => $theme_options['search_domain'],
		)),
		new TextField(array(
			'name'        => 'Search Results Per Page',
			'id'          => THEME_OPTIONS_NAME.'[search_per_page]',
			'description' => 'Number of search results to show per page of results',
			'default'     => 10,
			'value'       => $theme_options['search_per_page'],
		)),
	),
	'Contact Information' => array(
		new TextField(array(
			'name'        => 'Contact Email',
			'id'          => THEME_OPTIONS_NAME.'[site_contact]',
			'description' => 'Contact email address that visitors to your site can use to contact you.',
			'value'       => $theme_options['site_contact'],
		)),
		new TextField(array(
			'name'        => 'Organization Name',
			'id'          => THEME_OPTIONS_NAME.'[organization_name]',
			'description' => 'Your organization\'s name',
			'value'       => $theme_options['organization_name'],
			'default'     => 'College of Business Administration',
		)),
		new TextareaField(array(
			'name'        => 'Organization Address',
			'id'          => THEME_OPTIONS_NAME.'[organization_address]',
			'description' => 'The address of your organization.',
			'value'       => $theme_options['organization_address'],
			'default'	  => 'University of Central Florida
College of Business Administration
4000 Central Florida Blvd.
P.O. Box 161991
Orlando, FL 32816-1991',
		)),
	),
	'Social' => array(
		new RadioField(array(
			'name'        => 'Enable OpenGraph',
			'id'          => THEME_OPTIONS_NAME.'[enable_og]',
			'description' => 'Turn on the opengraph meta information used by Facebook.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_og'],
	    )),
		new TextField(array(
			'name'        => 'Facebook Admins',
			'id'          => THEME_OPTIONS_NAME.'[fb_admins]',
			'description' => 'Comma separated facebook usernames or user ids of those responsible for administrating any facebook pages created from pages on this site. Example: <em>592952074, abe.lincoln</em>',
			'default'     => null,
			'value'       => $theme_options['fb_admins'],
		)),
		new TextField(array(
			'name'        => 'Facebook URL',
			'id'          => THEME_OPTIONS_NAME.'[facebook_url]',
			'description' => 'URL to the facebook page you would like to direct visitors to.  Example: <em>https://www.facebook.com/CSBrisketBus</em>',
			'default'     => 'http://www.facebook.com/ucfcba',
			'value'       => $theme_options['facebook_url'],
		)),
		new TextField(array(
			'name'        => 'Twitter URL',
			'id'          => THEME_OPTIONS_NAME.'[twitter_url]',
			'description' => 'URL to the twitter user account you would like to direct visitors to.  Example: <em>http://twitter.com/csbrisketbus</em>',
			'default'     => 'https://www.twitter.com/ucfbusiness',
			'value'       => $theme_options['twitter_url'],
		)),
		new TextField(array(
			'name'        => 'Twitter Primary Widget ID',
			'id'          => THEME_OPTIONS_NAME.'[twitter_primary_widget_id]',
			'description' => 'The widget ID for the default Twitter timeline that displays when using the [twitter-timeline] shortcode.',
			'value'       => $theme_options['twitter_primary_widget_id'],
		)),
		new TextField(array(
			'name'        => 'Google+ URL',
			'id'          => THEME_OPTIONS_NAME.'[googleplus_url]',
			'description' => 'URL to the Google+ user account you would like to direct visitors to.  Example: <em>https://plus.google.com/+ucf/posts</em>',
			'default'     => 'https://plus.google.com/107946459277643419963/posts',
			'value'       => $theme_options['googleplus_url'],
		)),
		new TextField(array(
			'name'        => 'LinkedIn URL',
			'id'          => THEME_OPTIONS_NAME.'[linkedin_url]',
			'description' => 'URL to the LinkedIn user account you would like to direct visitors to.  Example: <em>https://www.linkedin.com/edu/university-of-central-florida-18118</em>',
			'default'     => 'http://bit.ly/ucfbusinessalumni',
			'value'       => $theme_options['linkedin_url'],
		)),
		new TextField(array(
			'name'        => 'Instagram URL',
			'id'          => THEME_OPTIONS_NAME.'[instagram_url]',
			'description' => 'URL to Instagram user account you would like to direct visitors to.',
			'default'     => 'http://instagram.com/ucfbusiness',
			'value'       => $theme_options['instagram_url'],
		)),
		new TextField(array(
			'name'        => 'Youtube URL',
			'id'          => THEME_OPTIONS_NAME.'[youtube_url]',
			'description' => 'URL to the YouTube channel you would like to direct visitors to.',
			'default'     => 'https://www.youtube.com/channel/UCVgFCMPKXoMeIJ3Tp73_NHg',
			'value'       => $theme_options['youtube_url'],
		)),
	),
	'Home Page' => array(
	    new SelectField(array(
		    'name' => 'Home Page Feature 1',
		    'id' => THEME_OPTIONS_NAME.'[home_feature_1]',
		    'description' => 'The 1st Home Page Feature to appear on the home page.',
		    'choices' => $homepagefeature->get_objects_as_options(),
		    'value' => $theme_options['home_feature_1'],
	    )),
        new SelectField(array(
    	    'name' => 'Home Page Feature 2',
    	    'id' => THEME_OPTIONS_NAME.'[home_feature_2]',
    	    'description' => 'The 2nd Home Page Feature to appear on the home page.',
    	    'choices' => $homepagefeature->get_objects_as_options(),
    	    'value' => $theme_options['home_feature_2'],
        )),
        new SelectField(array(
    	    'name' => 'Home Page Feature 3',
    	    'id' => THEME_OPTIONS_NAME.'[home_feature_3]',
    	    'description' => 'The 3rd Home Page Feature to appear on the home page.',
    	    'choices' => $homepagefeature->get_objects_as_options(),
    	    'value' => $theme_options['home_feature_3'],
        )),
        new SelectField(array(
    	    'name' => 'Home Page Feature 4',
    	    'id' => THEME_OPTIONS_NAME.'[home_feature_4]',
    	    'description' => 'The 4th Home Page Feature to appear on the home page.',
    	    'choices' => $homepagefeature->get_objects_as_options(),
    	    'value' => $theme_options['home_feature_4'],
        )),
        new SelectField(array(
    	    'name' => 'Home Page Feature 5',
    	    'id' => THEME_OPTIONS_NAME.'[home_feature_5]',
    	    'description' => 'The 5th Home Page Feature to appear on the home page.',
    	    'choices' => $homepagefeature->get_objects_as_options(),
    	    'value' => $theme_options['home_feature_5'],
        )),
		new RadioField(array(
			'name'        => 'Enable Home Page alternate call-to-action',
			'id'          => THEME_OPTIONS_NAME.'[enable_home_cta_alt]',
			'description' => 'When turned on, an alternate call-to-action link is displayed within the Home Page Feature area on the Home Page.',
			'default'     => 0,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_home_cta_alt'],
	    )),
	    new TextField(array(
	    	'name'        => 'Home Page alternate call-to-action link',
	    	'id'          => THEME_OPTIONS_NAME.'[home_cta_alt_url]',
	    	'description' => 'Where the alternate Home Page call-to-action link should direct to.',
	    	'value'       => $theme_options['home_cta_alt_url'],
	    )),
	    new SelectField(array(
		    'name' => 'Home Page alternate call-to-action image',
		    'id' => THEME_OPTIONS_NAME.'[home_cta_alt_image]',
		    'description' => 'Image that represents the call-to-action. Select any image uploaded to the <a href="'.get_admin_url().'upload.php">media gallery</a> or <a href="'.get_admin_url().'media-new.php">upload a new image</a>.',
		    'choices' => get_image_choices(),
		    'value' => $theme_options['home_cta_alt_image'],
	    )),
	),
	'Degrees' => array(),
	'Site' => array(
		new TextField(array(
			'name'        => 'Site Tagline',
			'id'          => THEME_OPTIONS_NAME.'[site_tagline]',
			'description' => 'An alternate site title that will display above the site title on all pages.',
			'default'     => '#UCFBusiness',
			'value'       => $theme_options['site_tagline'],
		)),
		new TextField(array(
			'name'        => 'COBA Pass button link',
			'id'          => THEME_OPTIONS_NAME.'[coba_pass_url]',
			'description' => 'The URL for the COBA Pass button used throughout the site.',
			'value'       => $theme_options['coba_pass_url'],
		)),
		new TextField(array(
			'name'        => 'AASCB Logo Button Link',
			'id'          => THEME_OPTIONS_NAME.'[aascb_url]',
			'description' => 'The URL for the AASCB logo button used throughout the site.',
			'value'       => $theme_options['aascb_url'],
		)),
	),
);

function add_degree_callout_settings() {
	$theme_options = get_option(THEME_OPTIONS_NAME);
	$terms = get_terms( 'degree_types', array( 'hide_empty' => false ) );
	$retval = array();

	if ( $terms ) {
		foreach( $terms as $term ) {
			$prefix = str_replace( '-', '_', $term->slug );

			$fields = array(
				new TextField( array(
					'name'        => $term->name.' callout box title',
					'id'          => THEME_OPTIONS_NAME.'[' . $prefix . '_callout_title]',
					'description' => 'The title text for the ' . $term->name . ' callout box displayed on degree profiles.',
					'default'     => 'Ready to Get Your Business Degree?',
					'value'       => $theme_options[ $prefix . '_callout_title'],
				)),
				new TextareaField( array(
					'name'        => $term->name.' callout box content',
					'id'          => THEME_OPTIONS_NAME.'[' . $prefix . '_callout_content]',
					'description' => 'Text to be displayed in the ' . $term->name . ' callout box that is displayed on degree profiles.  Supports HTML markup and shortcodes.',
					'value'       => $theme_options[ $prefix . '_callout_content'],
				)),
			);
			$retval = array_merge($retval, $fields);
		}
	}

	Config::$theme_settings['Degrees'] = array_merge( Config::$theme_settings['Degrees'], $retval );
}
add_action('init', 'add_degree_callout_settings');


Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);


Config::$styles = array(
	array('admin' => True, 'src' => THEME_COMPONENTS_URL.'/chosen/chosen.min.css',),
	array('admin' => True, 'src' => THEME_CSS_ADMIN_URL.'/admin.css',),
	plugins_url( 'gravityforms/css/forms.css' ),
	array('name' => 'webfont-opensans', 'src' => '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic,800,800italic'),
	array('name' => 'webfont-montserrat', 'src' => '//fonts.googleapis.com/css?family=Montserrat:400,700'),
	array('name' => 'webfont-roboto', 'src' => '//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic,900,900italic'),
	array('name' => 'webfont-rokkitt', 'src' => '//fonts.googleapis.com/css?family=Rokkitt:400'),
	array('name' => 'theme-styles', 'src' => THEME_CSS_URL.'/style.min.css',),
);

Config::$scripts = array(
	array('admin' => True, 'src' => THEME_COMPONENTS_URL.'/chosen/chosen.jquery.min.js',),
	array('admin' => True, 'src' => THEME_JS_ADMIN_URL.'/admin.js',),
	array('name' => 'ucfhb-script', 'src' => '//universityheader.ucf.edu/bar/js/university-header.js',),
	array('name' => 'theme-script', 'src' => THEME_JS_URL.'/script.min.js',),
);

Config::$metas = array(
	array('charset' => 'utf-8',),
	array('http-equiv' => 'X-UA-Compatible', 'content' => 'IE=Edge'),
	array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'),
);
if ($theme_options['gw_verify']){
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities($theme_options['gw_verify']),
	);
}



function jquery_in_header() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.0.min.js');
    wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts', 'jquery_in_header');
