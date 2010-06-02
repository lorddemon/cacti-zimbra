<?php
/*
*		Zimbra PHP SOAP stats client 
*		Written by: ben leto <ben@otelconsulting.com>
*		Special Thanks to: "--[ UxBoD ]--" <uxbod@splatnix.net> from the zimbra forums
*		Version: .01
*/

class ZimbraSoapStats{
		
		var $Hostname, $Username, $Password;
		var $AuthToken;
		
		function GetMTAstats(){

        $request = array(
                "Header" => array(
                        "context" => array(
                                "_jsns" => "urn:zimbra",
                                "authToken" => array(
                                        "_content" => $this->AuthToken,
                                ),
                        ),
                ),
                "Body" => array(
                        "GetLoggerStatsRequest" => array(
                                "_jsns" => "urn:zimbraAdmin",
                        				"zmmtastats" => array(
					                                        "_content" => "0",
																),
                        ),
                ),
	    	);
	    	
	    	$Res = $this->zimbraSoapRequest($request);
				print_r($Res);

		}
		
		function GetUserTotalActive(){

        $request = array(
                "Header" => array(
                        "context" => array(
                                "_jsns" => "urn:zimbra",
                                "authToken" => array(
                                        "_content" => $this->AuthToken,
                                ),
                        ),
                ),
                "Body" => array(
								"DumpSessionsRequest" => array(
					                        	"_jsns" => "urn:zimbraAdmin",
									"listSessions" => array(
					                                        "_content" => "0",
										),
									),
								),
	    	);
	    	
	    	$Res = $this->zimbraSoapRequest($request);
				return $Res['activeSessions'];
		}

		function GetUserTotal(){
			
			return $this->GetUserList(true);
		}
		
		function GetUserList($JustTotals=false){
		
			$request = array(
				                "Header" => array(
				                        "context" => array(
				                                "_jsns" => "urn:zimbra",
				                                "authToken" => array(
				                                        "_content" => $this->AuthToken,
				                                ),
				                        ),
				                ),
				                "Body" => array(
							"GetQuotaUsageRequest" => array(
				                        	"_jsns" => "urn:zimbraAdmin"
							),
						),
        );			
		
			$Res = $this->zimbraSoapRequest($request);

			$RtnAccArr = array();	
			$AccCnt = 0;
			
			foreach ($Res['account'] as $Row) {
				if (!preg_match("/^admin|^galsync|^ham\.|^spam\.|^wiki/i", $Row['name'])) {
					array_push($RtnAccArr, $Row);
					$AccCnt++;
				}
			}
			
			if($JustTotals)
				return $AccCnt;
			else
				return $RtnAccArr;
			
		}
		/*
		* Authentication Functions
		*/		
		function Auth($Hostname, $Username, $Password){

			$this->Hostname = $Hostname;
			$this->Username = $Username;
			$this->Password = $Password;	

			/*if(!$this->zimbraSoapCheckService())
				die('Zimbra Soap Service is down');
			*/
			if($this->AuthToken == Null)
				$this->GetAuthToken();
				
		}

		function GetAuthToken(){
			
				$request = array(
				"Header" => array(
					"_jsns" => "urn:zimbra",
				),
				"Body" => array(
					"AuthRequest" => array(
						"_jsns" => "urn:zimbraAdmin",
						"account" => array(
							"_content" => $this->Username,
						),
						"password" => array(
							"_content" => $this->Password,
						),
					)
				),
				);

				$Res = $this->zimbraSoapRequest($request);
				$this->AuthToken = $Res['authToken'][0]['_content'];		


		}

		function zimbraSoapRequest($data, $admin=true, $strip=true) {
			$js = json_encode($data);
	
			$url = ($admin ?
				sprintf("https://%s:7071/service/admin/soap/", $this->Hostname) :
				sprintf("https://%s/service/soap/", $this->Hostname)
			);
	
			$ch = curl_init();
	
			$options = array(
				CURLOPT_URL => $url,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $js,
				CURLOPT_HEADER => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_RETURNTRANSFER => true,
			);
	
			curl_setopt_array($ch, $options);
			$output = curl_exec($ch);
			curl_close($ch);
	
			if ($output === false) { print curl_error($ch); }
	
			$outputarr = json_decode($output);
			$arr = $this->object2array($outputarr);

			if (!$strip) { return $arr; }
			if ($arr) { $arr = array_shift($arr['Body']); }
	
			return $arr;
		}

		function object2array($object)
		{
			$return = NULL;
	      
			if(is_array($object))
			{
				foreach($object as $key => $value)
					$return[$key] = $this->object2array($value);
			}
			else
			{
				$var = get_object_vars($object);
	          
			if($var)
			{
				foreach($var as $key => $value)
				$return[$key] = ($key && !$value) ? NULL : $this->object2array($value);
			}
				else return $object;
			}
	
			return $return;
		}
		
		function zimbraSoapCheckService() {
			
			$soapurl = 'http://'.$this->Hostname.'/';

			return file_exists($soapurl);

		} 


}


?>