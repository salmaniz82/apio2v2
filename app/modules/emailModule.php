<?php 


if ( !defined('ABSPATH') )
    die('Forbidden Direct Access');


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
		
		$mail = new PHPMailer();
		return $mail;

	}



	public function getConfigMailer()
	{

		$mail = new PHPMailer(true);
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->CharSet = 'utf-8';
        $mail->Host = 'mail.iskillmetrics.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@iskillmetrics.com';       // SMTP username
        $mail->Password = 'S-atUbFrh;$M';                     // SMTP password

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
        $mail->setFrom('no-reply@iskillmetrics.com', 'iSkillMetrics');

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
        try {

            $mail->send();

            return true;

            
        } catch (Exception $e) {

            return false;
            
        }
        */

        /*    
        return $mail->send();
        */


        try {

            $mail->send();

            return true;
            
        } catch (Exception $e) {

           return false;
            
        }


	}


    public function sendInviationEmail($payload)
    {

        extract($payload);

        $inviteId;
        $toEmail;
        $toAddress;
        

        $mail = $this->getConfigMailer();
        $mail->isHTML(true);
        $mail->Subject = "Quiz Invitation - iSkillmetrics";
        $mail->FromName = 'iSkillmetrics';
        $mail->addAddress($toEmail, $toAddress);
        $mail->Body = Route::getCurlHtml(SITE_URL.'pages/invite-exam/'.$inviteId);


        /*    
        try {


            if($mail->send())
            {
                return true;
            }

            
        } catch (Exception $e) {

            return $mail->ErrorInfo;
            
        }
        */
        
       
        /*    
        return $mail->send();
        */



        try {

            $mail->send();

            return true;
            
        } catch (Exception $e) {

           return false;
            
        }

    }




    public function testEmailConfigModule()
    {



        $sentToAddress = 'salmaniz.82@gmail.com'; 

        $toAddress = 'salman ahmed';


        $mail = $this->getConfigMailer();
        $mail->isHTML(true);
        $mail->Subject = "Status Update";
        $mail->FromName = 'iSkillmetrics';
        $mail->addAddress($sentToAddress, $toAddress);
        $mail->Body = "<p> This is inform you </p>"; 

        try {

            $mail->send();

            return true;
            
        } catch (Exception $e) {

           return false;
            
        }


    }



    public function triggerForgetPassword($payload)
    {

        extract($payload);

        $mail = $this->getConfigMailer();
        $mail->isHTML(true);
        $mail->Subject = "Password Recovery - iSkillmetrics";
        $mail->FromName = 'iSkillmetrics';
        $mail->addAddress($email);
        $mail->Body = Route::getCurlHtml(SITE_URL.'pages/changepassword?actionid='.$id);

        try {

            $mail->send();

            return true;
            
        } catch (Exception $e) {

           return false;
            
        }

    }


}