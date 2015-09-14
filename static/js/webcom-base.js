// Define globals for JSHint validation:
/* global window, document */


/******************************************************************************\
 Global Namespace
 the only variable exposed to the window should be Webcom
\******************************************************************************/
var Webcom = {};

if(!window.console ) { window.console = { log: function() { return; } }; }

Webcom.handleExternalLinks = function($){
	$('a:not(.ignore-external)').each(function(){
		var url  = $(this).attr('href');
		var host = window.location.host.toLowerCase();

		if (url && url.search(host) < 0 && url.search('http') > -1){
			$(this).attr('target', '_blank');
			$(this).addClass('external');
		}
	});
};

Webcom.loadMoreSearchResults = function($){
	var more        = '#search-results .more';
	var items       = '#search-results .result-list .item';
	var list        = '#search-results .result-list';
	var start_class = 'new-start';

	var next = null;
	var sema = null;

	var load = (function(){
		if (sema){
			setTimeout(function(){load();}, 100);
			return;
		}

		if (next === null){return;}

		// Grab results content and append to current results
		var results = $(next).find(items);

		// Add navigation class for scroll
		$('.' + start_class).removeClass(start_class);
		$(results[0]).addClass(start_class);

		$(list).append(results);

		// Grab new more link and replace current with new
		var anchor = $(next).find(more);
		if (anchor.length < 1){
			$(more).remove();
		}
		$(more).attr('href', anchor.attr('href'));

		next = null;
	});

	var prefetch = (function(){
		sema = true;
		// Fetch url for href via ajax
		var url = $(more).attr('href');
		if (url){
			$.ajax({
				'url'     : url,
				'success' : function(data){
					next = data;
				},
				'complete' : function(){
					sema = false;
				}
			});
		}
	});

	var load_and_prefetch = (function(){
		load();
		prefetch();
	});

	if ($(more).length > 0){
		load_and_prefetch();

		$(more).click(function(){
			load_and_prefetch();
			var scroll_to = $('.' + start_class).offset().top - 10;

			var element = 'body';

			if($.browser.mozilla || $.browser.msie){
				element = 'html';
			}

			$(element).animate({'scrollTop' : scroll_to}, 1000);
			return false;
		});
	}
};
