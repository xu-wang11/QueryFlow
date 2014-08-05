<?php

namespace Auth;

use Luracast\Restler\iAuthenticate;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Storage\Pdo;
use OAuth2\Server as OAuth2Server;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\Request;
use OAuth2\Response;
use Behat\Behat\Exception\Exception;

/**
 * Class Server
 *
 * @package OAuth2
 *         
 */
class Server implements iAuthenticate {
	public $host = "http://localhost:8080/";
	/**
	 *
	 * @var OAuth2Server
	 */
	protected static $server;
	/**
	 *
	 * @var Pdo
	 */
	protected static $storage;
	/**
	 *
	 * @var Request
	 */
	protected static $request;
	public function __construct() {
		$dsn = 'mysql:dbname=senss;host=localhost';
		$username = 'root';
		$password = '#wangxu1993';
		// end with /
		                                  // $dir = __DIR__ . '/db/';
		                                  // $file = 'oauth.sqlite';
		                                  // if (!file_exists($dir . $file)) {
		                                  // include_once $dir . 'rebuild_db.php';
		                                  // }
		                                  // static::$storage = new Pdo(
		                                  // array('dsn' => 'sqlite:' . $dir . $file)
		                                  // );
		static::$storage = new Pdo ( array (
				'dsn' => $dsn,
				'username' => $username,
				'password' => $password 
		) );
		// create array of supported grant types
		$grantTypes = array (
				'authorization_code' => new AuthorizationCode ( static::$storage ),
				'user_credentials' => new UserCredentials ( static::$storage ),
				'client_credentials' => new ClientCredentials(static::$storage)
		);
		static::$request = Request::createFromGlobals ();
		static::$server = new OAuth2Server ( static::$storage, array (
				'enforce_state' => true,
				'allow_implicit' => true 
		), $grantTypes );
	}
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for($i = 0; $i < $length; $i ++) {
			$randomString .= $characters [rand ( 0, strlen ( $characters ) - 1 )];
		}
		return $randomString;
	}
	/**
	 * Stage 1: Client sends the user to this page
	 *
	 * User responds by accepting or denying
	 *
	 * @view oauth2/server/authorize.twig
	 * @format HtmlFormat
	 */
	public function authorize() {
		static::$server->getResponse ( static::$request );
		// validate the authorize request. if it is invalid,
		// redirect back to the client with the errors in tow
		if (! static::$server->validateAuthorizeRequest ( static::$request )) {
			static::$server->getResponse ()->send ();
			exit ();
		}
		return array (
				'queryString' => $_SERVER ['QUERY_STRING'] 
		);
	}
	
	/**
	 * Stage 2: User response is captured here
	 *
	 * Success or failure is communicated back to the Client using the redirect
	 * url provided by the client
	 *
	 * On success authorization code is sent along
	 *
	 *
	 * @param bool $authorize        	
	 *
	 * @return \OAuth2\Response @format JsonFormat,UploadFormat
	 */
	public function postAuthorize($authorize = false) {
		static::$server->handleAuthorizeRequest ( static::$request, new Response (), ( bool ) $authorize )->send ();
		exit ();
	}
	
	/**
	 * Stage 3: Client directly calls this api to exchange access token
	 *
	 * It can then use this access token to make calls to protected api
	 *
	 * @format JsonFormat,UploadFormat
	 */
	public function postGrant() {
		static::$server->handleTokenRequest ( static::$request )->send ();
		exit ();
	}
	/**
	 * @url POST token
	 */
	public function token(){
		static::$server->handleTokenRequest(Request::createFromGlobals())->send();
	}
	/**
	 * Sample api protected with OAuth2
	 *
	 * For testing the oAuth token
	 *
	 * @access protected
	 */
	public function access() {
		return array (
				'friends' => array (
						'john',
						'matt',
						'jane' 
				) 
		);
	}
	/**
	 * Sample api protected with OAuth2
	 *
	 * For testing the oAuth token
	 * @access protected
	 * @url GET getSwitches
	 */
	public function getSwitches() {
		$headers = array (
				'Content-Type: application/json' 
		);
		
		$device = curl_init ( $this->host . "/wm/core/controller/switches/json" );
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
	 * @access protected
	 * @url GET deleteflow/{srcip}/{srcport}/{dstip}/{dstport}
	 */
	public function deleteflow($srcip, $srcport, $dstip, $dstport) {
		$flows = $this->getAllflow ( $this->host );
		
		$headers = array (
				'Content-Type: application/json' 
		);
		$result = array ();
		foreach ( $flows as $flow ) {
			foreach ( $flow as $flow_name => $flow_content ) {
				if (($srcip == '*' || $srcip == $flow_content ['match'] ['networkSource']) && ($dstip == '*' || $dstip == $flow_content ['match'] ['networkDestination']) && ($srcport == '*' || $srcport == $flow_content ['match'] ['transportSource']) && ($dstport == '*' || $dstport == $flow_content ['match'] ['transportDestination'])) {
					$json = array (
							"name" => $flow_name 
					);
					$ch = curl_init ( $this->host . "wm/staticflowentrypusher/json" );
					curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
					curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
					curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
					curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
					curl_exec ( $ch );
					// Closing
					$result [$flow_name] = 'entry deleted.';
					curl_close ( $ch );
				}
			}
		}
		return $result;
	}
	
	/**
	 * @access protected
	 * push flow to controller
	 * @url GET addflow/{srcip}/{srcport}/{dstip}/{dstport}
	 */
	public function addflow($srcip, $srcport, $dstip, $dstport) {
		$headers = array (
				'Content-Type: application/json' 
		);
		$switches = $this->getSwitches ();
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
			$json ['name'] = Server::generateRandomString ();
			$json ['cookie'] = '0';
			$json ['priority'] = '1';
			$json ['ether-type'] = '2048';
			$json ['protocol'] = '6';
			$json ['actions'] = 'output=4';
			$json ['active'] = 'true';
			
			$ch = curl_init ( $this->host. "wm/staticflowentrypusher/json" );
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
	 * @access protected
	 * @return mixed
	 */
	public function getRules() {
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $this->host . "wm/firewall/rules/json" );
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
	 * @access protected
	 * @url GET deleterule/{srcip}/{dstip}
	 */
	public function deleteRules($srcip, $dstip) {
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $this->host . "wm/firewall/rules/json" );
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
			if (($srcip == '*' || $rule ['nw_src_prefix'] == $srcip) && ($dstip == '*' || $rule ['nw_dst_prefix'] == $dstip)) {
				$json = array (
						"ruleid" => $rule ['ruleid'] 
				);
				$ch = curl_init ( $this->host . "wm/firewall/rules/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_exec ( $ch );
				// Closing
				curl_close ( $ch );
				$count ++;
			}
		}
		$result = array (
				"status" => $count . " rules deleted." 
		);
		return $result;
	}
	
	/**
	 * get all devices; this method didn't work well because the controller don't always record all devices if some device don't work for some time.
	 * @access protected
	 * @url GET devices
	 */
	public function getDevices() {
		$headers = array (
				'Content-Type: application/json' 
		);
		$device = curl_init ( $this->host. "wm/device/" );
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
	 * @access protected
	 * @url GET filter/{action}/{protocol}/{srcip}/{destip}
	 */
	public function installFilter($action, $protocol, $srcip, $destip) {
		echo $action, $protocol, $srcip, $destip;
		$headers = array (
				'Content-Type: application/json' 
		);
		$srcmask = 32;
		$dstmask = 32;
		if ($srcip != "*") {
			$start = strpos ( $srcip, ":" );
			if ($start !== FALSE) {
				$srcmask = substr ( $srcip, $start + 1 );
				$srcip = substr ( $srcip, 0, $start );
			}
		}
		if ($destip != "*") {
			$start = strpos ( $destip, ":" );
			if ($start !== FALSE) {
				$dstmask = substr ( $destip, $start + 1 );
				$destip = substr ( $destip, 0, $start );
			}
		}
		
		$rules = Server::getRules ();
		// delete out-date rules
		foreach ( $rules as $rule ) {
			if (($srcip == '*' || ($rule ['nw_src_prefix'] == $srcip)) && ($destip == '*' || $rule ['nw_dst_prefix'] == $destip)) {
				$json = array (
						"ruleid" => $rule ['ruleid'] 
				);
				$ch = curl_init ( $this->host . "wm/firewall/rules/json" );
				curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $json ) );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_exec ( $ch );
				// Closing
				curl_close ( $ch );
			}
		}
		$install_filter = array ();
		if ($srcip != '*') {
			$install_filter ['src-ip'] = $srcip . '/' . $srcmask;
		}
		if ($destip != '*') {
			$install_filter ['dst-ip'] = $destip . '/' . $dstmask;
		}
		$install_filter ['action'] = $action;
		$install_filter ['nw-proto'] = $protocol;
		$data_string = json_encode ( $install_filter );
		$ch = curl_init ( $this->host . "wm/firewall/rules/json" );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-Type: application/json',
				'Content-Length: ' . strlen ( $data_string ) 
		) );
		$flow = curl_exec ( $ch );
		curl_close ( $ch );
		
		// $status .= $result['status'];
		
		return json_decode ( $flow );
	}
	
	/**
	 * @access protected
	 * 
	 * @url GET query/{srcip}/{srcport}/{destip}/{destport}
	 */
	public function fetchFlow($srcip, $srcport, $destip, $destport) {
		$headers = array (
				'Content-Type: application/json' 
		);
		// -------get all flow list
		$flow_url = curl_init ( $this->host . "wm/core/switch/all/flow/json" );
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
	 * Access verification method.
	 *
	 * API access will be denied when this method returns false
	 *
	 * @return boolean true when api access is allowed; false otherwise
	 */
	public function __isAllowed() {
		return self::$server->verifyResourceRequest ( static::$request );
	}
	public function __getWWWAuthenticateString() {
		return 'Bearer realm="example"';
	}
}
