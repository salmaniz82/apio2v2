<?php 

if ( !defined('ABSPATH') )
    die('Forbidden Direct Access');

class globalListCtrl extends appCtrl {


    public $catModule;
    public $queModule;    

    public function __construct()
    {
        $this->catModule = $this->load('module', 'category');
        $this->queModule = $this->load('module', 'questions');
    }


    public function index()
    {


        if(!jwACL::isLoggedIn()) 
            return $this->uaReponse();


        


        $categories = $this->catModule->flatJoinList();
        $poolSummary = $this->queModule->summaryCount();
        $data['categories'] = $categories;
        $data['pool'] = $poolSummary;

        return View::responseJson($data, 200);
    	
    }




}