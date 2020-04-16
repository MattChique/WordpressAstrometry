jQuery(document).ready(function($) {

	function Solve()
	{		
		var data = {'action': 'astronomyImageAction','postId': ajax_object.postId, 'mediaId': $('.astrometry-image').data('mediaid') };

		$('.astrometry-image').append("<div class='astroStatus'></div>");
		$('.astrometry-image').append("<div class='solving'></div>");

		(function worker() {
			jQuery.post(ajax_object.ajax_url, data, function(response) {
				if(response != "") {
					$('.astrometry-image').find('.astroStatus').html(response);
					setTimeout(worker, 5);
				} else {
					$('.astrometry-image .solving').remove();
					$('.astrometry-image').find('.astroStatus').fadeOut(10000, function() { $('.astrometry-image').find('.astroStatus').remove(); });
					$('.astrometry-image :not(.annotations)').addClass('solved');

					AddActionBar();
					AddAnnotations();
				}
			});
		})();		
	}

	function AddActionBar()
	{
		//ActionBar
		$(".astrometry-image").append("<div class='astrometryActions' />");
		$(".astrometryActions").append("<span class='toggleAnnotations astrometryAction' />");
		$(".astrometryActions").append("<span class='toggleMonochrome astrometryAction' />");
		$(".astrometryActions").append("<span class='openFull astrometryAction' />");
	
		//Actions
		$(".astrometryActions .toggleAnnotations").on('click', function() {
			$(".astrometry-image").find(".annotations").toggleClass("visible");
			$(this).toggleClass("active");
		});
		$(".astrometryActions .toggleMonochrome").on('click', function() {
			$(".astrometry-image img.solved").toggleClass("monochrome");
			$(this).toggleClass("active");
		});
		$(".astrometryActions .openFull").on('click', function() {
			window.open($(".astrometry-image img.solved").attr("src"), '_blank');
		});

		//Zoomable Skyplot
		$(".skyplot").on('click', function() {
			var src = $(this).find('img').attr('src');
			if(src.indexOf('zoom2') > 0) {
				$(this).find('img').attr('src', src.replace("zoom2", "zoom1"))
				return;
			}
			if(src.indexOf('zoom1') > 0) {
				$(this).find('img').attr('src', src.replace("zoom1", "zoom2"))
				return;
			}
		});

		//Toggle Fullsize Image width Annotations
		$(".astrometry-image img").on('click', function() {
			$(".astrometry-image").toggleClass("fullsize");
		});
	}

	function AddAnnotations()
	{
		width = $(".astrometry-image > figure").width();
		if(width != null && width > 0) {
			var annotationObjects = $("<img />")
				.insertAfter(".astrometry-image img.solved")
				.attr("src", $(".astrometry-image img").data("solved") + "&w=" + width)
				.addClass("annotations");
		}
	}

	if($(".astrometry-image").length > 0) {		
		if($(".astrometry-image img.solved").length == 0) {
			Solve();
		} else {
			AddActionBar();
			AddAnnotations();
		}
	}
});
