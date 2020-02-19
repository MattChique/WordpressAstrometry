jQuery(function($) {

	function showAnnotations() {
		//Annotations
		$(".astrometry-image").append("<img class='annotations' />");
		$(".astrometry-image").find(".annotations").attr("src", $(".astrometry-image img.solved").data("solved"));
		
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
	}

	if($(".astrometry-image").find(".solved").length) {
		showAnnotations();
	}
});
