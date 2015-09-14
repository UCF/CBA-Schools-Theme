# College of Business Administration WordPress Theme - [University of Central Florida Business Degrees, Graduate MBA and PhD in Business Administration](http://business.ucf.edu)  

WordPress theme for UCF COBA, written off of Generic Theme.


## Installation Requirements:
* WordPress 4.1+

### Plugins
* Page Links To


## Deployment

When this theme is delivered to the client, all Bower assets should be
committed to the repo.  No special steps should be required during
deployment, other than checking out the theme via Git.

Unlike most themes based off of Generic Theme, Bootstrap is not included
as a submodule within this project.  Recursive cloning and submodule
updating are not necessary during deployment.


## Development

### PHP
This theme follows most standard WordPress theme conventions, but utilizes
helper classes for doing common but tedious functions like registering custom
post types, meta fields, and theme options.  See the "Important Files/Folders"
section for more information.


### CSS, JavaScript
This theme utilizes a combination of CodeKit and Bower for handling third-party
front-end dependencies, minification, and concatenation of static files.

Using these tools for development is not required; you can easily add quick CSS
and JavaScript overrides by creating new CSS/JS files and adding them to the
bottom of the registered styles/scripts arrays (`Config::$styles` and
`Config::$scripts`) in `functions/config.php`. However, for the sake of theme
organization and clean code, we recommend using the tools below for
modifications and additions to the theme's front-end assets.

