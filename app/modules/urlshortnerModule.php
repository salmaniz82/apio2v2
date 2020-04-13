<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class urlshortnerModule {


	public $DB;


	public $chars = '123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';


	public function __construct()
	{

        

	}


	public function convertIntToShortCode($id) {
        $id = intval($id);
        if ($id < 1) {
            throw new Exception(
                "The ID is not a valid integer");
        }

        $length = strlen($this->chars);


        // make sure length of available characters is at
        // least a reasonable minimum - there should be at
        // least 10 characters
        if ($length < 10) {
            throw new Exception("Length of chars is too small");
        }

        $code = "";
        while ($id > $length - 1) {
            // determine the value of the next higher character
            // in the short code should be and prepend


            $modexINdex = intval(fmod($id, $length));

            
            $code = $this->chars[$modexINdex] . $code;
            // reset $id to remaining value to be converted
            $id = intval(floor($id / $length));
        }

        // remaining value of $id is less than the length of
        // self::$chars 

        $code = $this->chars[$id] . $code;

        return $code;
    }



}