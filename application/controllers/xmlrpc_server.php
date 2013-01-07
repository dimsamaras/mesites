<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Created on Aug 11, 2011 by Damiano Venturin http://www.squadrainformatica.com
//
// That's a fully functional XMLRPC server implementation
// It's a CodeIgniter 2.0 controller handling the requests performed by a XMLRPC client (look xmlrpc_client.php)
// I think it's interesting the use of reflection in listing the available XMLRPC methods (__getMyMethods)

class Xmlrpc_server extends CI_Controller {

    public $builtInMethods;

    function __construct()
    {
        parent::__construct();

        $this->builtInMethods = array();

        $this->load->library('xmlrpc');
        $this->load->library('xmlrpcs');

        // A method could be published manually using an array like this one
        // EX.:    $config['functions']['helloWorld'] = array('function' => 'Xmlrpc_server.helloWorld',
        //                                                docstring' => 'Returns an helloWorld array'        
        //                                                );
        // but it's much easier to use the automatic method __getMyMethods

        //Automatically expose xmlrpc methods by providing automatically the configuration
        $this->__getMyMethods();
        $config = array();
        $config['functions'] = $this->builtInMethods['functions'];
        $this->xmlrpcs->initialize($config);
        $this->xmlrpcs->set_debug($this->config->item('xmlrpc_debug'));        

        //Start the server
        $this->xmlrpcs->serve();    
    }    

    /**
     *
     * Analizes self methods using reflection
     * @return Boolean
     */
	private function __getMyMethods()
	{
		$reflection = new ReflectionClass($this);

		//get all methods
		$methods = $reflection->getMethods();
		$this->builtInMethods = array();

		//get properties for each method
		if(!empty($methods))
		{
			foreach ($methods as $method) {
				if(!empty($method->name))
				{
					$methodProp = new ReflectionMethod($this, $method->name);

					//saves all methods names found
					$this->builtInMethods['all'][] = $method->name;

					//saves all private methods names found
					if($methodProp->isPrivate())
					{
						$this->builtInMethods['private'][] = $method->name;
					}

					//saves all private methods names found
					if($methodProp->isPublic())
					{
						$this->builtInMethods['public'][] = $method->name;

						// gets info about the method and saves them. These info will be used for the xmlrpc server configuration.
						// (only for public methods => avoids also all the public methods starting with '__')
						if(!preg_match('/^__/', $method->name, $matches))
						{
							// -method name
							$this->builtInMethods['functions'][$method->name]['function'] = $reflection->getName().'.'.$method->name;

							// -method docstring
							$this->builtInMethods['functions'][$method->name]['docstring'] =  $this->__extractDocString($methodProp->getDocComment());
						}
					}
				}
			}
		} else {
			return false;
		}
		return true;
	}

    /**
     *
     * Manipulates a DocString and returns a readable string
     * @param String $DocComment
     * @return Array $_tmp
     */
    private function __extractDocString($DocComment)
    {
        $split = preg_split("/rn|n|r/", $DocComment);
        $_tmp = array();
        foreach ($split as $id => $row)
        {
            //clean up: removes useless chars like new-lines, tabs and *
            $_tmp[] = trim($row, "* /ntr");
        }            
        return trim(implode("n",$_tmp));
    }

    /**
     *
     * Authentication method for the xml-rpc client
     * @param array $request    Array with the following items: array("username", "password")
     * @return boolean
     */
    private function __auth($request)
    {
        //presets
        $username = trim($this->config->item('xmlrpc_username'));
        $password = trim($this->config->item('xmlrpc_password'));

        //getting sent parameters
        $parameters = $request->output_parameters();
        $givenUsername = trim($this->__clean($parameters['0']));    
        $givenPassword = trim($this->__clean($parameters['1']));

        //if($givenUsername != $username) return false;
        if(md5($givenPassword) != $password) return false;

        return true;
    }

    private function __clean($request)
    {
        return trim(addslashes($request));    
    }

    /**
     * Test method meant to be used by developers to test the XML-RPC client side.
     * Requires no authentication
     * @return array    Array with 4 items: 'Hello', 'World', 'and', 'everybody else'
     */    
    public function helloWorld()
    {                    
        $words = array('Hello', 'World', 'and', 'everybody else');

        $response = array(
                            $words,
                            'struct'
                         );
        return $this->xmlrpc->send_response($response);
    }

    /**
     *
     * Test method meant to be used by developers to test the XML-RPC client side.
     * Returns a multiple array as response. It's interesting to see how the response must be wrapped into several arrays as
     * described in the CI2 documentation http://codeigniter.com/user_guide/libraries/xmlrpc.html.
     * Requires authentication.
     * @return array    Array with 2 items each one composed by 4 items
     */    
    public function getContacts($request)
    {
        //auth check        
        $result = $this->__auth($request);
        if($result == false)
        {
            //$response = array($request->output_parameters(),'struct');
            //return $this->xmlrpc->send_response($response);        
            return $this->xmlrpc->send_error_message('100', 'Wrong credentials');    
        }

        $response = array(
                            array('0' => array(
                                            array(
                                                 'first_name' => array('John', 'string'),
                                                 'last_name' => array('Doe', 'string'),
                                                 'member_id' => array(123435, 'int')),'struct'
                                              ),
                                  '1' => array(
                                            array(
                                                 'first_name' => array('Robert', 'string'),
                                                 'last_name' => array('Brown', 'string'),
                                                 'member_id' => array(123436, 'int')),'struct'
                                              )
                                 ), 'struct'); 

        return $this->xmlrpc->send_response($response);        
    }
}

/* End of xmlrpc_server.php */