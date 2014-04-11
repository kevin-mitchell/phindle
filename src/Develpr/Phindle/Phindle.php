<?php namespace Develpr\Phindle;


class Phindle{

	private $content;
	private $toc;
	private $outputDirectory;
    private $metadata;
    private $errors;

	public function __construct($data = array())
	{
		if(array_key_exists('outputDirectory', $data))
			$this->outputDirectory = $data['outputDirectory'];

        $this->metadata = $data;

		$this->content = array();
	}

    public function valid()
    {
        return count($this->validate()) > 0 ? false : true;
    }

    /**
     * Validate that all required parameters are set in the metadata
     *
     * @return array
     */
    public function validate()
    {
        $this->errors = array();

        if(!array_key_exists('title', $this->metadata) || strlen($this->metadata['data'] < 1))
            $errors[] = 'A title must be specified.';

        if(!array_key_exists('language', $this->metadata) || strlen($this->metadata['language'] < 1))
            $errors[] = 'A language must be specified, and it should be in the format (example) `en-us`';

        //todo: multiple creators should be configurable
        if(!array_key_exists('creator', $this->metadata) || strlen($this->metadata['creator'] < 1))
            $errors[] = 'A creator (author) must be specified. ';

        if(!array_key_exists('publisher', $this->metadata) || strlen($this->metadata['publisher'] < 1))
            $errors[] = 'A publisher must be specified. If there is no publisher, use the same as `creator`';

        if(!array_key_exists('subject', $this->metadata) || strlen($this->metadata['subject'] < 1))
            $errors[] = 'A subject is required - see https://www.bisg.org/complete-bisac-subject-headings-2013-edition';

        if(!array_key_exists('description', $this->metadata) || strlen($this->metadata['description'] < 1))
            $errors[] = 'A description is required.';



        return $this->errors;
    }

	/**
	 * Set the base output directory for the mobi file as we as temp files
	 *
	 * @param $outputDirectory
	 * @return $this
	 */
	public function setOutputDirectory($outputDirectory)
	{
		$this->outputDirectory = $outputDirectory;

		return $this;
	}

	/**
	 *
	 * Add new content to the Phindle document
	 *
	 * @param ContentInterface $content
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function addContent(ContentInterface $content)
	{
		if(!$content instanceof ContentInterface)
			throw new \InvalidArgumentException("Content must implement the ContentInterface");

		$this->content[] = $content;

		return $this;
	}

	/**
	 * Add new Table of Contents (toc) to Phindle document
	 *
	 * @param IndexInterface $index
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function addTableOfContents(TableOfContentsInterface $toc)
	{
		if(!$toc instanceof TableOfContentsInterface)
			throw new \InvalidArgumentException("Content must implement the TableOfContentsInterface");

		$this->toc = $toc;

		return $this;
	}

	private function writeFile($fileName, $fileContents)
	{

		$success = file_put_contents($fileName, "\xEF\xBB\xBF".  $fileContents);

		return $success === false ? false : true;

	}

}