<?php class globalListCtrl extends appCtrl {


    public $catModule;
    public $queModule;    

    public function __construct()
    {
        $this->catModule = $this->load('module', 'category');
        $this->queModule = $this->load('module', 'questions');
    }


    public function index()
    {

        $categories = $this->catModule->flatJoinList();
        $poolSummary = $this->queModule->summaryCount();
        $data['categories'] = $categories;
        $data['pool'] = $poolSummary;

        return View::responseJson($data, 200);
    	
    }




}