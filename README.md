php_sdk
=======

Juengo PHP SDK

This is the official PHP SDK for Juengo API. You can find more information about Juengo at
http://www.juengo.com and information about the API and how to use it at http://developers.juengo.com

HOW TO USE:
===========

You would need to include the juengo.class.php and initialize the class with the required configuration
by setting your Juengo app's API KEY and APP SECRET.

example:

```php
require_once('juengo/juengo.class.php');

$config = array(
	"APIKEY"=>"YOUR API KEY",
	"SECRET"=>"YOUR APP SECRET",
	"DEBUG"=> false,
	"SANDBOX"=>true
);

$myjuengoclass = new Juengo($config);
```

Please visit http://developers.juengo.com for information on how to use this API.

IMPORTANT!
==========
Prior running any of your code, make sure to modify the juengo.class.php and set the default 
domain extension (default is com, other include gr) to the Juengo platform you have access to (USA or Greece)

REQUIREMENTS:
=============
* You must register to Juengo for Communities before being able to use any of Juengo services 
(Information found here http://www.juengo.com/corporate)
* PHP 5> (+ json_decode extension)



CURRENT VERSION:
================
API version 2.0 / PHP SDK Version 1.0


