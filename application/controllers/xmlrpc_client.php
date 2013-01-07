<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Created on Aug 11, 2011 2011 by Damiano Venturin http://www.squadrainformatica.com
//
// That's a fully functional XMLRPC client implementation
// It's a CodeIgniter 2.0 controller handling the requests performed by a XMLRPC client (look xmlrpc_client.php)
// I think it's interesting the use of reflection in listing the available XMLRPC methods (displayServerMethods)

class Xmlrpc_client extends CI_Controller {
    function __construct()
    {
        parent::__construct();

        $this->xmlrpc->debug = $this->config->item('xmlrpc_debug');

        $server_url = site_url('xmlrpc_server');
        $this->xmlrpc->server($server_url, 80);    
    }

    public function index()
    {
        $data['methods_list'] = $this->displayServerMethods();
        $this->load->view('xmlrpcDoc',$data);
    }

    /**
     *
     * Produces a human readable list of methods available on the server side
     */
    private function displayServerMethods()
    {
        //get methods list
        $this->xmlrpc->method('system.listMethods');
        $request = array();
        $methods_html = '';
        $methods = $this->makeRequest($request,false);

        //show the docstring for the method
        if(count($methods)>0)
        {
            $this->xmlrpc->method('system.methodHelp');
            $methods_html .= '<dl>';
            foreach ($methods as $method) {        
                $request = array($method,'struct');
                $docstring = $this->makeRequest($request,false);
                if(empty($docstring))
                {
                    $methods_html .= '<dt>'.$method.'</dt><dd>No description available</dd>';
                } else {
                    $methods_html .= '<dt>'.$method.'</dt><dd>'.$docstring.'</dd>';
                }
            }    
            $methods_html .= '</dl>';
        }   
        return $methods_html;
    }

    function makeRequest($request=null,$output=true)
    {
        if(!is_array($request) or empty($request) ) $request = array();    

        //performs the request
        $this->xmlrpc->request($request);

        //handles the request
        if ( ! $this->xmlrpc->send_request())
        {
            echo $this->xmlrpc->display_error();
        }
        else
        {
            $res = $this->xmlrpc->display_response();
            if($output)
            {
                echo '<pre>';
                print_r($res);
                echo '</pre>';
            } else {
                return $res;
            }
        }
    }

    function helloWorld()
    {
        $this->xmlrpc->method('helloWorld');
        $request = array();
        return $this->makeRequest($request);
    }    

    function getContacts()
    {
        $this->xmlrpc->method('getContacts');
        $request = array('user','password');
        return $this->makeRequest($request);
    }        

}

/* End of xmlrpc_client.php */