#### Bower
Bower is set up in this repo for handling all third-party front-end
dependencies.  See [their website](http://bower.io/) for more information and
installation instructions.

Whenever a third-party package needs to be included in this theme, run
`bower install --save <package>` from the theme's root directory to download
the package's contents to the `static/bower_components/` directory and update
the Bower dependencies list (`bower.json`).

Whenever this theme is completed, all Bower components should be pushed up to
the repo for easier access for COBA's admins.  If Bower components have not yet
been pushed up to the repo, they will need to be installed on your development
machine by running `bower install` from the theme's root directory. **Note:
Bower packages must be installed in `static/bower_components/` BEFORE
attempting to modify any theme-specific SASS or JavaScript files.**

No files in the `bower_components` directory should be modified directly.  If a
package really needs a custom modification, consider forking the package and
possibly hosting it on Github, then installing the package via Bower by Github
URL.

#### CodeKit
A CodeKit config file is included in this theme's root directory.  [CodeKit]
(http://incident57.com/codekit/) is a Mac-only tool that handles Sass and
JavaScript compilation and minification, and also has browser refreshing tools
and Bower compatibility built-in.

CodeKit's config file is set up to make CodeKit compile (almost) all of our
theme-specific SASS and JavaScript together with our Bower components to
produce single, minified files for fewer client-side HTTP requests.

##### CSS Asset Compiling/Minifying
Whenever a theme-specific SASS file is updated (and CodeKit is running), all
SASS files should compile together with the third-party packages defined in
`static/scss/style.scss`.  The final compiled file is saved as
`static/css/style.min.css`.

Assets defined in `style.scss` should always be included in the following
order:
* `_variables.scss`
* Third-party vendor assets
* `_base.scss`

Admin-specific CSS is not compiled/minified.

##### JavaScript Asset Compiling/Minifying
Whenever a theme-specific JavaScript file is updated (and CodeKit is running),
`static/js/script.js` will be prepended with any included scripts at the top
of the file (using `@codekit-prepend`) and is minified and saved to
`static/js/script.min.js`.

Assets defined in `script.js` should always be included in the following order:
* Third-party vendor assets
* `webcom-base.js`
* `generic-base.js`

All files prepended to `script.js` should work independently and can be
registered as separate scripts with WordPress for debugging purposes if
necessary.

Admin-specific JS is not compiled/minified.

#### SASS
Non-admin, theme-specific styles for this theme are saved in `static/scss/`
for cleaner, more organized style definitions (as opposed to managing all
of our site's styles from a single file).  We use CodeKit to combine all the
SASS files together and compile the final code into actual CSS
(`static/css/style.min.css`).

Individual SASS partials are combined **in a specific order**--see
`static/scss/_base.scss`.  Note that `_variables.scss` is not included in this
file; it is included in the final minified file before third-party packages
are included, so that we can override SASS variables as necessary before
processing.

As a general rule, SASS partials should be combined in order from the most
generic/abstract to the most specific.

When writing view-specific styles, try to follow WordPress template naming
conventions-- i.e., styles that are specific to the 'Person' post type should
be in a file named `_views-single-person.scss`


## Important files/folders:

### functions/base.php
Where functions and classes used throughout the theme are defined.

### functions/config.php
Where Config::$links, Config::$scripts, Config::$styles, and
Config::$metas should be defined.  Custom post types and custom taxonomies should
be set here via Config::$custom_post_types and Config::$custom_taxonomies.
Custom thumbnail sizes, menus, and sidebars should also be defined here.

### functions.php
Theme-specific functions only should be defined here.  (Other required
function files are also included at the top of this file.)

### shortcodes.php
Where Wordpress shortcodes can be defined.  See example shortcodes for more
information.

### custom-post-types.php
Where the abstract custom post type and all its descendants live.

### static/
Where, aside from style.css in the root, all static content such as
javascript, images, and css should live.  Bower components should also live
here (separate from our theme-specific files).


## Notes
* As of v1.1.0 of this theme, Faculty News and Research articles have been
given a separate post type, Publications.  A converter tool for generating
Publications from both posts (categorized as "Faculty News" or "Faculty
Research") and from Person meta fields is available in the WordPress admin,
under "Tools".  After running the conversion tool, old News/Research posts
should be deleted.  A future theme update will remove the Person meta fields.


## Custom Post Types

### Degree
Defines a degree program and its information.  Can be grouped by Degree Type.

### Home Page Feature
Defines a large homepage image and call-to-action.

### Person
Defines a person that is associated with the College of Business.  Can be
grouped by Organizational Groups, Employee Types, and/or Departments.

### Spotlight
Defines a link to some external content with a thumbnail (is displayed on the
homepage).

### Publication
Defines a published article, which can refer to either an external URL, or
be self-hosted, similar to a standard Post.  Can be grouped by Publication
Type.


## Custom Taxonomies

### Degree Types
Organizes Degrees.  Generally used for defining Degrees by level of education;
e.g. "Undergraduate Programs", "Graduate Programs".

### Organizational Groups
Organizes People.  Useful for defining more arbitrary groups to generate
custom lists of People; e.g. "Economics Faculty" or "College Executive
Committee".

### Employee Types
Organizes People.  Intended to define a Person's role within the college; e.g.
"Faculty" or "Staff".

### Departments
Organizes People.  Intended to define specific, existing departments within the
college and the People associated with them.

### Publication Types
Organizes Publications.  Useful for defining detailed groups to generate custom
lists of Publications; e.g. "Faculty News" or "Faculty Research".


## Shortcodes

### [gallery]
The default WordPress [gallery] shortcode has been overridden in this theme to
render Bootstrap carousels instead.  The shortcode still accepts all attributes
of the original shortcode, but those that are not necessary for creating
Bootstrap carousels (e.g. 'itemtag', 'icontag', 'columns') are ignored.

### [posttype-list]
Custom post types that have defined $use_shortcode as True can automatically
utilize this shortcode for displaying a list of posts created under the given
post type; e.g., [document-list] will output a list of all published Documents.
Additional parameters can be used to further narrow down the shortcode's results;
see the Theme Help section on shortcodes for an available list of filters.

### [search_form]
Outputs the site search form.  The search form output can be modified via
searchform.php

### [post-type-search]
Generates a searchable list of posts. Post lists are generated in
alphabetical order and, by default, by category and post title. Posts can be
searched by post title and any tags assigned to the post. See the Theme Help
section on shortcodes for more information.

### [coba-pass]
Displays the COBA Pass image and link.

### [aascb_logo]
Displays the AASCB logo and link.

### [left-col], [right-col]
Starts and ends a row with a custom set of two columns.

### [lead]
Styles text with .lead class.

### [button-link]
Creates a Bootstrap button.  See the Theme Help section on shortcodes for an
available list of arguments.

### [icon]
Generates an empty `<span>` tag for passing FontAwesome or Glyphicon icon
classes to.

### [twitter-timeline]
Generates a JavaScript-based Twitter timeline on the page.  Uses the widget ID
set in Theme Options as a default.  See the Theme Help section on shortcodes
for an available list of arguments.

### [rss-feed]
Displays a simple list of links from any RSS feed.  See the Theme Help section
on shortcodes for an available list of arguments.

### [news-feed]
Displays a list of UCF Today stories with thumbnails and excerpts.  See the
Theme Help section on shortcodes for an available list of arguments.

### [events-feed]
Displays a list of events from events.ucf.edu.  See the Theme Help section on
shortcodes for an available list of arguments.

### [well]
Wraps content within a Bootstrap well.

### [degree-callout]
Displays callout box (well) for Degrees.


