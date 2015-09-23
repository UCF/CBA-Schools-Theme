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
	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5' );

	// Custom Image Sizes
	add_image_size('homepagefeature-photo', 1200, 640, True);
	add_image_size('person-thumb', 170, 170, True);
	add_image_size('spotlight-thumb', 195, 195, True);
	add_image_size('slideshow-photo', 1140, 641);

	// Custom Menus
	register_nav_menu('devos-menu', __('Devos Menu'));

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
define('THEME_JOBS_DIR', THEME_DIR.'/jobs');
define('THEME_CUSTOMIZER_PREFIX', 'cbachild_');
define('GA_ACCOUNT', get_theme_mod_or_default( 'ga_account' ) );
define('CB_UID', get_theme_mod_or_default( 'cb_uid' ) );
define('CB_DOMAIN', get_theme_mod_or_default( 'cb_domain' ) );

define( 'FEED_FETCH_TIMEOUT', 8 ); // seconds


/**
 * Set config values including meta tags, registered custom post types, styles,
 * scripts, and any other statically defined assets that belong in the Config
 * object.
 **/
Config::$custom_post_types = array(
	'Centerpiece',
	'Page',
	'Post',
	'Spotlight',
	'Publication',
	'Person',
);

Config::$custom_taxonomies = array(
	'OrganizationalGroups',
	'Departments',
	'Cohorts',
);

Config::$body_classes = array();
/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions/base.php
 **/
function define_customizer_panels ( $wp_customize ) {
	$wp_customize->add_panel(
		THEME_CUSTOMIZER_PREFIX . 'home',
		array(
			'title'           => 'Home Page'
		)
	);
}

add_action( 'customize_register', 'define_customizer_panels' );

