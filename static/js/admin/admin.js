// Define globals for JSHint validation:
/* global send_to_editor */


// Adds filter method to array objects
// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/filter

/* jshint ignore:start */
if (!Array.prototype.filter) {
  Array.prototype.filter = function(a) {
    "use strict";
    if (this === void 0 || this === null) throw new TypeError;
    var b = Object(this);
    var c = b.length >>> 0;
    if (typeof a !== "function") throw new TypeError;
    var d = [];
    var e = arguments[1];
    for (var f = 0; f < c; f++) {
      if (f in b) {
        var g = b[f];
        if (a.call(e, g, f, b)) d.push(g)
      }
    }
    return d
  }
}
/* jshint ignore:end */


var WebcomAdmin = {};


WebcomAdmin.__init__ = function($) {
  // Allows forms with input fields of type file to upload files
  $('input[type="file"]').parents('form').attr('enctype', 'multipart/form-data');
  $('input[type="file"]').parents('form').attr('encoding', 'multipart/form-data');
};


WebcomAdmin.shortcodeTool = function($) {
  var cls = this;
  cls.metabox = $('#shortcodes-metabox');
  if (cls.metabox.length < 1) {
    return;
  }

  cls.form = cls.metabox.find('form');
  cls.search = cls.metabox.find('#shortcode-search');
  cls.button = cls.metabox.find('button');
  cls.results = cls.metabox.find('#shortcode-results');
  cls.select = cls.metabox.find('#shortcode-select');
  cls.form_url = cls.metabox.find("#shortcode-form").val();
  cls.text_url = cls.metabox.find("#shortcode-text").val();

  cls.shortcodes = (function() {
    var shortcodes = [];
    cls.select.children('.shortcode').each(function() {
      shortcodes.push($(this).val());
    });
    return shortcodes;
  })();

  cls.shortcodeAction = function(shortcode) {
    var text = "[" + shortcode + "]";
    send_to_editor(text);
  };

  cls.searchAction = function() {
    cls.results.children().remove();

    var value = cls.search.val();

    if (value.length < 1) {
      return;
    }

    var found = cls.shortcodes.filter(function(e) {
      return e.match(value);
    });

    if (found.length > 1) {
      cls.results.removeClass('empty');
    }

    $(found).each(function() {
      var item = $("<li />");
      var link = $("<a />");
      link.attr('href', '#');
      link.addClass('shortcode');
      link.text(this.valueOf());
      item.append(link);
      cls.results.append(item);
    });


    if (found.length > 1) {
      cls.results.removeClass('empty');
    } else {
      cls.results.addClass('empty');
    }

  };

  cls.buttonAction = function() {
    cls.searchAction();
  };

  cls.itemAction = function() {
    var shortcode = $(this).text();
    cls.shortcodeAction(shortcode);
    return false;
  };

  cls.selectAction = function() {
    var selected = $(this).find(".shortcode:selected");
    if (selected.length < 1) {
      return;
    }

    var value = selected.val();
    cls.shortcodeAction(value);
  };

  //Resize results list to match size of input
  cls.results.width(cls.search.outerWidth());

  // Disable enter key causing form submit on shortcode search field
  cls.search.keyup(function(e) {
    cls.searchAction();

    if (e.keyCode === 13) {
      return false;
    }
  });

  // Search button click action, cause search
  cls.button.click(cls.buttonAction);

  // Option change for select, cause action
  cls.select.change(cls.selectAction);

  // Results click actions
  cls.results.find('li a.shortcode').live('click', cls.itemAction);
};


WebcomAdmin.themeOptions = function($) {
  var cls = this;
  cls.active = null;
  cls.parent = $('.i-am-a-fancy-admin');
  cls.sections = $('.i-am-a-fancy-admin .fields .section');
  cls.buttons = $('.i-am-a-fancy-admin .sections .section a');

  this.showSection = function() {
    var button = $(this);
    var href = button.attr('href');
    var section = $(href);

    // Switch active styles
    cls.buttons.removeClass('active');
    button.addClass('active');

    cls.active.hide();
    cls.active = section;
    cls.active.show();

    history.pushState({}, "", button.attr('href'));
    var http_referrer = cls.parent.find('input[name="_wp_http_referer"]');
    http_referrer.val(window.location);
    return false;
  };

  this.__init__ = function() {
    cls.active = cls.sections.first();
    cls.sections.not(cls.active).hide();
    cls.buttons.first().addClass('active');
    cls.buttons.click(this.showSection);

    if (window.location.hash) {
      cls.buttons.filter('[href="' + window.location.hash + '"]').click();
    }

    var fadeTimer = setInterval(function() {
      $('.updated').fadeOut(1000);
      clearInterval(fadeTimer);
    }, 2000);
  };

  if (cls.parent.length > 0) {
    cls.__init__();
  }
};


