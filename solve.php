<?php

if (!function_exists('astrometry')) {
	function astrometrySolve($postId, $mediaId) 
	{
		$astrometry_settings_options = get_option( 'astrometry_settings_option_name' );
		$apiKey = $astrometry_settings_options['api_key'];

		if($apiKey == "")
			return "No API Key!";

		global $wp_query;
		$annotedImage = "";
		$objectTags = "";

		//http://astrometry.net/doc/net/api.html#misc-notes
		//http://nova.astrometry.net/user_images/1447320#original

		if(get_post_meta($postId, "astrometry_annotations", true) != "")
		{
			$annotedImage = "http://nova.astrometry.net/annotated_full/" . get_post_meta($postId, "astrometry_job", true);
			$jsonInfo = json_decode(get_post_meta($postId, "astrometry_info", true));
			foreach($jsonInfo->machine_tags as $key)
			{
				$objectTags = $objectTags . $key . ", ";
			}
		}
		else
		{
			if(get_post_meta($postId, "astrometry_subid", true) != "")
			{
				$result = file_get_contents("http://nova.astrometry.net/api/submissions/".get_post_meta($postId, "astrometry_subid", true));
				$resultJson = json_decode($result);

				if($resultJson->jobs[0] != "")
				{
					add_post_meta($postId, "astrometry_submission", $result, true);

					$resultJobsJson = json_decode(file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJson->jobs[0]));

					if($resultJobsJson->status == "failure")
					{
						delete_post_meta($postId, "astrometry_submission");
						delete_post_meta($postId, "astrometry_subid");
						return "Bild wurde nicht astrometrisiert. Fehlschlag.";
					}
					if($resultJobsJson->status == "solving")
					{
						return "Bild wird astrometrisiert. Job: " . $resultJson->jobs[0] . " -> " . $resultJobsJson->status;
					}
					if($resultJobsJson->status == "success")
					{			
						$resultI = file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJson->jobs[0]."/info/");
						$resultA = file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJson->jobs[0]."/annotations/");
						add_post_meta($postId, "astrometry_info", $resultI, true);
						add_post_meta($postId, "astrometry_annotations", $resultA, true);
						add_post_meta($postId, "astrometry_job", $resultJson->jobs[0], true);
						add_post_meta($postId, "astrometry_jobcalibrations", json_encode($resultJson->job_calibrations), true);
						return "Bild wurde erfolgreich astrometrisiert. Bitte Seite neu laden.";
					}
					return "Bild wird astrometrisiert. " . $resultJobsJson->status . " Submission: " . get_post_meta($postId, "astrometry_subid", true);
				}
				return "Bild wird astrometrisiert. Submission: " . get_post_meta($postId, "astrometry_subid", true);
			}
			else
			{
				$loginResponse = json_decode(astrometryCurl("http://nova.astrometry.net/api/login",'request-json={"apikey": "'.$apiKey.'"}'), false);
				$url =  wp_get_attachment_image_src($mediaId, 'original')[0];
				$content = 'request-json={"session": "'. $loginResponse->session.'", "url": "'.$url.'", "allow_commercial_use": "n", "publicly_visible" : "n"}';
				$jobResponse = json_decode(astrometryCurl("http://nova.astrometry.net/api/url_upload",$content),false);

				add_post_meta($postId, "astrometry_subid", $jobResponse->subid, true);

				return "Bild wird angemeldet zum Astrometrisieren: "  . $jobResponse->subid;
			}
		}
	}
}

if (!function_exists('astrometryCurl')) {
	function astrometryCurl($url, $content)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		return curl_exec($curl);
	}
}
?>