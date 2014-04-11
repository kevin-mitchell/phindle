<?php namespace Develpr\Phindle;

class FileHandler{

    private $basePath;
    private $tempDirectory;

    public function __construct($basePath, $tempDirectory)
    {
        $this->basePath = $this->addTrailingSlash($basePath);;
        $this->tempDirectory = $this->removeLeadingSlash($tempDirectory);
    }

	public function writeFile($fileName, $fileContents)
	{

		$success = file_put_contents($fileName, "\xEF\xBB\xBF".  $fileContents);

		return $success === false ? false : true;
	}

	/**
	 * Removing a leading slash from a path
	 * @param $path
	 * @return mixed
	 */
	private function removeLeadingSlash($path)
	{
		return (substr($path, 0, 1) == '/' ? substr_replace($path, "", 0, 1) : $path);
	}

	private function addTrailingSlash($path)
	{
		return (substr($path, -1) == '/' ? $path : $path . '/');
	}
}