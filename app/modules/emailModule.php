<?php 


// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';


class emailModule extends appCtrl {

	
	public function message()
	{
		echo "welcome from email module";
	}


	public function getMailer()
	{
		
		$mail = new PHPMailer(true);
		return $mail;

	}



	public function getConfigMailer()
	{

		$mail = new PHPMailer(true);
		$mail->IsMail();                                      // Set mailer to use SMTP
		$mail->CharSet = 'utf-8';
        $mail->Host = 'mail.iskillmetrics.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@iskillmetrics.com';       // SMTP username
        $mail->Password = 'w?C35UF?xMa[';                     // SMTP password

        if(SITE_URL == 'http://api.io2v3.dvp/')
        {
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `ssl` also accepted 
            $mail->Port = 587;                                    // TCP port to connect to 465 for ssl 587 for tsl
        }

        else {

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   // Enable TLS encryption, `ssl` also accepted 
            $mail->Port = 465;                                    // TCP port to connect to 465 for ssl 587 for tsl
        }

        $mail->isHTML(true);
        $mail->From = 'no-reply@iskillmetrics.com';

        return $mail;

	}


	public function sendRegistrationEmail($dataPayload)
	{


		$userId = $dataPayload['user_id'];

		$sentToAddress = $dataPayload['email'];

		$mail = $this->getConfigMailer();

		$mail->isHTML(true);

		$mail->Subject = "Registration Successfull - iSkillmetrics";

		$mail->FromName = 'iSkillmetrics';

		$mail->addAddress($sentToAddress);

		$mail->Body = Route::getCurlHtml(SITE_URL.'pages/signup?user_id='.$userId);

		/*


		if(!$mail->send())
        {

            return false;
        }

        else {
            return true;
        }
        */

        try {

        	$mail->send();

        	return true;

        	
        } catch (Exception $e) {

        	return false;
        	
        }


	}



}