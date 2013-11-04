<?php

require_once('juengo/juengo.class.php');

$config = array(
	"APIKEY"=>"",
	"SECRET"=>"",
	"DEBUG"=> false
);

$jn = new Juengo($config);
//$jn->curlOn(true);

$response = $jn->getProfile();

echo 'My Juengo id is: '.$response[0]->id.'<BR />';
echo 'My name is: '.$response[0]->name.'<BR />';
echo 'and I have : '.$response[0]->juengos.' juengos! <BR />';

?>
