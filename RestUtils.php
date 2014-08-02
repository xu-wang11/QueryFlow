<?php
require_once ('RestRequest.php');
class RestUtils {
	public static function processRequest() {
		// handle route
		$url = strtolower ( $_SERVER ['REQUEST_URI'] );
		$params = explode ( '/', $url );
		$len = count ( $params );
		$i = 0;
		// query/srcip/srcport/dstip/dstport/
		if ($params [3] == "query") {
			if (count ( $params ) < 8) {
				RestUtils::sendResponse ( 404 );
			}
			$srcip = $params [4];
			$srcport = $params [5];
			$dstip = $params [6];
			$dstport = $params [7];
			$flows = RestUtils::fetchFlow ( "http://localhost:8080", $srcip, $srcport, $dstip, $dstport );
		} 		// filter/enable or disable/srcip/dstip/
		else if ($params [3] == "filter") {
			if ($params [4] == "enable") {
				$action = "ALLOW";
			} else if ($params [4] == "disable") {
				$action = "DENY";
			} else {
				RestUtils::sendResponse ( 404 );
				return;
			}
			$protocol = $params[5];
			$srcip = $params [6];
			$dstip = $params [7];
			$flows = RestUtils::installFilter ( 'http://localhost:8080', $srcip, $dstip, $action, $protocol );
		} 		// delete all firewall rule.
		else if ($params [3] == "delete") {
			RestUtils::deleteRules ( 'http://localhost:8080' );
			return;
		} 		// /pushflow/srcip/srcport/dstip/dstport/
		else if ($params [3] == "pushflow") {
			$srcip = $params [4];
			$srcport = $params [5];
			$dstip = $params [6];
			$dstport = $params [7];
			$flows = RestUtils::pushFlow ( 'http://localhost:8080', $srcip, $srcport, $dstip, $dstport );
		}
		else if($params[3] == "deleteflow"){
			$srcip = $params [4];
			$srcport = $params [5];
			$dstip = $params [6];
			$dstport = $params [7];
			$flows = RestUtils::deleteflow('http://localhost:8080', $srcip, $srcport, $dstip, $dstport );
			
			
		}
		else if($params[3] == "deletefilter"){
			$srcip = $params[4];
			$dstip = $params[5];
			$flows = RestUtils::deleteRules('http://localhost:8080', $srcip, $dstip);
		} 
		else {
			RestUtils::sendResponse ( 404 );
			return;
		}
		return $flows;
	}
	
	public static function getAllflow($host= "http://localhost:8080")
	{
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json'
		);
		$device = curl_init ( $host . "/wm/staticflowentrypusher/list/all/json" );
		curl_setopt ( $device, CURLOPT_POST, false );
		curl_setopt ( $device, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $device, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $device, CURLOPT_SSL_VERIFYPEER, false );
		$nodes = curl_exec ( $device );
		// Closing
		curl_close ( $device );
		$node_array = json_decode ( $nodes, true );
		return $node_array;
		
	}
	
	/**
	 * 
	 * @param string $host
	 * @param string $srcip
	 * @param string $srcport
	 * @param string $dstip
	 * @param string $dstport
	 * @return multitype:
	 */
	public static function deleteflow($host = "http://localhost:8080", $srcip, $srcport, $dstip, $dstport)
	{
		$flows = RestUtils::getAllflow($host);
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json'
		);
		$result = array();
		foreach($flows as $flow)
		{
			foreach ($flow as $flow_name=>$flow_content)
			{
			if(($srcip == '*' || $srcip == $flow_content['match']['networkSource'])
				&&($dstip == '*' || $dstip == $flow_content['match']['networkDestination'])
				&&($srcport == '*' || $srcport == $flow_content['match']['transportSource'])
				&&($dstport == '*' || $dstport == $flow_content['match']['transportDestination']))
			{
				$json = array (
						"name" => $flow_name
				);
				$ch = curl_init ( $host . "wm/staticflowentrypusher/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
				curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_exec ( $ch );
				// Closing
				$result[$flow_name] = 'entry deleted.';
				curl_close ( $ch );
			}
			}
		}
		return $result;   
	}
	
	/**
	 * get switch information from floodlight controller /wm/core/controller/switches/json
	 * 
	 * @param string $host        	
	 * @return multitype: switch json
	 */
	public static function getSwitches($host) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $host . "/wm/core/controller/switches/json" );
		curl_setopt ( $device, CURLOPT_POST, false );
		curl_setopt ( $device, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $device, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $device, CURLOPT_SSL_VERIFYPEER, false );
		$nodes = curl_exec ( $device );
		// Closing
		curl_close ( $device );
		$node_array = json_decode ( $nodes, true );
		return $node_array;
	}
	
	/**
	 * push flow to controller
	 * 
	 * @param string $host        	
	 * @param string $srcip        	
	 * @param string $srcport        	
	 * @param string $dstip        	
	 * @param string $dstport        	
	 * @return return status json
	 */
	public static function pushFlow($host, $srcip, $srcport, $dstip, $dstport) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$switches = RestUtils::getSwitches ( $host );
		foreach ( $switches as $switch ) {
			$json = array ();
			$json ['switch'] = $switch ['dpid'];
			if ($srcip != "*")
				$json ['src-ip'] = $srcip;
			if ($dstip != "*")
				$json ['dst-ip'] = $dstip;
			if ($srcport != "*")
				$json ['src-port'] = $srcport;
			if ($dstport != "*")
				$json ['dst-port'] = $dstport;
			$json ['name'] = RestUtils::generateRandomString ();
			$json ['cookie'] = '0';
			$json ['priority'] = '1';
			$json ['ether-type'] = '2048';
			$json ['protocol'] = '6';
			$json ['actions'] = 'output=4';
			$json ['active'] = 'true';
			
			$ch = curl_init ( $host . "wm/staticflowentrypusher/json" );
			curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec ( $ch );
			// Closing
			curl_close ( $ch );
			return json_decode ( $result );
		}
	}
	
	/**
	 * get all firewall rules
	 * 
	 * @param string $host        	
	 * @return json rules
	 */
	public static function getRules($host) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $host . "wm/firewall/rules/json" );
		curl_setopt ( $device, CURLOPT_POST, false );
		curl_setopt ( $device, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $device, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $device, CURLOPT_SSL_VERIFYPEER, false );
		$nodes = curl_exec ( $device );
		// Closing
		curl_close ( $device );
		$node_array = json_decode ( $nodes, true );
		return $node_array;
	}
	
	/**
	 * delete all rules.
	 * 
	 * @param string $host        	
	 * @return no return
	 */
	public static function deleteRules($host, $srcip, $dstip) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $host . "wm/firewall/rules/json" );
		curl_setopt ( $device, CURLOPT_POST, false );
		curl_setopt ( $device, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $device, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $device, CURLOPT_SSL_VERIFYPEER, false );
		$nodes = curl_exec ( $device );
		// Closing
		curl_close ( $device );
		$node_array = json_decode ( $nodes, true );
		$count = 0;
		foreach ( $node_array as $rule ) {
			if(($srcip == '*' || $rule['nw_src_prefix'] == $srcip)
				&&($dstip == '*' || $rule['nw_dst_prefix'] == $dstip))
			{
			$json = array (
					"ruleid" => $rule ['ruleid'] 
			);
			$ch = curl_init ( $host . "wm/firewall/rules/json" );
			curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_exec ( $ch );
			// Closing
			curl_close ( $ch );
			$count ++;
		}
		}
		$result = array("status"=>$count." rules deleted.");
		return $result;
	}
	
	/**
	 * get all devices; this method didn't work well because the controller don't always record all devices if some device don't work for some time.
	 * 
	 * @param string $host        	
	 * @param
	 *        	json all Device
	 */
	public static function getDevices($host) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $host . "wm/device/" );
		curl_setopt ( $device, CURLOPT_POST, false );
		curl_setopt ( $device, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $device, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $device, CURLOPT_SSL_VERIFYPEER, false );
		$nodes = curl_exec ( $device );
		// Closing
		curl_close ( $device );
		$node_array = json_decode ( $nodes, true );
		return $node_array;
	}
	
	/**
	 * install filter
	 * 
	 * @param string $host,
	 *        	controller server ip.
	 * @param string $srcip        	
	 * @param sting $destip        	
	 * @return json status
	 * @todo the "*" handle isn't complemented well.
	 */
	public static function installFilter($host, $srcip, $destip, $action, $protocol) {
	
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		$devices = RestUtils::getDevices ( $host );
		$rules = RestUtils::getRules ( $host );
		$ipaddrs = array ();
		foreach ( $devices as $ipaddr ) {
			$ipaddrs [] = $ipaddr ['ipv4'];
		}
		$status = "";
		if ($srcip == "*") {
			$srcarray = array ();
			foreach ( $ipaddrs as $ipaddr ) {
				foreach ( $ipaddr as $ip )
					$srcarray [] = $ip;
			}
		} else {
			foreach ( $ipaddrs as $ips ) {
				foreach ( $ips as $ip ) {
					if ($ip == $srcip) {
						$srcarray = array (
								$ip 
						);
						break;
					}
				}
			}
		}
		if (count ( $srcarray ) == 0) {
			$status .= "no Src found; ";
		}
		if ($destip == "*") {
			$destarray = array ();
			foreach ( $ipaddrs as $ipaddr ) {
				foreach ( $ipaddr as $ip )
					$destarray [] = $ip;
			}
		} else {
			foreach ( $ipaddrs as $ips ) {
				foreach ( $ips as $ip ) {
					if ($ip == $destip) {
						$destarray = array (
								$ip 
						);
						break;
					}
				}
			}
		}
		if (count ( $destip ) == 0) {
			$status .= "No dest found. ";
		}
		foreach ( $srcarray as $src ) {
			foreach ( $destarray as $dest ) {
				// delete out-date rules
				foreach ( $rules as $rule ) {
					if ($rule ['nw_src_prefix'] == $src && $rule ['nw_dst_prefix'] == $dest) {
						$json = array (
								"ruleid" => $rule ['ruleid'] 
						);
						$ch = curl_init ( $host . "wm/firewall/rules/json" );
						curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
						curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
						curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
						curl_exec ( $ch );
						// Closing
						curl_close ( $ch );
					}
				}
				//
				if(strpos($src, "/") ===FALSE)
				{
					$src = $src."/32";
				}
				if(strpos($dest, "/") === FALSE)
				{
					$dst = $dst."/32";
				}
				$install_filter = array (
						"src-ip" => $src,
						"dst-ip" => $dest,
						"action" => $action,
						"nw-proto"=>$protocol 
				);
				$data_string = json_encode ( $install_filter );
				$ch = curl_init ( $host . "wm/firewall/rules/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
						'Content-Type: application/json',
						'Content-Length: ' . strlen ( $data_string ) 
				) );
				$result = curl_exec ( $ch );
				curl_close ( $ch );
				$result = json_decode ( $result, true );
				$install_filter = array (
						"src-ip" => $src . "/32",
						"dst-ip" => $dest . "/32",
						"action" => $action,
						"dl-type" => "ARP" 
				);
				$data_string = json_encode ( $install_filter );
				$ch = curl_init ( $host . "wm/firewall/rules/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
						'Content-Type: application/json',
						'Content-Length: ' . strlen ( $data_string ) 
				) );
				$result = curl_exec ( $ch );
				curl_close ( $ch );
				$result = json_decode ( $result, true );
				// $status .= $result['status'];
				$install_filter = array (
						"src-ip" => $src . "/32",
						"dst-ip" => $dest . "/32",
						"action" => $action,
						"nw-proto" => "ICMP" 
				);
				$data_string = json_encode ( $install_filter );
				$ch = curl_init ( $host . "wm/firewall/rules/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
						'Content-Type: application/json',
						'Content-Length: ' . strlen ( $data_string ) 
				) );
				$result = curl_exec ( $ch );
				curl_close ( $ch );
				$result = json_decode ( $result, true );
				// $status .= $result['status'];
			}
		}
		if (strlen ( $status ) == 0)
			$status = "success";
		$flows = array (
				"status" => $status 
		);
		return $flows;
	}
	// query/srcip/dstip
	public static function fetchFlow($host, $srcip, $srcport, $destip, $destport) {
		$len = strlen ( $host );
		if ($host [$len - 1] != '/')
			$host = $host . "/";
		$headers = array (
				'Content-Type: application/json' 
		);
		// -------get all flow list
		$flow_url = curl_init ( $host . "wm/core/switch/all/flow/json" );
		curl_setopt ( $flow_url, CURLOPT_POST, false );
		curl_setopt ( $flow_url, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $flow_url, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $flow_url, CURLOPT_SSL_VERIFYPEER, false );
		$flow = curl_exec ( $flow_url );
		curl_close ( $flow_url );
		$flow_array = json_decode ( $flow, true );
		$status = "Sucess";
		// filter flows information;
		$result = array (
				'flows' => array (),
				'status' => $status 
		);
		foreach ( $flow_array as $switch_flows ) {
			foreach ( $switch_flows as $flow_ele ) {
				try {
					$flow_src = $flow_ele ['match'] ['networkSource'];
					$flow_dest = $flow_ele ['match'] ['networkDestination'];
					$flow_src_port = $flow_ele ['match'] ['transportSource'];
					$flow_dst_port = $flow_ele ['match'] ['transportDestination'];
					if (($srcip == '*' || $srcip == $flow_src) && ($srcport == "*" || $srcport == $flow_src_port)) {
						if (($destip == '*' || $destip == $flow_dest) && ($destport == "*" || $destport == $flow_dst_port)) {
							$result ['flows'] [] = $flow_ele;
						}
					}
				} catch ( Exception $e ) {
					echo "no flow attr";
					$result ['status'] [] = "failed";
				}
			}
		}
		return $result;
	}
	
	/**
	 * send response.
	 * 
	 * @param number $status        	
	 * @param string $body        	
	 * @param string $content_type        	
	 */
	public static function sendResponse($status = 200, $body = '', $content_type = 'text/html') {
		$status_header = 'HTTP/1.1 ' . $status . ' ' . RestUtils::getStatusCodeMessage ( $status );
		// set the status
		header ( $status_header );
		// set the content type
		header ( 'Content-type: ' . $content_type );
		
		// pages with body are easy
		if ($body != '') {
			// send the body
			echo $body;
			exit ();
		} 		// we need to create the body if none is passed
		else {
			// create some body messages
			$message = '';
			
			// this is purely optional, but makes the pages a little nicer to read
			// for your users. Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch ($status) {
				case 401 :
					$message = 'You must be authorized to view this page.';
					break;
				case 404 :
					$message = 'The requested URL ' . $_SERVER ['REQUEST_URI'] . ' was not found.';
					break;
				case 500 :
					$message = 'The server encountered an error processing your request.';
					break;
				case 501 :
					$message = 'The requested method is not implemented.';
					break;
			}
			
			// servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER ['SERVER_SIGNATURE'] == '') ? $_SERVER ['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER ['SERVER_NAME'] . ' Port ' . $_SERVER ['SERVER_PORT'] : $_SERVER ['SERVER_SIGNATURE'];
			
			// this should be templatized in a real-world solution
			$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
								<title>' . $status . ' ' . RestUtils::getStatusCodeMessage ( $status ) . '</title>
							</head>
							<body>
								<h1>' . RestUtils::getStatusCodeMessage ( $status ) . '</h1>
								<p>' . $message . '</p>
								<hr />
								<address>' . $signature . '</address>
							</body>
						</html>';
			
			echo $body;
			exit ();
		}
	}
	/**
	 * generate a random string.
	 * 
	 * @param number $length        	
	 * @return string
	 */
	public static function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for($i = 0; $i < $length; $i ++) {
			$randomString .= $characters [rand ( 0, strlen ( $characters ) - 1 )];
		}
		return $randomString;
	}
	/**
	 *
	 * @param unknown $status        	
	 * @return string
	 */
	public static function getStatusCodeMessage($status) {
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array (
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				306 => '(Unused)',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported' 
		);
		
		return (isset ( $codes [$status] )) ? $codes [$status] : '';
	}
}

?>