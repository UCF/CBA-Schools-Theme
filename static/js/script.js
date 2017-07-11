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

var initCarousel = function($) {
  if ($(document).width() < 768 && $centerpieceCarousel.find('.item').length == 1) {
    $('#centerpiece-carousel').carousel().carousel('cycle');
  }

  $('.carousel-indicators li').on('click', function() {
    // Explicitly cast slide to int or the .carousel(int) function fails.
    var slide = parseInt($(this).attr('data-slide-to'));
    $('#centerpiece-carousel').carousel(slide);
  });
};


if (typeof jQuery !== 'undefined'){
  jQuery(document).ready(function ($) {


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
    initCarousel($);
    responsiveVideos($);
    splitFooterMenu($);
    showSubMenu($);
  });
} else {
  console.log('jQuery dependency failed to load');
}
