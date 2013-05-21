<?php namespace Core;

/**
 * Quickly assemled rest controller based on Kohana 3's Kohana_Controller_REST
 * https://github.com/kohana/core/blob/3.1%2Fmaster/classes/kohana/controller/rest.php
 * 
 * Added functionality from FuelPHP's Controller_Rest
 * https://github.com/fuel/fuel/blob/develop/fuel/core/classes/controller/rest.php
 * 
 * All credits to Kohana and FuelPHP teams!
 * Jonas Nyström - cambiata
 */

class Controller_Restful  extends \Core\Controller_Restler {
	
	public $output_format = 'jsonp'; // default
	
	public $input_content; // For handling incoming data from POST and PUT
	public $input_content_type;
	public $input_content_length;
	
	/**
	 * Init rest controller by checking format for output data and reading input data content.
	 */
	public function before() {
		parent::before();		
		
		$format = $this->request->param('format');
	
		// get output format from route file extension
		if (!empty($format))
		{
			$this->output_format = $format;
		}

		// get intput data for POST and PUT
		$this->input_content = $this->request->body(); 		
		$this->input_content_type = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : FALSE;
		$this->input_content_length = (isset($_SERVER['CONTENT_LENGTH'])) ? $_SERVER['CONTENT_LENGTH'] : FALSE;
		
	}

	//------------------------------------------------------------------------------	
	
	/**
	 * Restful GET - this method should be overridden! 
	 */
	public function action_index() 
	{
		$this->rest_output(array(
			'GET example!', 
		));
	}
	
	/**
	 * Restful POST - this method should be overridden!
	 */
	function action_create() 
	{
		$this->rest_output(array(
			'POST example! (Returns some info about incoming data, just for reference!)', 
			'CONTENT_TYPE:'.$this->input_content_type, 
			'CONTENT_LENGTH:'.$this->input_content_length,		
			'CONTENT:'.$this->input_content,
		));
	}
	
	/**
	 * Restful PUT - this method should be overridden!
	 */
	function action_update() 
	{
		$this->rest_output(array(
			'PUT example!  (Returns some info about incoming data, just for reference!)', 
			'CONTENT_TYPE:'.$this->input_content_type, 
			'CONTENT_LENGTH:'.$this->input_content_length, 
			'CONTENT:'.$this->input_content,
		));
	}

	/**
	 * Restful DELETE - this method should be overridden!
	 */
	function action_delete() 
	{
		$this->rest_output(array('DELETE example'));
	}	

	//-----------------------------------------------------------------------------------------
	
	/**
	 * Handling of output data set in action methods with $this->rest_output($data).
	 *  
	 * @param array|object $data
	 * @param int $http_status
	 */
	protected function rest_output($data = array(), $http_status = 200) {
		
		if (empty($data)) {
			$this->response->status(404);
			return;
		}		

		$this->response->status($http_status);
		
		$mime = \Utils::mime_by_ext($this->output_format);
		
		$format_method = '_format_' . $this->output_format;

		// If the format method exists, call and return the output in that format
		if (method_exists($this, $format_method)) {
			$output_data = $this->$format_method($data);

			$this->response->headers('content-type', $mime);
			$this->response->headers('content-length', (string) \strlen($output_data));
			$this->response->body($output_data);
			
		} else 	{
			$this->response->body(\var_export( $data, true));
		}
	}	
	
	// ------------------------------------------------------------------------------------
	// Format the output data to requested type 
	
	private function _format_xml($data = array(), $structure = NULL, $basenode = 'xml')	{
		return '';//Cambiata_Format::to_xml($data, $structure, $basenode);
	}
	
	private function _format_json($data = array()) {
		return \json_encode($data);
	}	

	private function _format_jsonp($data = array()) {
		return \json_encode($data);
	}	

	private function _format_html($data = array()) {
		return '<pre>' . \var_export($data, true) . '</pre>';
	}	
	
}
