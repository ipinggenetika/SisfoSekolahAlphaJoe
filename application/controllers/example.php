<?php

class Example extends Controller {

	function Example()
	{
		parent::Controller();

		$this->load->helper('html');
	}

	function index()
	{
		$this->load->view('example');
	}
}

?>