jQuery(document).ready(function($) {
	if($(".astrometry-image").length) {		
		//Append displayed width
		width = $(".astrometry-image").width();
		if(width != null && width > 0) {
			$(".astrometry-image img.annotations").data("width", width);
			$(".astrometry-image img.solved+img.annotations").attr("src",$(".astrometry-image img.annotations").data("src") + "&w=" + width);
		}
		
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
	}
});
