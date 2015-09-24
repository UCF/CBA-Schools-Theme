// Define globals for JSHint validation:
/* global console, ga, DocumentTouch */


/*
 * Theme-specific function definitions:
 */


/*
 * Determine if the primary nav mobile toggle is visible (if the user's
 * device is mobile-sized)
 */
var isMobile = function() {
  return jQuery('#header-menu-mobile-toggle').is(':visible');
};


/*
 * Detect if the user is on a touch device (Modernizr check)
 */
var isTouchDevice = function() {
  return ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;
};


/*
 * Assign browser-specific body classes on page load
 */
var addBodyClasses = function() {
  var bodyClass = 'js-enabled';

  // Old IE:
  if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) { //test for MSIE x.x;
    var ieversion = Number(RegExp.$1); // capture x.x portion and store as a number
    if (ieversion >= 10) { bodyClass += ' ie ie10'; }
    else if (ieversion >= 9) { bodyClass += ' ie ie9'; }
    else if (ieversion >= 8) { bodyClass += ' ie ie8'; }
    else if (ieversion >= 7) { bodyClass += ' ie ie7'; }
  }
  // IE11+:
  else if (navigator.appName === 'Netscape' && !!navigator.userAgent.match(/Trident\/7.0/)) { bodyClass += ' ie ie11'; }
  // iOS:
  else if (navigator.userAgent.match(/iPhone/i)) { bodyClass += ' iphone'; }
  else if (navigator.userAgent.match(/iPad/i)) { bodyClass += ' ipad'; }
  else if (navigator.userAgent.match(/iPod/i)) { bodyClass += ' ipod'; }
  // Android:
  else if (navigator.userAgent.match(/Android/i)) { bodyClass += ' android'; }
  // Touch device:
  if (isTouchDevice()) { bodyClass += ' touch-device'; }

  $('body').addClass(bodyClass);
};


/*
 * Strip empty p tags
 */
var stripEmptyPtags = function() {
  $('p:empty').remove();
};


/*
 * Google Analytics click event tracking.
 * Do not apply the .ga-event-link class to non-link ('<a></a>') tags!
 *
 * interaction: Default 'event'. Used to distinguish unique interactions, i.e. social interactions
 * category:    Typically the object that was interacted with (e.g. button); for social interactions, this is the 'socialNetwork' value
 * action:      The type of interaction (e.g. click) or 'like' for social ('socialAction' value)
 * label:       Useful for categorizing events (e.g. nav buttons); for social, this is the 'socialTarget' value
 */
var gaEventTracking = function() {
  $('.ga-event-link').on('click', function(e) {
    e.preventDefault();

    var $link       = $(this);
    var url         = $link.attr('href');
    var interaction = $link.attr('data-ga-interaction') ? $link.attr('data-ga-interaction') : 'event';
    var category    = $link.attr('data-ga-category') ? $link.attr('data-ga-category') : 'Outbound Links';
    var action      = $link.attr('data-ga-action') ? $link.attr('data-ga-action') : 'click';
    var label       = $link.attr('data-ga-label') ? $link.attr('data-ga-label') : $link.text();
    var target      = $link.attr('target');

    if (typeof ga !== 'undefined' && action !== null && label !== null) {
      ga('send', interaction, category, action, label);
      if (typeof target !== 'undefined' && target === '_blank') {
        window.open(url, '_blank');
      }
      else {
        window.setTimeout(function(){ document.location = url; }, 200);
      }
    }
    else {
      document.location = url;
    }
  });
};


/*
 * Apply ellipses to overflowing content.
 */
var handleEllipses = function($) {
  var $homeNewsTitles = $('.feature-news .news-title a');
  $homeNewsTitles.dotdotdot({
    watch: true,
  });
};


/*
 * Affix primary header to top of page.
 * Header must be completely unaffixed at mobile sizes
 * so that an expanded subnav can be scrolled through.
 */
var primaryHeaderAffix = function($) {
  var setAffix = function() {
    var $header = $('.primary-header, #primary-nav-menu-pulldown');

    if (!isTouchDevice() && !isMobile()) {
      $header
        .affix({
          offset: {
            top: 50
          }
        });
    }
    else {
      $(window).off('.affix');
      $header
        .removeData('bs.affix')
        .removeClass('affix affix-top affix-bottom');
    }
  };

  setAffix();
  $(window).on('resize', setAffix);
};


