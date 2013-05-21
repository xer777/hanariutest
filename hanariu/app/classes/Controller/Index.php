<?php //namespace Controller;

class Controller_Index extends  \Core\Controller_Template  { // \Controller { //

	public $template = 'templates/layout';

	public function action_index()
	{
		//echo \Core\Debug::dumpy(TRUE);
		//echo \URL::title2('aa');
		//$this->response->body('hello, world!<br>Rendered in {execution_time} with {memory_usage} of memory.');
		$this->template->content = \Core\View::factory('main');
	}
}
