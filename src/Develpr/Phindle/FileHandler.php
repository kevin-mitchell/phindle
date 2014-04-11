<?php namespace Develpr\Phindle;

class FileHandler{

    private $basePath;
    private $tempDirectory;

    public function __construct($basePath, $tempDirectory)
    {
        $this->basePath = $basePath;
        $this->tempDirectory = $tempDirectory;
    }

	public function writeFile($fileName, $fileContents)
	{

		$success = file_put_contents($fileName, "\xEF\xBB\xBF".  $fileContents);

		return $success === false ? false : true;

	}
	
}