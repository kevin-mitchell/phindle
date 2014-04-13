<?php namespace Develpr\Phindle;

class FileHandler{

    private $basePath;
    private $tempDirectory;

    public function __construct($basePath, $tempDirectory)
    {
        $this->basePath = $this->addTrailingSlash($basePath);

		//Check that the base path is writable
		if(!is_writable($this->basePath))
			throw new \Exception('The path provided is not writeable by PHP. Considering using a different path or change permissions for ' . $this->basePath);

        $this->tempDirectory = $this->addTrailingSlash($this->removeLeadingSlash($tempDirectory));

        mkdir($this->basePath . $this->tempDirectory);
    }

    public function writeTempFile($filename, $fileContents)
    {
        $this->writeFile($this->tempDirectory . $filename, $fileContents);
    }

	public function writeFile($fileName, $fileContents)
	{
		$success = file_put_contents($this->basePath . $fileName, $fileContents);

		return $success !== false;
	}

    public function clean()
    {
        $files = glob($this->basePath . $this->tempDirectory . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($this->basePath . $this->tempDirectory);
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

	/**
	 * Add a trailing slash to a path
	 *
	 * @param $path
	 * @return string
	 */
	private function addTrailingSlash($path)
	{
		return (substr($path, -1) == '/' ? $path : $path . '/');
	}
}