WebcomAdmin.MultiSelectAutocomplete = function($) {
  var $autocompleteFields = $('select[multiple]');

  if ($autocompleteFields.length) {
    $autocompleteFields.chosen({width: '90%'});
  }
};


(function($) {
  WebcomAdmin.__init__($);
  WebcomAdmin.themeOptions($);
  WebcomAdmin.shortcodeTool($);
  WebcomAdmin.MultiSelectAutocomplete($);
})(jQuery);


/**
 * Utility method to increment form field names, used be the clonable fields.
 * @author rj.bruneel@ucf.com (RJ Bruneel)
 */

function updateFieldNames(fields) {

  var $ = jQuery.noConflict();

  fields.val('');

  // update name attribute after cloning with new number using regex
  $.each(fields, function(index) {
    fields.eq(index).attr('name', fields.eq(index).attr('name').replace(/\[(\d+)\]/, function(fullMatch, n) {
      return "[" + (Number(n) + 1) + "]";
    }));
  });

}


/**
 * jQuery plugin to add/remove class fields
 * @author rj.bruneel@ucf.com (RJ Bruneel)
 */

(function($) {

  $.fn.classClonableFields = function() {

    var $container = this,
      $classScheduleTable = $container.find('.class-schedule-table'),
      count = $container.find('.add-class').attr('data-ucf-count');

    // Adds new class fields
    function addClassSchedule(e) {

      e.preventDefault();

      var $lastRow = $classScheduleTable.find('tr').last(),
        newFields = $lastRow.clone().appendTo($classScheduleTable).find('input');

      count++;

      updateFieldNames(newFields);

    }

    // Remove new class
    function removeFields(e) {
      var $parent = $(e.target).parent().parent();

      if ($parent.parent().find('tr').length === 2) {
        // if only one item empty field values
        $parent.find('input').val('');
      } else {
        // else remove fields
        $(this).parent().parent().remove();
      }
    }

    // Click handler to add new classes to the class schedule
    $container.on('click', '.add-class', addClassSchedule);

    // Click handler to remove class from the class schedule
    $container.on('click', '.remove-class', removeFields);

    return $container;

  };

}(jQuery));


/**
 * jQuery plugin to add/remove news/research/media fields
 * @author rj.bruneel@ucf.com (RJ Bruneel)
 */

(function($) {

  $.fn.clonableFields = function() {

    var $container = this,
      count = $container.find('.add-item').attr('data-ucf-count');

    // Add fields method for add click handler
    function addFields(e) {
      e.preventDefault();

      var $lastRow = $container.find('.item-container').last(),
        newFields = $lastRow.clone().appendTo($container.find('.article-container')).find('input, textarea');

      count++;

      updateFieldNames(newFields);

    }

    function removeFields(e) {
      var $parent = $(e.target).parent();

      if ($parent.parent().find('.item-container').length === 1) {
        // if only one item empty field values
        $parent.find('input, textarea').val('');
      } else {
        // else remove fields
        $parent.slideUp("slow", function() {
          $parent.remove();
        });
      }
    }

    // Click handler to add fields
    $container.on('click', '.add-item', addFields);

    // Click handler to remove fields
    $container.on('click', '.remove-item', removeFields);

    return $container;

  };

}(jQuery));


/**
 * Initialize Class, News and Research add/remove fields
 * @author rj.bruneel@ucf.com (RJ Bruneel)
 */

(function() {

  var $ = jQuery.noConflict();

  $('#person_class_schedule_metabox').classClonableFields();

  $('#person_news_metabox').clonableFields();

  $('#person_research_metabox').clonableFields();

  $('#person_media_metabox').clonableFields();

})();


/**
 * Add autocomplete functionality to Departments taxonomy "Links To Page" field
 **/
(function() {

  var $ = jQuery.noConflict(),
      $linksTo = $('.department-links-to-autocomplete'),
      $linksToWrap = $linksTo.parent('.term-meta-page_link-wrap'),
      $descriptionWrap = $linksToWrap.prev('.term-description-wrap');

  // Move the Links To Page field above Description to prevent overflow
  // clipping from the parent #col-left div
  if ( $descriptionWrap.length ) {
    $linksToWrap
      .detach()
      .insertBefore($descriptionWrap);
  }

  $linksTo.chosen({width: '90%'});

})();
