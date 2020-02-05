<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

require ABSPATH.'app/external/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

use Dompdf\Options;

class pdfCtrl extends appCtrl {



	public function testInstaller()
	{
		
		$DomPdf = new Dompdf();	

		$html = Route::getCurlHtml(SITE_URL.'pages/staticpage');

		$DomPdf->loadHtml($html);

		$DomPdf->setPaper('A4', 'landscape');

		$DomPdf->render();

		/*
	
		'Attachment'=> 0 = read in browswer
		'Attachment'=> 1 = dowload

		*/

		

		$DomPdf->stream('weblession', array('Attachment'=> 0));		


		
		
	}


	public function getDomPDFInstance()
	{



	}


	public function scorecardPDF()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		$quiz_id = (int) Route::$params['quiz_id'];

		$attempt_id = (int) Route::$params['attempt_id'];


		$options = new Options();

		$options->setIsRemoteEnabled(true);


		$DomPdf = new Dompdf();	

		$DomPdf->setOptions($options);

		$html = Route::getCurlHtml(SITE_URL.'pages-scoresheet/'.$quiz_id.'/'.$attempt_id);

		$DomPdf->loadHtml($html);

		$DomPdf->setPaper('A4', 'portrait');

		$DomPdf->render();


		$DomPdf->stream('scorecard', array('Attachment'=> 1));



	}


	





}




