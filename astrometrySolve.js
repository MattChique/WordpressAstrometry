jQuery(document).ready(function($) {
	
	if($(".astrometry-image:not(.solved)").length) {
		var data = {'action': 'astronomyImageAction','postId': ajax_object.postId, 'mediaId': $('.astrometry-image').data('mediaid') };

		$('.astrometry-image').append("<div class='astroStatus'>Astrometrisieren wird vorbereitet...</div>");
		$('.astrometry-image').append("<div class='solving'></div>");

		(function worker() {
			jQuery.post(ajax_object.ajax_url, data, function(response) {
				if(response != "") {
					$('.astrometry-image').find('.astroStatus').html(response);
					setTimeout(worker, 10);
				} else {
					$('.astrometry-image .solving').remove();
					$('.astrometry-image img').addClass("solved");
				}
			});
		})();
	}
});