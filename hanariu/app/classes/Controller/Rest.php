<?php //namespace Controller;

class Controller_Rest extends \Core\Controller_Restful {

 /*   public $indata; // for POST and PUT

    function before() {
        parent::before();
        echo "<pre>controller parameters:<br/>";
        print_r($this->request->param());
        $this->indata = file_get_contents('php://input');
    }   

    function action_index() {
        echo 'action_index(): GET';
    }

    function action_create() {
        echo 'action_create(): POST<br/>';
        echo "indata<br/>";
        print_r($this->indata); 

    }
    function action_update() {
        echo 'action_update(): PUT<br/>';
        echo "indata<br/>";
        print_r($this->indata);         
    }

    function action_delete() {
        echo 'action_delete(): DELETE';
    }
    */
 	private $parameter1;
	private $parameter2;
	private $parameter3;
	
	public function before() {
		parent::before();
		
		//------------------------------------------------------------------------------
		// Get some request parameters
		
		$this->parameter1 = $this->request->param('par1');
		$this->parameter2 = $this->request->param('par2');
		$this->parameter3 = $this->request->param('par3');
		
		//------------------------------------------------------------------------------
		// Init authentication 
		//\HttpAuth::basic_http_auth($this, 'basic_auth_callback');
		// or...	
		//\HttpAuth::header_token_auth($this, 'token_auth_callback', \HttpAuth::X_AUTH_TOKEN);
	}
	
	//-----------------------------------------------------------------------------
	// Authentication callbacks
	/*	
	static public function basic_auth_callback($username, $password) {
		// this method should be overridden
		return true; //($username == 'jonas' AND $password = 'cambiata');
	}
	*/
	// or...
	/*
	static public function token_auth_callback($token) {
		// this method should be overridden		
		return ($token == 'abc123');
	}
	*/	
	
	//-------------------------------------------------------------
	//-------------------------------------------------------------
	//-------------------------------------------------------------
	
	// GET
	public function action_index(){  
		
		// Create some output data
		$output_data = array('Hello' => 'World!');
		
		$this->rest_output($output_data);
	}

	// POST
	function action_create() 
	{
		// Do whatever needed with incoming data:
		// $this->input_content  -> insert to database or something...
		
		// Create some output data		
		$output_data = new StdClass();		
		$output_data->monitor_incoming = $this->input_content;
		$output_data->test = 'ABC123';
		$output_data->hobbies = array('Knitting', 'Cliffhanging');
		
		$this->rest_output($output_data);
	}	
	
	// PUT
	function action_update() 
	{
		// Do whatever needed with incoming data:
		// $this->input_content  -> update database or something...
		
		$this->rest_output('PUT');
	}	

	// DELETE
	function action_delete() 
	{
		$this->rest_output('DELETE');
	}		

}