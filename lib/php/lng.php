<?php
# Copyright 2013 Mike Thorn (github: WasabiVengeance). All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

global $__lng;
$__lng = array(
	'language'=>'',
	'variant'=>'',
	'phrases'=>array(),
	'paths'=>array(),
	'log_hook'=>null,
	'error_mode'=>'exception', // or 'return_id'
);

class lng
{
	public static function init($language,$variant)
	{
		global $__lng;
		$paths = func_get_args();
		$__lng['language'] = array_shift($paths);
		$__lng['variant'] = array_shift($paths);
		$__lng['paths'] = $paths;
		#print_r($__lng['paths']);
		foreach($__lng['paths'] as $path)
		{
			$base1 = $path.'/'.$__lng['language'].'.php';
			$base2 = $path.'/'.$__lng['language'].'_'.$__lng['variant'].'.php';
			
			if(file_exists($base1))
			{
				lng::log('Loading main language '.$base1);
				include_once($base1);
			}
			if(file_exists($base2))
			{
				lng::log('Loading variant '.$base2);
				include_once($base2);
			}
		}
	}
	
	public static function __callStatic($phrase,$parameters)
	{
		global $__lng;
		
		# the phrase does not exist. Try to load it.
		if(!isset($__lng['phrases'][$phrase]))
		{
			$base1  = $__lng['language'];
			$base2  = $__lng['language'].'_'.$__lng['variant'];
			
			$names = explode('___',$phrase);
			$subdict_name = '';
			for($i=0; $i< (count($names)-1); $i++)
			{
				$subdict_name .= '___'.$names[$i];
				foreach($__lng['paths'] as $path)
				{
					lng::log('looking for '.$path.'/'.$base1.$subdict_name.'.php');
					if(file_exists($path.'/'.$base1.$subdict_name.'.php'))
					{
						lng::log('Loading Subdict of main language '.$path.'/'.$base1.$subdict_name.'.php');
						include_once($path.'/'.$base1.$subdict_name.'.php');
					}
					
					lng::log('looking for '.$path.'/'.$base2.$subdict_name.'.php');
					
					if(file_exists($path.'/'.$base2.$subdict_name.'.php'))
					{
						lng::log('Loading Subdict of variant  '.$path.'/'.$base2.$subdict_name.'.php');
						include_once($path.'/'.$base2.$subdict_name.'.php');
					}
				}
			}
		}
		
		if(!isset($__lng['phrases'][$phrase]))
		{
			if('error_mode' == 'exception')
				throw new Exception('LNG: Could not find phrase '.$phrase);
			else if('error_mode' == 'return_id')
				return $phrase;
		}
		
		$output = $__lng['phrases'][$phrase];
		for($i=0; $i<count($parameters); $i++)
		{
			$output = str_replace('{'.$i.'}',$parameters[$i],$output);
		}
		return $output;
	}
	
	function log($string_to_log)
	{
		global $__lng;
		if(!is_null($__lng['log_hook']))
		{
			$__lng['log_hook']('LNG: '.$string_to_log);
		}
	}	
}

?>