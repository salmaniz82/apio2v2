<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class invitationsCtrl extends appCtrl
{

	public $module;
	public $profileModule;



	public function __construct()
	{
		
		$this->module = $this->load('module', 'invitations');
		$this->profileModule = $this->load('module', 'profile');

	}


	public function generateInviteToken($payload)
	{


		return urlencode(base64_encode(json_encode($payload)));

	}


	public function addInvitation()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	





		$user_id = jwACL::authUserId();

		$enroll_id = $this->getID();

		$profile = $this->profileModule->getProfileByUserId($user_id);


		if(!$profile)
		{

			$data['message'] = "Please add profile information";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}

		else if($profile[0]['companyTitle'] == null)  
		{

			$data['message'] = "Profile set organization name";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}

		else if($profile[0]['logo'] == null)
		{

			$data['message'] = "Profile set logo image";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}

		else if($profile[0]['slug'] == null || $profile[0]['slug'] == "")
		{

			$data['message'] = "Slug is not defined in profile settings";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}

		else {

			$slug = $profile[0]['slug'];

		}

		

		$addPayload = array(

			'enroll_id'=> $enroll_id,
			'entity_id'=> $user_id

		);


		if($inviteId = $this->module->saveInvitation($addPayload))
		{

			$data['inviteId'] = $inviteId;

			$uriPayload = array(

				'action' => 'quizInvitation',
				'entitySlug' => $slug,
				'enroll_id' => $enroll_id,
				'entity_id' => $user_id,
				'invite_id' => $inviteId
			);

			$uriToken = $this->generateInviteToken($uriPayload);


			if(!$this->module->updateRecordwithToken(array('uriToken'=> $uriToken), $inviteId))
			{

				$data['message'] = 'Failed while updating token';
				$statusCode = 500;
				return View::responseJson($data, $statusCode);

			}

			return $this->TriggerInvitationEmail($inviteId);

		}

		else {
			
			$data['message'] = 'Failed while sending Invitation';
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);


	}



	public function TriggerInvitationEmail($inviteID)
	{
		
		$emailModule = $this->load('module', 'email');

		$invitation = $this->module->invitationQuizDetails($inviteID);

		$invitation = $invitation[0];

		$payload = array(

			'inviteId' => $invitation['id'],
			'toAddress' => $invitation['name'],
			'toEmail' => $invitation['email']

		);

		$enrollModule = $this->load('module', 'enroll');


		if($emailModule->sendInviationEmail($payload))
		{


			$enroll_id = $invitation['enroll_id'];

			if($enrollModule->updateInvited($enroll_id))
			{
				$data['enroll'] = 'updated to true';
			}
			

			$data['message'] = "Invitation Sent to " . $payload['toAddress'] . " successfully";
			$statusCode = 200;
		}

		else {

			$data['message'] = "Cannot trigger email";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);		

	}


}
