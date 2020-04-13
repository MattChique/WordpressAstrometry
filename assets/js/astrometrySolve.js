jQuery(document).ready(function($) {
	
	if($(".astrometry-image").length > 0 && $(".astrometry-image img.solved").length == 0) {
		var data = {'action': 'astronomyImageAction','postId': ajax_object.postId, 'mediaId': $('.astrometry-image').data('mediaid') };

		$('.astrometry-image').append("<div class='astroStatus'>Astrometrisieren wird vorbereitet...</div>");
		$('.astrometry-image').append("<div class='solving'></div>");
		$('.astrometryActions').css('display','none');

		(function worker() {
			jQuery.post(ajax_object.ajax_url, data, function(response) {
				if(response != "") {
					$('.astrometry-image').find('.astroStatus').html(response);
					setTimeout(worker, 10);
				} else {
					$('.astrometry-image .solving').remove();
					$('.astrometry-image').find('.astroStatus').fadeOut(10000, function() { $('.astrometry-image').find('.astroStatus').remove(); });
					$('.astrometry-image img:not(.annotations)').addClass('solved');
					$('.astrometryActions').fadeIn();
					$(".astrometry-image img.solved+img.annotations").attr("src",$(".astrometry-image img.annotations").data("src") + "&w=" + $(".astrometry-image img.annotations").data("width"));
				}
			});
		})();
	}
});