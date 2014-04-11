<?php namespace Develpr\Phindle;


class Phindle{

	private $content;
	private $toc;
    private $attributes;
    private $fileHandler;

	public function __construct($data = array(), FileHandler $handler = null)
	{
		$this->attributes = $data;

		$this->content = array();


		$this->fileHandler = $handler;


	}

	public function setAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	public function getAttribute($key)
	{
		return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
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
        $errors = array();

        if(!$this->attributeExists('title'))
            $errors[] = 'A title must be specified.';

		if(!$this->attributeExists('language'))
            $errors[] = 'A language must be specified, and it should be in the format (example) `en-us`';

        //todo: multiple creators should be configurable
		if(!$this->attributeExists('creator'))
            $errors[] = 'A creator (author) must be specified. ';

		if(!$this->attributeExists('publisher'))
            $errors[] = 'A publisher must be specified. If there is no publisher, use the same as `creator`';

		if(!$this->attributeExists('subject'))
            $errors[] = 'A subject is required - see https://www.bisg.org/complete-bisac-subject-headings-2013-edition';

		if(!$this->attributeExists('description'))
            $errors[] = 'A description is required.';

		if((is_null($this->fileHandler) || !$this->fileHandler) && !$this->attributeExists('path'))
		{
			$errors[] = "A FileHandler must be created to save files, or the path must be set so a FileHandler can be created.";
		}


        return $errors;
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

	private function sortByPosition(ContentInterface $c1, ContentInterface $c2)
	{
		if($c1->getPosition() == $c2->getPosition())
			return 0;

		return ($c1->getPosition() < $c2->getPosition()) ? -1 : 1;
	}

	private function sortContent()
	{
		usort($this->content, array($this, 'sortByPosition'));

		return $this;
	}
	
	private function createFileHandler()
	{
		if(!$this->attributeExists('path'))
			throw new \Exception("Unable to create FileHandler without a path supplied");

		if(!$this->attributeExists('tempDirectory'))
			$this->setAttribute('tempDirectory', rand(11111111,999999999));

		$this->fileHandler = new FileHandler($this->attributes['path'], $this->attributes['tempDirectory']);

		return $this->fileHandler;
	}


	/**
	 * Create a new Mobi document from data provided
	 *
	 * @throws \Exception
	 */
	public function process()
	{
		$result = $this->validate();

		if(count($result) > 0)
			throw new \Exception("Invalid Phidle. Additional configuration options required. " . implode('. ', $result));

		if(!$this->fileHandler)
			$this->fileHandler = $this->createFileHandler();


		$this->sortContent();

	}

	private function attributeExists($attribute)
	{
		return !(!array_key_exists($attribute, $this->attributes) || strlen($this->attributes[$attribute]) < 1);
	}

}
