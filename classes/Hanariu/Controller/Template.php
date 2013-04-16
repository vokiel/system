<?php namespace Hanariu\Controller;

abstract class Template extends \Hanariu\Controller {

	public $template = 'template';
	public $auto_render = TRUE;

	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{
			$this->template = \Hanariu\View::factory($this->template);
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
