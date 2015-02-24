<?php

function printHelp($helpArray ){

    foreach ($helpArray as $key => $value) 
    {
    	echo "\t".$key."\t\t".$value."\n";
    }
	exit(0);
}

function checkImproperArguments($variableSet, $variableError){
	if (strpos($variableSet,'no') !== false) 
	{
		foreach ($variableError as $key => $value) 
		{
			echo $value."\n";
		}
		exit(0);
		return false;
	}
}

function checkArguments($help, $helpArray, $variableSet, $variableError){
	if ($help) 
	{
	 printHelp($helpArray);
	}
	checkImproperArguments($variableSet, $variableError);
}


?>