/*
 * Add icons to primary nav menu items that have children.
 * Icon markup is stored in footer template.
 */
var addPrimaryNavChevrons = function($) {
  var $icon = $('#primary-nav-expand-icon').removeAttr('id');
  var $navLinks = $('#header-menu > .menu-item-has-children > a');

  $navLinks.append($icon);
};


/*
 * Handle sizing of primary nav menu columns.
 */
var primaryNavSubmenuColSizing = function($) {
  var $submenus = $('#header-menu > li > .sub-menu');

  /*
   * Handle 1st-level .sub-menu widths.
   * Note this width value is overridden via CSS at mobile sizes!
   */
  var setMaxContentWidth = function() {
    $submenus.width($('#header-menu').width() - 40); // add a little bit of pad ($grid-gutter-width * 2)
  };

  setMaxContentWidth();
  $(window).on('resize', setMaxContentWidth);

  /*
   * Handle 1st-level .sub-menu list item grouping
   */
  var groupSubmenuItem = function( $submenu, $listItem, col ) {
    var $col1 = $submenu.children('.sub-menu-col-1');
    var $col2 = $submenu.children('.sub-menu-col-2');
    var $col3 = $submenu.children('.sub-menu-col-3');

    switch (col) {
      case 1:
        $col1.append($listItem);
        break;
      case 2:
        $col2.append($listItem);
        break;
      default:
        $col3.append($listItem);
        break;
    }
  };

  /*
   * Sort .sub-menu items into subcolumns for more logical grouping
   */
  $('.sub-menu-col-1, .sub-menu-col-2, .sub-menu-col-3').clone().appendTo($submenus);
  $submenus.each(function() {
    var $submenu = $(this);
    var $listItems = $submenu.children('.menu-item').detach();
    // List Item Count
    var $licount = $listItems.length;
    // The remainder of $licount / 3.
    var $liRemainder = $licount % 3;

    var col1Limit = 1;
    var col2Limit = 2;
    var col       = 1;

    if ($listItems.length > 3) {
      switch ( $liRemainder ) {
        case 1:
          col1Limit = (($licount - $liRemainder) / 3) + $liRemainder;
          col2Limit = (col1Limit * 2) - 1;
          break;
        case 2:
          col1Limit = (($licount - ($licount % 3)) / 3) + 1;
          col2Limit = col1Limit * 2;
          break;
        default:
          col1Limit = $licount / 3;
          col2Limit = ($licount / 3) * 2;
          break;
      }
    }

    for (var i=0; i<$listItems.length; i++) {
      // Determine which of 3 columns this item belongs in
      if (i >= col1Limit && i < col2Limit) {
        col = 2;
      }
      else if (i >= col2Limit) {
        col = 3;
      }

      groupSubmenuItem( $submenu, $listItems[i], col );
    }
  });
};


/*
 * Toggle the primary nav menu pulldown when a nav menu item
 * with children is hovered over.
 */
