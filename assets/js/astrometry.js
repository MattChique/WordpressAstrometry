jQuery(document).ready(function($) {

	function Solve(astroImg)
	{		
		var solvedata = {
			'action': 'astronomyImageStartSolve',
			'postId': ajax_object.postId, 
			'mediaId': astroImg.data('mediaid') 
		};

		astroImg.append("<div class='solving'></div>");

		(function worker() {
			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: solvedata,
				success: function (response, textStatus, XMLHttpRequest) {
					if(response != "") {
						if(astroImg.find(".astrometry-status").length == 0) {
							astroImg.append("<div class='astrometry-status'></div>");
						}
						astroImg.find('.astrometry-status').html(response);
						setTimeout(worker, 5);
					} else {
						astroImg.find('.solving').remove();
						astroImg.find('.astrometry-status').fadeOut(10000, function() { astroImg.find('.astroStatus').remove(); });
						astroImg.find(':not(.annotations)').addClass('solved');
	
						AddActionBar(astroImg);
						AddAnnotations(astroImg);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("Solving failed at Ajax Post")
					astroImg.find('.solving').remove();
				}
			});
		})();
	}

	function RefreshAstrometryInfo(astroImg)
	{		
		var solvedata = {
			'action': 'astronomyRefreshAstrometryInfo',
			'postId': ajax_object.postId, 
			'mediaId': astroImg.data('mediaid') 
		};

		(function worker() {
			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: solvedata,
				success: function (response, textStatus, XMLHttpRequest) {
					astroImg.parent().find(".objects").html(response);
					astroImg.parent().find(".refreshInfo").toggleClass("active");
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					astroImg.parent().find(".refreshInfo").toggleClass("active");
				}
			});
		})();
	}

	function AddActionBar(astroImg)
	{
		//ActionBar
		astroImg.append("<div class='astrometryActions' />");
		astroImg.find(".astrometryActions").append("<span class='toggleAnnotations astrometryAction' />");
		astroImg.find(".astrometryActions").append("<span class='toggleMonochrome astrometryAction' />");
		astroImg.find(".astrometryActions").append("<span class='openFull astrometryAction' />");
	
		//Actions
		astroImg.find(".astrometryActions .toggleAnnotations").on('click', function() {
			astroImg.find(".annotations").toggleClass("visible");
			$(this).toggleClass("active");
		});
		astroImg.find(".astrometryActions .toggleMonochrome").on('click', function() {
			astroImg.find("img.solved").toggleClass("monochrome");
			$(this).toggleClass("active");
		});
		astroImg.find(".astrometryActions .openFull").on('click', function() {
			window.open($(".astrometry-image img.solved").attr("src"), '_blank');
		});

		//Refresh info button
		astroImg.parent().find(".refreshInfo").on('click', function() {
			RefreshAstrometryInfo(astroImg);
			$(this).toggleClass("active");
		});

		//Zoomable Skyplot
		astroImg.parent().find(".skyplot img").on('click', function() {
			var src = $(this).attr('src');
			if(src.indexOf('zoom2') > 0) {
				$(this).attr('src', src.replace("zoom2", "zoom1"))
				return;
			}
			if(src.indexOf('zoom1') > 0) {
				$(this).attr('src', src.replace("zoom1", "zoom2"))
				return;
			}
		});

		//Toggle Fullsize Image with Annotations
		astroImg.find("figure").on('click', function() {
			astroImg.toggleClass("fullsize");
		});
	}

	function AddAnnotations(astroImg)
	{
		width = astroImg.find("> figure").width();
		if(width != null) {
			if(width > 0) {
				AddAnnotationImage(astroImg, width);
			} else {
				width = 0;
				parent = astroImg.find("> figure").parent();
				while(parent != null)
				{
					if(parent.width() > 0)
					{
						width = parent.width()
						parent = null;
					}
					else
					{
						parent = parent.parent();
					}
				}
				AddAnnotationImage(astroImg, width);
			}
		}
	}

	function AddAnnotationImage(astroImg, width)
	{
		var annotationObjects = $("<img />")
		.insertAfter(astroImg.find("img.solved"))
		.attr("src", astroImg.find("img").data("solved") + "&w=" + width)
		.addClass("annotations");	
	}

	$(".astrometry-image").each(function() {
		if($(this).find("img.solved").length == 0) {
			Solve($(this));
		} else {
			AddActionBar($(this));
			AddAnnotations($(this));
		}
	});
});
