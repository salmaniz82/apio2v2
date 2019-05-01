<?php 

function _e($string)
{
	return htmlspecialchars($string);
}


function dataLoop(array $dataforLoop, $callback)
{


	foreach ($dataforLoop as $key => $value) {


		$callback($callback);
			
		}	

}


function sanitize($colums)
{

    foreach ($colums as $key => $value) {
        if(isset($_POST[$value]))
        {
            $data[$value] = $_POST[$value];  
        }
    }

    return $data;

}


function hello(){

	echo "hello from helper";
}


function ddx($var)
{
	echo "<pre>";

	var_dump($var);

	echo "</pre>";
}


function prx($var)
{
	echo "<pre>";

	print_r($var);

	echo "</pre>";
}


function lang()
{
	// interface for language singleton class;
	return LocaleFactory::instance();
}



function sanitizeFilename($string)
{
	$res = preg_replace("/[^a-z0-9\.]/", "", $string);
	return $res;
}



function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}


function xTimeAgo ($oldTime) {

  $newTime = Date('Y-m-d H:i:s');

  $timeCalc = strtotime($newTime) - strtotime($oldTime);
  if ($timeCalc > (60*60*24)) {$timeCalc = round($timeCalc/60/60/24) . " days ago";}
  else if ($timeCalc > (60*60)) {$timeCalc = round($timeCalc/60/60) . " hours ago";}
  else if ($timeCalc > 60) {$timeCalc = round($timeCalc/60) . " minutes ago";}
  else if ($timeCalc > 0) {$timeCalc .= " seconds ago";}
  return $timeCalc;

}


?>