var primaryNavBgToggle = function($) {
  var $primaryHeader = $('.primary-header');
  var $pulldown = $('#primary-nav-menu-pulldown');
  var $navLinks = $('#header-menu > .menu-item > a');

  var setPulldownHeight = function(height) {
    $pulldown.height(height);
  };

  /*
   * Check if the mouse has moved below the pulldown. Toggle the pulldown off if
   * the mouse has moved out.
   *
   * The pulldown area spans 100% width, so we don't care about X coordinates here.
   *
   * We handle this on document.mousemove, as opposed to on mouseenter/leave
   * on $pulldown, due to the absolute positioning of $primaryHeader's
   * contents.
   */
  var handlePulldownMouseEvents = function(e) {
    var mouseY = e.clientY || e.pageY;
    var height = e.data.height + 30; // add a little bit of pad to account for general user error

    if ($('#ucfhb').length && !$primaryHeader.hasClass('affix')) {
      height = height + $('#ucfhb').height();
    }

    if ($pulldown.hasClass('active') && mouseY > height) {
      deactivateActiveSubmenu();
      togglePulldown(false);
    }
  };

  var togglePulldown = function(activate, height) {
    if (activate === true) {
      $pulldown.addClass('active');
      setPulldownHeight(height);
      $(document).on('mousemove', { height: height }, handlePulldownMouseEvents);
    }
    else {
      $pulldown.removeClass('active');
      setPulldownHeight(0);
      $(document).off('mousemove', handlePulldownMouseEvents);
    }
  };

  var activateSubmenu = function($link, $submenu) {
    // Attempt to get the fixed height of the submenu
    // so that we can animate to that fixed value.
    if (isMobile()) {
      $submenu.css('height', 'auto');
      var fixedHeight = $submenu.height();
      $submenu
        .css('height', 0)
        .animate({
          height: fixedHeight,
        }, 250);
    }

    $link.addClass('active');
    $submenu.addClass('active');
  };

  var deactivateActiveSubmenu = function() {
    var $activeSubmenu = getActiveSubmenu();

    // Animate back to 0 height (because expanding the submenu
    // added a fixed height value to the submenu.)
    if (isMobile()) {
      $activeSubmenu
        .animate({
          height: 0,
        }, 250, function() {
          $(this).css('height', ''); // Unset fixed 0 height when animation is done
        });
    }

    $navLinks
      .removeClass('active')
      .find('.active')
        .removeClass('active');
    $activeSubmenu.removeClass('active');
  };

  var getActiveSubmenu = function() {
    return $navLinks.siblings('.sub-menu.active');
  };

  /*
   * Returns the necessary height for $pulldown based on the
   * provided $submenu's dimensions.
   */
  var getPulldownHeightBySubmenu = function($submenu) {
    var newPulldownHeight = (($submenu.offset().top + $submenu.outerHeight()) - $primaryHeader.offset().top);

    if ($('#ucfhb').length) {
      newPulldownHeight = newPulldownHeight + $('#ucfhb').height();
    }

    return newPulldownHeight;
  };

  /*
   * Activate a submenu. Trigger the pulldown toggle for non-mobile devices
   * or expand the submenu on mobile based on the primary nav link toggled.
   */
  var handleLinkHover = function(e) {
    e.preventDefault();

    var $link = e.data.$link;
    var $submenu = e.data.$submenu;

    if (isMobile()) {
      // Mobile toggles can only be activated/deactivated on link items with
      // sub-menus, so we don't have to check for children here.
      // Just toggle the active classes.
      if ($link.hasClass('active')) {
        deactivateActiveSubmenu();
      }
      else {
        deactivateActiveSubmenu();
        if ($submenu) {
          activateSubmenu($link, $submenu);
        }
      }
    }
    else {
      deactivateActiveSubmenu();

      // Show pulldown, submenu if this link's parent has a submenu
      if ($submenu) {
        activateSubmenu($link, $submenu);
        var newPulldownHeight = getPulldownHeightBySubmenu($submenu);
        togglePulldown(true, newPulldownHeight);
      }
      // If this is a link with no submenus, deactivate the pulldown
      else {
        togglePulldown(false, null);
      }
    }
  };

  /*
   * Turn on/off mouseover for primary nav links depending on browser size.
   */
  var toggleLinkHoverEvents = function() {
    $navLinks.each(function() {
      var $toggler = $(this);
      var $togglerMobile = $toggler.find('.primary-nav-expand-icon');
      var $submenu = $toggler.siblings('.sub-menu');
      $submenu = $submenu.length ? $submenu : null;

      $toggler.off('mouseenter mouseleave', handleLinkHover);
      $togglerMobile.off('click', handleLinkHover);

      if (isMobile()) { // Don't detect hover events for the mobile nav menu
        $togglerMobile.on('click', { $link: $togglerMobile, $submenu: $submenu }, handleLinkHover);
      }
      else {
        $toggler.on('mouseenter mouseleave', { $link: $toggler, $submenu: $submenu }, handleLinkHover);
      }
    });
  };


  toggleLinkHoverEvents();

  // Make sure the window width has actually changed (touch
  // events can return a false positive for window.resize)
  var windowWidth = $(window).width();

  $(window).on('resize', function() {
    var newWindowWidth = $(window).width();

    if (newWindowWidth !== windowWidth) {
      deactivateActiveSubmenu();
      togglePulldown(false, null);

      // Turn off hover events for primary nav links if the
      // window resized to a mobile size
      toggleLinkHoverEvents();

      // Adjust the pulldown height if the active submenu's
      // contents expanded/contracted on window resize.
      var $activeSubmenu = getActiveSubmenu();
      var height = 0;
      if ($activeSubmenu.length) {
        height = getPulldownHeightBySubmenu($activeSubmenu);
      }
      setPulldownHeight(height);

      windowWidth = newWindowWidth;
    }
  });
};


