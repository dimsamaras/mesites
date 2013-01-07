<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spitogatos extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	/*
	* Spitogatos URL: http://webservices.spitogatos.gr/listingSync/v1_0
	* API KEY: A12312LD8756Y4IWP2F4KGSMV
	*/
	protected $api_key, $server_url, $username,$password;

	public function __construct() 
	{
		parent::__construct();
		// $api = A12312LD8756Y4IWP2F4KGSMV;
		$this->api_key = 'A12312LD8756Y4IWP2F4KGSMV';
		$this->server_url = 'http://webservices.spitogatos.gr/listingSync/v1_0';
		$this->username = 'projectteam';
		$this->password = '$*z1$*';
		//$this->server_port = '1510128';

		

	}

	public function index()
	{	

		// $this->load->library('zend');
		// $this->load->library('Zend/XmlRpc/Client');

		// $client = new Zend_XmlRpc_Client('http://framework.zend.com/xmlrpc');
 
		// echo $client->call('test.sayHello');

		//$client = new Zend_XmlRpc_Client('http://framework.zend.com/xmlrpc');

		//echo $client->call('test.sayHello');
		// $listingid = 1510128;
		// echo 'Key:'.$this->api_key.'<br />';
		// echo 'Username:'.$this->username.'<br />';
		// echo 'Password:'.$this->password.'<br />';

		$this->xmlrpc->server("http://webservices.spitogatos.gr/listingSync/v1_0");
		$this->xmlrpc->method("sync.getListing");
		// $request = array(
  //                array(
  //                      array(
  //                            'user'=>'rj', 
  //                            'api_key'=>'b25b959554ed76058ac220b7b2e0a026'
  //                           ),
  //                      'struct'
  //                     )
  //               );
		// $this->xmlrpc->request($request);

		$request = array(
					array('A12312LD8756Y4IWP2F4KGSMV','string'),
					array('projectteam','string'),
					array('$*z1$*','string'),
					array(1510128, 'int'),
					'struct'
					);

		// echo $request;
		//$request = array('A12312LD8756Y4IWP2F4KGSMV', 'projectteam', '$*z1$*', 1510128);
		$this->xmlrpc->request($request);
		//$this->xmlrpc->set_debug(TRUE);

		if ( ! $this->xmlrpc->send_request())
		 {
		 	echo $this->xmlrpc->display_error();
		 }

		//$client = new Zend_XmlRpc_Client('link to XML-RPC server',1510128);
		//print_r($client->call('sync.getListing',array($appkey,$username,
		//$password,$listingid)));

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */