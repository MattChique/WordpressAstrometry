jQuery(function($) {

	function showAnnotations() {
		//Displayed imagewidth
		$(".astrometry-image img.solved").data("solved", $(".astrometry-image img.solved").data("solved") + "&w=" + $(".astrometry-image img.solved").width());

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
		$(".astrometryActions .openFull").on('click', function() {
			window.open($(".astrometry-image img.solved").attr("src"), '_blank');
		});
	}

	if($(".astrometry-image").find(".solved").length) {
		showAnnotations();
	}
});
