<?php 
class profileModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'profile';		
	}


	public function getProfileByUserId($user_id)
	{

	
		$sql = "SELECT p.id, p.user_id, p.companyTitle, p.slug, p.photo, p.logo, p.url, p.email, 
		p.contactPerson, p.address, p.mobile, p.landline from profile p WHERE p.user_id = $user_id LIMIT 1";

		if($profile = $this->DB->rawSql($sql)->returnData())
		{
			return $profile;
		}

		return false;

	}



	public function updateOrSave($user_id, $logoImageUrl)
	{


		$sql = "INSERT INTO profile (user_id, logo) VALUES($user_id, '{$logoImageUrl}')	
		ON DUPLICATE KEY UPDATE logo = '{$logoImageUrl}'";
		$this->DB->rawSql($sql);

		return $this->DB->resource;

	}



	public function udpateProfileInformation($dataPayload, $condArr)
	{


		if($this->DB->update($dataPayload, $condArr))
		{
			return true;
		}

		return false;


	}


	public function save($dataPayalod)
	{



		if($last_id = $this->DB->insert($dataPayalod))
		{
			return $last_id;
		}

		return false;
	}


	public function profileExists($user_id)
	{



		if($user_id = $this->DB->pluck('id')->Where("user_id = '".$user_id."'"))
		{
			return true;
		}

		else {
			return false;
		}


	}


	public function entityProfileBySlug($slug)
	{

		
		$sql = "SELECT id, user_id as entityId, companyTitle, slug, photo, logo, url, 
		email, contactPerson, address, mobile, landline FROM `profile` WHERE slug = '{$slug}' LIMIT 1";

		if($profile = $this->DB->rawSql($sql)->returnData())
		{

				return $profile;
		}

				return false;

	}



	public function entityslugId($user_id)
	{



		if($slug = $this->DB->pluck('slug')->Where("user_id = '".$user_id."'"))
		{
			return $slug;
		}

		else {
			return false;
		}


	}


	public function isSlugTaken($slug)
	{


		$sql = "SELECT id from profile where slug = '{$slug}' LIMIT 1";


		if($slug = $this->DB->rawSql($sql)->returnData())
		{
			return true;	
		}

		return false;

	}



}