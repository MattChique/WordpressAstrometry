<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class AstrometryData
{
    private $postId;
    private $mediaId;
    private $metaFieldName;
	private $data;
	
	private $astrometryNetApi = "http://nova.astrometry.net/api/";
	private $astrometryNetPublicly = "n";

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
		if($this->data == null)
			return null;

        if(array_key_exists($key,$this->data) && $this->data[$key] != null)
            return $this->data[$key];
        else
            return null;
    }

    public function Solve($apiKey) 
	{
		if($apiKey == "")
			return "No API Key!";

		if($this->Get("annotations") == null)
		{
			if($this->Get("subid") != null)
			{
				$resultJsonSubmission = json_decode(file_get_contents($astrometryNetApi."submissions/".$this->Get("subid")));

				if($resultJsonSubmission->jobs[0] != "")
				{	
                    $this->Add("submission", $resultJsonSubmission);

					$resultJsonJob = json_decode(file_get_contents($astrometryNetApi."jobs/".$resultJsonSubmission->jobs[0]));
					if($resultJsonJob->status == "failure")
					{
						$this->Remove("submission");
						$this->Remove("subid");

						return __("Could not solve image", "astrometry");
					}
					if($resultJsonJob->status == "solving")
					{
						return printf(__('Solving image. Job: %1$s -> %2$s', "astrometry"), $resultJsonSubmission->jobs[0], $resultJsonJob->status);
					}
					if($resultJsonJob->status == "success")
					{			
						GetInfo($resultJsonSubmission->jobs[0]);
						GetAnnotations($resultJsonSubmission->jobs[0]);

						return __("Image successfully solved!", "astrometry");
					}

					return printf(__('Solving image: %1$s Submission: %2$s', "astrometry"), $resultJsonJob->status, $this->Get("subid"));
                }
				
				return printf(__('Solving image. Submission: %1$s', "astrometry"), $this->Get("subid"));
			}
			else
			{
                $loginResponse = json_decode($this->Curl($astrometryNetApi."login",'request-json={"apikey": "'.$apiKey.'"}'), false);
                $imageUrl =  wp_get_attachment_image_src($this->mediaId, 'original')[0];
				$content = 'request-json={"session": "'. $loginResponse->session.'", "url": "'.$imageUrl.'", "allow_commercial_use": "n", "publicly_visible" : "'.$astrometryNetPublicly.'"}';
				$jobResponse = json_decode($this->Curl($astrometryNetApi."url_upload",$content),false);
				
				$this->Add("subid", $jobResponse->subid);
				
				return printf(__('Image solving startet: %1$s', "astrometry"), $jobResponse->subid);
			}
		}
	}

	public function GetInfo($jobId)
	{
		$resultJson = json_decode(file_get_contents($this->astrometryNetApi."jobs/".$jobId."/info/"), true);
		$this->Add("info", $resultJson);
	}

	public function GetAnnotations($jobId)
	{
		$resultJson = json_decode(file_get_contents($this->astrometryNetApi."jobs/".$jobId."/annotations/"), true);
		$this->Add("annotations", $resultJson);
	}

	public function GetTagLinks()
	{
        $tags = array();
        foreach($this->Get("info")["tags"] as $t)
        {
            $text = preg_replace("/u([0-9a-f]{2,4})/", "&#x\\1;", $t);
            array_push($tags, "<a href='/?s=".$text."'>".$text."</a>");
		}
		return $tags;
	}

	private function Curl($url, $content)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$result = curl_exec($curl);

		curl_close($curl);

		return $result;
	} 
}
?>