/*
 * Toggle the nav menu at mobile sizes.
 */
var mobilePrimaryNavToggle = function($) {
  var $mobileNavToggle = $('#header-menu-mobile-toggle');
  var $menu = $('#header-menu');

  $mobileNavToggle.on('click', function(e) {
    e.preventDefault();

    $(this).toggleClass('active');

    // Animate slideup/slidedown of #header-menu from height 0 to 'auto'.
    if ($menu.hasClass('mobile-active')) {
      $menu
        .animate({
          height: 0,
        }, 250)
        .removeClass('mobile-active');
    }
    else {
      $menu.css('height', 'auto');
      var fixedHeight = $menu.height();
      $menu
        .css('height', 0)
        .animate({
          height: fixedHeight,
        }, 250)
        .addClass('mobile-active');
    }
  });

  // Make sure an expanded submenu is deactivated when
  // the window size changes.
  // Make sure the window width has actually changed (touch
  // events can return a false positive for window.resize)
  var windowWidth = $(window).width();

  $(window).on('resize', function() {
    var newWindowWidth = $(window).width();

    if (newWindowWidth !== windowWidth) {
      $mobileNavToggle.removeClass('active');
      $menu
        .css('height', 0)
        .removeClass('mobile-active');

      windowWidth = newWindowWidth;
    }
  });
};


/*
 * Make sure embedded videos are responsive.  Also fixes youtube z-index issues
 */
var responsiveVideos = function($) {
  var $embeds = $('iframe, embed, video, object');
  if ($embeds.length) {
    $embeds.each(function() {
      var $embed = $(this),
          src = $embed.attr('src');

      // Loose youtube url match.
      var youtubeMatch = src.match(/(youtube\.)|(youtu\.be)/);
      if (youtubeMatch && youtubeMatch.length) {
        var append = src.indexOf('?') === 0 ? '?' : '&';
        $embed.attr('src', src + append + 'wmode=transparent');
      }

      if (!$embed.parent('div.embed-responsive').length) {
        $embed
          .addClass('embed-responsive-item')
          .wrap('<div class="embed-responsive embed-responsive-16by9" />');
      }
    });
  }
};


/*
 * Split footer menu into two columns
 */
var splitFooterMenu = function ($) {
  var $primaryFooter = $('.primary-footer'),
    $subMenuItems = $primaryFooter.find('.sub-menu > li'),
    middlePoint = Math.ceil($subMenuItems.length / 2) + 1,
    middleElement;

  if (middlePoint) {
    middleElement = $subMenuItems.eq(middlePoint)
      .parent('.sub-menu')
      .parent('.menu-item');

    if (middleElement) {
      $primaryFooter.find('#primary-subfooter-nav-2')
        .find('ul')
        .append(middleElement.nextAll().andSelf());

      $primaryFooter.find('.invisible').removeClass('invisible');
    }
  }
};


/*
 * Display the side menu subnav
 */
var showSubMenu = function ($) {
  var $currentPage = $('.side-menu').find('.current_page_item'),
    $parent = $currentPage.parent();

  if ($parent.hasClass('sub-menu')) {
    $currentPage.addClass('selected');
    $parent.attr('style','display:block');
  } else {
    $currentPage.find('.sub-menu').attr('style','display:block');
  }
};


if (typeof jQuery !== 'undefined'){
  jQuery(document).ready(function ($) {
    var $centerpieceCarousel = $('#centerpiece-carousel');

    Webcom.handleExternalLinks($);
    Webcom.loadMoreSearchResults($);

    Generic.defaultMenuSeparators($);
    Generic.removeExtraGformStyles($);
    Generic.PostTypeSearch($);

    /* Theme-specific Functions */
    // $('input, textarea').placeholder();
    addBodyClasses($);
    stripEmptyPtags($);
    gaEventTracking($);
    handleEllipses($);
    primaryHeaderAffix($);
    addPrimaryNavChevrons($);
    primaryNavSubmenuColSizing($);
    primaryNavBgToggle($);
    mobilePrimaryNavToggle($);
    if ($(document).width() > 768 && $centerpieceCarousel.find('.item').length > 1) {
      $centerpieceCarousel.carousel().carousel('cycle');
    }
    responsiveVideos($);
    splitFooterMenu($);
    showSubMenu($);
  });
} else {
  console.log('jQuery dependency failed to load');
}
