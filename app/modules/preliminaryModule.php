<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');



class preliminaryModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'preliminary';
	}



	public function addTickets($dataPayload)
	{


		$dataPayload['ticket'] = strrev($dataPayload['ticket']);


		if($this->DB->insert($dataPayload))
		{
			return true;
		}

		return false;

	}


	public function removeTicket($dataPayload)
	{


		if($this->DB->delete($dataPayload))
		{
			return true;
		}

		return false;


	}




}