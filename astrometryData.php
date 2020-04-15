<?
class AstrometryData
{
    private $postId;
    private $mediaId;
    private $metaFieldName;
    private $data;

    public function __construct($postId, $mediaId)
    {
        $this->postId = $postId;
        $this->mediaId = $mediaId;
        $this->metaFieldName = "astrometry_data_" . $this->mediaId;
        $this->data = json_decode(get_post_meta($this->postId, $this->metaFieldName, true), true);
    }

    public function Add($key, $value)
    {
        $this->data[$key] = $value;

        if(!add_post_meta($this->postId, $this->metaFieldName, json_encode($this->data), true)) { 
            update_post_meta($this->postId, $this->metaFieldName, json_encode($this->data));
        }
    }

    public function Remove($key)
    {
        unset($this->data[$key]);
        
        update_post_meta ( $this->postId, $this->metaFieldName, json_encode($this->data) );
    }

    public function Get($key)
    {
        if(array_key_exists($key,$this->data) && $this->data[$key] != null)
            return $this->data[$key];
        else
            return null;
    }

    public function Solve($apiKey) 
	{
		if($apiKey == "")
			return "No API Key!";

        if($this->Get("annotations") == "")
		{
			if($this->Get("subid") != "")
			{
				$resultJsonSubmission = json_decode(file_get_contents("http://nova.astrometry.net/api/submissions/".$this->Get("subid")));

				if($resultJsonSubmission->jobs[0] != "")
				{
                    $this->Add("submission", $resultJsonSubmission);

					$resultJsonJob = json_decode(file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJsonSubmission->jobs[0]));
					if($resultJsonJob->status == "failure")
					{
						$this->Remove("submission");
						$this->Remove("subid");

						return "Bild wurde nicht astrometrisiert. Fehlschlag.";
					}
					if($resultJsonJob->status == "solving")
					{
						return "Bild wird gerade astrometrisiert. Job: " . $resultJsonSubmission->jobs[0] . " -> " . $resultJsonJob->status;
					}
					if($resultJsonJob->status == "success")
					{			
						$resultJsonJobInfo = json_decode(file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJsonSubmission->jobs[0]."/info/"));
						$resultJsonAnnotations = json_decode(file_get_contents("http://nova.astrometry.net/api/jobs/".$resultJsonSubmission->jobs[0]."/annotations/"));

						$this->Add("info", $resultJsonJobInfo);
						$this->Add("annotations", $resultJsonAnnotations);

						return "Bild wurde erfolgreich astrometrisiert.";
					}

					return "Bild wird astrometrisiert. " . $resultJsonJob->status . " Submission: " . $this->Get("subid");
                }
                
				return "Bild wird astrometrisiert. Submission: " . $this->Get("subid");
			}
			else
			{
                $loginResponse = json_decode($this->Curl("http://nova.astrometry.net/api/login",'request-json={"apikey": "'.$apiKey.'"}'), false);
                $url =  wp_get_attachment_image_src($this->mediaId, 'original')[0];
				$content = 'request-json={"session": "'. $loginResponse->session.'", "url": "'.$url.'", "allow_commercial_use": "n", "publicly_visible" : "n"}';
				$jobResponse = json_decode($this->Curl("http://nova.astrometry.net/api/url_upload",$content),false);
                
				$this->Add("subid", $jobResponse->subid);
                
				return "Bild wird angemeldet zum Astrometrisieren: "  . $jobResponse->subid;
			}
		}
	}

	private function Curl($url, $content)
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