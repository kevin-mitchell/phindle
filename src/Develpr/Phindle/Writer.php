<?php namespace Develpr\Phindle;

class Writer{
	
	public function writeFile($fileName, $fileContents)
	{

		$success = file_put_contents($fileName, "\xEF\xBB\xBF".  $fileContents);

		return $success === false ? false : true;

	}
	
}