function define_customizer_sections( $wp_customize ) {
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'homefeatures',
		array(
			'title' => 'Home Page Features',
			'panel' => THEME_CUSTOMIZER_PREFIX.'home'
		)
	);

	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'footer',
		array(
			'title'       => 'Footer',
			'description' => 'Settings for the site footer',
		)
	);

	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'analytics',
		array(
			'title' => 'Analytics'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'events',
		array(
			'title'       => 'Events',
			'description' => 'Settings for event lists used throughout the site.'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'news',
		array(
			'title'       => 'News',
			'description' => 'Settings for news feeds used throughout the site.'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'search',
		array(
			'title'       => 'Search',
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'contact',
		array(
			'title'       => 'Contact Information'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'social',
		array(
			'title'       => 'Social Media'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'webfonts',
		array(
			'title'       => 'Web Fonts'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'people',
		array(
			'title'       => 'People Settings',
			'description' => 'Defaults for People'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'parentmenu',
		array(
			'title'       => 'Parent Site',
			'description' => 'Settings for syncing menus with parent site.'
		)
	);
	$wp_customize->add_section(
		THEME_CUSTOMIZER_PREFIX.'sitestyles',
		array(
			'title'       => 'Site Styles',
			'description' => 'Various style settings for the site.'
		)
	);

	$wp_customize->get_section( 'static_front_page' )->panel = THEME_CUSTOMIZER_PREFIX.'home';
}

add_action( 'customize_register', 'define_customizer_sections' );

Config::$settings_defaults = array(
	'events_max_items'      => 4,
	'events_url'            => 'http://events.ucf.edu/feed.rss',
	'news_url'              => 2,
	'news_max_items'        => 'http://today.ucf.edu/feed/',
	'enable_google'         => 1,
	'search_per_page'       => 10,
	'cloud_typography'      => '//cloud.typography.com/730568/675644/css/fonts.css', // TODO: update to use PROD css key
	'organization_name'     => 'College of Business Administration',
	'organization_address'  => 'University of Central Florida
College of Business Administration
4000 Central Florida Blvd.
P.O. Box 161991
Orlando, FL 32816-1991',
	'bootstrap_menu_styles' => 'nav-pills',
);

function get_setting_default( $setting, $fallback=null ) {
	return isset( Config::$settings_defaults[$setting] ) ? Config::$settings_defaults[$settings] : $fallback;
}

function define_customizer_fields( $wp_customize ) {
	// Home Settings
	$wp_customize->add_setting(
		'home_page_banner'
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'home_page_banner',
			array(
			    'label'    => 'Home Page Banner Image',
			    'section'  => THEME_CUSTOMIZER_PREFIX.'homefeatures',
			)
		)
	);

	$centerpieces = get_posts( array( 'post_type' => 'centerpiece' ) );

	$centerpiece_arr = array();

	$centerpiece_arr[''] = '----------------';

	foreach( $centerpieces as $centerpiece ) {
		$centerpiece_arr[$centerpiece->ID] = $centerpiece->post_title;
	}

	$wp_customize->add_setting(
		'home_page_default_centerpiece'
	);

	$wp_customize->add_control(
		'home_page_default_centerpiece',
		array(
			'type'    => 'select',
			'label'   => 'Home Page Default Centerpiece',
			'section' => THEME_CUSTOMIZER_PREFIX.'homefeatures',
			'choices' => $centerpiece_arr
		)
	);

	$spotlights = get_posts( array( 'post_type' => 'spotlight' ) );

	$spotlight_arr = array();

	$spotlight_arr[''] = '----------------';

	foreach( $spotlights as $spotlight ) {
		$spotlight_arr[$spotlight->ID] = $spotlight->post_title;
	}

	$wp_customize->add_setting(
		'home_page_spotlight'
	);

	$wp_customize->add_control(
		'home_page_spotlight',
		array(
			'type'        => 'select',
			'label'       => 'Home Page Spotlight',
			'section'     => THEME_CUSTOMIZER_PREFIX.'homefeatures',
			'choices'     => $spotlight_arr
		)
	);

	$publications = get_posts( array( 'post_type' => 'publication' ) );

	$publication_arr = array();

	$publication_arr[''] = '----------------';

	foreach( $publications as $publication ) {
		$publication_arr[$publication->ID] = $publication->post_title;
	}

	$wp_customize->add_setting(
		'home_page_publication'
	);

	$wp_customize->add_control(
		'home_page_publication',
		array(
			'type'        => 'select',
			'label'       => 'Home Page Publication',
			'section'     => THEME_CUSTOMIZER_PREFIX.'homefeatures',
			'choices'     => $publication_arr
		)
	);

	$wp_customize->add_setting(
		'home_page_video_url'
	);

	$wp_customize->add_control(
		'home_page_video_url',
		array(
			'type'        => 'url',
			'label'       => 'Home Page Video URL',
			'section'     => THEME_CUSTOMIZER_PREFIX.'homefeatures'
		)
	);

	// Footer Settings
	$wp_customize->add_setting(
		'footer_feature_title'
	);

	$wp_customize->add_control(
		'footer_feature_title',
		array(
			'type'        => 'text',
			'label'       => 'Feature Title',
			'description' => 'Title displayed in the footer feature.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'footer'
		)
	);

	$wp_customize->add_setting(
		'footer_feature_image'
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'footer_feature_image',
			array(
			    'label'    => 'Feature Image',
			    'section'  => THEME_CUSTOMIZER_PREFIX.'footer',
			)
		)
	);

	$wp_customize->add_setting(
		'footer_feature_cta'
	);

	$wp_customize->add_control(
		'footer_feature_cta',
		array(
			'type'        => 'text',
			'label'       => 'Feature Call to Action',
			'description' => 'CTA in the footer feature.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'footer'
		)
	);

	// Analytics
	$wp_customize->add_setting(
		'gw_verify'
	);
	$wp_customize->add_control(
		'gw_verify',
		array(
			'type'        => 'text',
			'label'       => 'Google WebMaster Verification',
			'description' => 'Example: <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'analytics',
		)
	);
	$wp_customize->add_setting(
		'gw_account'
	);
	$wp_customize->add_control(
		'gw_account',
		array(
			'type'        => 'text',
			'label'       => 'Google Analytics Account',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'analytics'
		)
	);
	// Events
	$wp_customize->add_setting(
		'events_max_items',
		array(
			'default'     => get_setting_default( 'events_max_items' ),
		)
	);
	$wp_customize->add_control(
		'events_max_items',
		array(
			'type'        => 'select',
			'label'       => 'Events Max Items',
			'description' => 'Maximum number of events to display when outputting event information.',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'events',
			'choices'     => array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5
			)
		)
	);
	$wp_customize->add_setting(
		'events_url',
		array(
			'default'     => get_setting_default( 'events_url' ),
		)
	);
	$wp_customize->add_control(
		'events_url',
		array(
			'type'        => 'text',
			'label'       => 'Events Calendar URL',
			'description' => 'Base URL for the calendar you wish to use. Example: <em>http://events.ucf.edu/mycalendar</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'events'
		)
	);
	// News
	$wp_customize->add_setting(
		'news_max_items',
		array(
			'default'     => get_setting_default( 'news_max_items' ),
		)
	);
	$wp_customize->add_control(
		'news_max_items',
		array(
			'type'        => 'select',
			'label'       => 'News Max Items',
			'description' => 'Maximum number of articles to display when outputting news information.',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'news',
			'choices'     => array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5
			)
		)
	);
	$wp_customize->add_setting(
		'news_url',
		array(
			'default'     => get_setting_default( 'news_url' ),
		)
	);
	$wp_customize->add_control(
		'news_url',
		array(
			'type'        => 'text',
			'label'       => 'News Feed',
			'description' => 'Use the following URL for the news RSS feed <br>Example: <em>http://today.ucf.edu/feed/</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'news'
		)
	);
	// Search
	$wp_customize->add_setting(
		'enable_google',
		array(
			'default'     => get_setting_default( 'enable_google' ),
		)
	);
	$wp_customize->add_control(
		'enable_google',
		array(
			'type'        => 'checkbox',
			'label'       => 'Enable Google Search',
			'description' => 'Enable to use the google search appliance to power the search functionality.',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'search'
		)
	);
	$wp_customize->add_setting(
		'search_domain'
	);
	$wp_customize->add_control(
		'search_domain',
		array(
			'type'        => 'text',
			'label'       => 'Search Domain',
			'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example: <em>some.domain.com</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'search'
		)
	);
	$wp_customize->add_setting(
		'search_per_page',
		array(
			'default'     => get_setting_default( 'search_per_page' ),
			'type'        => 'option'
		)
	);
	$wp_customize->add_control(
		'search_per_page',
		array(
			'type'        => 'number',
			'label'       => 'Search Results Per Page',
			'description' => 'Number of search results to show per page of results',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'search',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 50,
				'step' => 1
			)
		)
	);
	// Contact Info
	$wp_customize->add_setting(
		'organization_name'
	);

	$wp_customize->add_control(
		'organization_name',
		array(
			'type'        => 'text',
			'label'       => 'Organization Name',
			'description' => 'Your organization\'s name',
			'section'     => THEME_CUSTOMIZER_PREFIX.'contact',
			'default'     => get_setting_default( 'organization_name' )
		)
	);

	$wp_customize->add_setting(
		'contact_email'
	);
	$wp_customize->add_control(
		'contact_email',
		array(
			'type'        => 'email',
			'label'       => 'Contact Email',
			'description' => 'Contact email address that visitors to your site can use to contact you.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'contact'
		)
	);

	$wp_customize->add_setting(
		'contact_phone'
	);

	$wp_customize->add_control(
		'contact_phone',
		array(
			'type'        => 'text',
			'label'       => 'Contact Phone',
			'description' => 'Contact phone that visitors to your site can use to contact you.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'contact'
		)
	);

	$wp_customize->add_setting(
		'contact_fax'
	);

	$wp_customize->add_control(
		'contact_fax',
		array(
			'type'        => 'tel',
			'label'       => 'Fax',
			'description' => 'Fax number that visitors to your site can use to fax you.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'contact'
		)
	);

	$wp_customize->add_setting(
		'organization_address'
	);

	$wp_customize->add_control(
		'organization_address',
		array(
			'type'        => 'textarea',
			'label'       => 'Organization Address',
			'description' => 'The address of your organization.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'contact',
			'default'     => get_setting_default( 'organization_address' )
		)
	);

	$wp_customize->add_setting(
		'office'
	);

	$wp_customize->add_control(
		'office',
		array(
			'type'       => 'text',
			'label'      => 'Office Building/Room Number',
			'section'    => THEME_CUSTOMIZER_PREFIX.'contact'
		)
	);

	$wp_customize->add_setting(
		'office_hours'
	);

	$wp_customize->add_control(
		'office_hours',
		array(
			'type'       => 'text',
			'label'      => 'Office Hours',
			'section'    => THEME_CUSTOMIZER_PREFIX.'contact'
		)
	);

	// Social Media
	$wp_customize->add_setting(
		'facebook_url'
	);
	$wp_customize->add_control(
		'facebook_url',
		array(
			'type'        => 'url',
			'label'       => 'Facebook URL',
			'description' => 'URL to the Facebook page you would like to direct visitors to.  Example: <em>https://www.facebook.com/UCF</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'social'
		)
	);
	$wp_customize->add_setting(
		'twitter_url'
	);
	$wp_customize->add_control(
		'twitter_url',
		array(
			'type'        => 'url',
			'label'       => 'Twitter URL',
			'description' => 'URL to the Twitter user account you would like to direct visitors to.  Example: <em>http://twitter.com/UCF</em>',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'social'
		)
	);
	// Web Fonts
	$wp_customize->add_setting(
		'cloud_typography_key',
		array(
			'default'     => get_setting_default( 'cloud_typography_key' )
		)
	);
	$wp_customize->add_control(
		'cloud_typography_key',
		array(
			'type'        => 'text',
			'label'       => 'Cloud.Typography CSS Key URL',
			'description' => 'The CSS Key provided by Cloud.Typography for this project.  <strong>Only include the value in the "href" portion of the link
								tag provided; e.g. "//cloud.typography.com/000000/000000/css/fonts.css".</strong><br><br>NOTE: Make sure the Cloud.Typography
								project has been configured to deliver fonts to this site\'s domain.<br>
								See the <a target="_blank" href="http://www.typography.com/cloud/user-guide/managing-domains">Cloud.Typography docs on managing domains</a> for more info.',
			'section'     => THEME_CUSTOMIZER_PREFIX . 'webfonts'
		)
	);

	$wp_customize->add_setting(
		'parent_site_menu_url'
	);

	$wp_customize->add_control(
		'parent_site_menu_url',
		array(
			'type'        => 'url',
			'label'       => 'Parent Site Menu URL',
			'description' => 'The url of the page on the parent site where the header menu is located.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'parentmenu'
		)
	);

	$wp_customize->add_setting(
		'people_default_image'
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control( $wp_customize, 'people_default_image',
			array(
			    'label'    => 'People Default Image',
			    'section'  => THEME_CUSTOMIZER_PREFIX.'people',
			)
		)
	);

	$cohorts = get_terms( array( 'cohorts' ) );

	$cohort_arr = array();

	$cohort_arr[''] = '----------------';

	foreach( $cohorts as $cohort ) {
		$cohort_arr[$cohort->term_id] = $cohort->name;
	}

	$wp_customize->add_setting(
		'people_default_cohort'
	);

	$wp_customize->add_control(
		'people_default_cohort',
		array(
			'type'        => 'select',
			'label'       => 'Default Cohort',
			'description' => 'The cohort that will be displayed on the Meet the Cohort page.',
			'section'     => THEME_CUSTOMIZER_PREFIX.'people',
			'choices'     => $cohort_arr
		)
	);

	/**
	 * If Yoast SEO is activated, assume we're handling ALL SEO-related
	 * modifications with it.  Don't add Facebook Opengraph theme options.
	 **/
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( !is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
		$wp_customize->add_setting(
			'enable_og',
			array(
				'default'     => 1,
			)
		);
		$wp_customize->add_control(
			'enable_og',
			array(
				'type'        => 'checkbox',
				'label'       => 'Enable Opengraph',
				'description' => 'Turn on the Opengraph meta information used by Facebook.',
				'section'     => THEME_CUSTOMIZER_PREFIX . 'social'
			)
		);
		$wp_customize->add_setting(
			'fb_admins'
		);
		$wp_customize->add_control(
			'fb_admins',
			array(
				'type'        => 'textarea',
				'label'       => 'Facebook Admins',
				'description' => 'Comma separated facebook usernames or user ids of those responsible for administrating any facebook pages created from pages on this site. Example: <em>592952074, abe.lincoln</em>',
				'section'     => THEME_CUSTOMIZER_PREFIX . 'social'
			)
		);

		$wp_customize->add_setting(
			'bootstrap_menu_styles'
		);
		$wp_customize->add_control(
			'bootstrap_menu_styles',
			array(
				'type'        => 'select',
				'label'       => 'Bootstrap Menu Style',
				'description' => 'Choose the style of menu to use for the sidebar menu.',
				'section'     => THEME_CUSTOMIZER_PREFIX.'sitestyles',
				'choices'     => array(
					'default'   => 'Default (list of links with dropdowns)',
					'nav-tabs'  => 'Tabs with dropdowns',
					'nav-pills' => 'Pills with dropdowns'
				),
				'default'     => get_setting_default( 'bootstrap_menu_styles' )
			)
		);
	}
}

add_action( 'customize_register', 'define_customizer_fields' );

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
if ( get_theme_mod_or_default( 'gw_verify' ) ) {
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities( get_theme_mod_or_default( 'gw_verify' ) ),
	);
}

function jquery_in_header() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.0.min.js');
    wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts', 'jquery_in_header');
