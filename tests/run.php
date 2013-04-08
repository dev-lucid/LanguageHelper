<?php
# Copyright 2013 Mike Thorn (github: WasabiVengeance). All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

global $__lng;
include_once(__DIR__.'/../lib/php/lng.php');
lng::init('en','us',__DIR__.'/tests/dict_path1',__DIR__.'/tests/dict_path2',__DIR__.'/tests/dict_path3');
#$__lng['log_hook'] = 'mylogger';

global $output_path;
$output_path = '';
$nl = (isset($_SERVER['HTTP_HOST']))?'<br />':"\n";
$output  = __DIR__.'/generated/';
$compare = __DIR__.'/expected/';
$tests  = __DIR__.'/tests/';


$files = glob($output.'*');
foreach($files as $file)
{
	if(is_file($file))
		unlink($file); 
}

function mylogger($string)
{
	echo($string."\n");
}

$fail_count = 0;
echo('Beginning test run'.$nl.' '.$nl);

$files = glob($tests.'*');
foreach($files as $file)
{
	
	if(is_file($file))
	{
		$name = str_replace(__DIR__.'/tests/','',str_replace('.php','',$file));
		$output_path = $output . $name.'.txt';
		
		include($file);
		
		if(file_exists($output . $name.'.txt'))
		{
			$to_test  = file_get_contents($output . $name.'.txt');
		}
		else
		{
			$to_test = -1;
		}
		
		if(file_exists($output . $name.'.txt'))
		{
			$good_val  = file_get_contents($compare . $name.'.txt');
		}
		else
		{
			$good_val = -2;
		}
		
		echo($name.': ');
		
		if($to_test != $good_val)
			$fail_count++;
			
		$result = ($to_test == $good_val)?'SUCCESS':'FAIL';
		echo($result);
		echo($nl);
	}
}

echo('-----------------'.$nl);
if($fail_count == 0)
{
	echo('ALL TESTS PASS!'.$nl);
}
else
{
	echo($fail_count. ' TEST(S) FAILED'.$nl);
}



?>