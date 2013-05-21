<?php namespace Core;

abstract class Controller_Template extends \Controller
{

	public $template = 'template';
	public $auto_render = TRUE;

	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{
			$this->template = \Core\View::factory($this->template);
		}
	}

	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			$this->response->body($this->template->render());
		}

		parent::after();
	}

}
