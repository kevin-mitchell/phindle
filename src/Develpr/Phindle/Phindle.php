<?php namespace Develpr\Phindle;


class Phindle{

	private $content;
	private $toc;
    private $attributes;
    private $fileHandler;
    private $opfRenderer;
    private $ncxRenderer;

    /**
     * @param array $data
     * @param FileHandler $fileHandler
     * @param NcxRenderer $ncxRenderer
     * @param OpfRenderer $opfRenderer
     */
    public function __construct($data = array(), FileHandler $fileHandler = null, NcxRenderer $ncxRenderer = null, OpfRenderer $opfRenderer = null, ContentInterface $tableOfContents = null)
	{
		$this->attributes = $data;

		$this->content = array();

        if(is_null($fileHandler))
		    $this->createFileHandler();
        else
            $this->fileHandler = $fileHandler;

        if(is_null($opfRenderer))
            $this->opfRenderer = new OpfRenderer(new Templatish(), new HtmlElementExtractor());

        if(is_null($ncxRenderer))
            $this->ncxRenderer = new NcxRenderer(new Templatish());

        if(is_null($tableOfContents))
            $this->toc = new TableOfContents(new Templatish(), new HtmlElementExtractor());

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

		$this->generateUniqueId();

        $this->sortContent();

        $this->setAttribute('start',reset($this->content)->getAnchorPath());

        //If a default instance of TableOfContents provided by this package was used then we need to tell
        //the TableOfContents instance about the contents of the Phindle file. It is not required that you use
        //TableOfContents, as long as you implement the ContentsInterface and so in these cases the generate
        //method may not exist.
        if($this->toc instanceof TableOfContents)
        {
            $this->toc->generate($this->content);
        }

        //We will add the table of contents as a "normal" content element as well because it will implement getHtml
        //and so we can write it out as a temporary static file as needed
        $this->addContent($this->toc);
        $this->sortContent();

        $this->setAttribute('toc', $this->toc->getAnchorPath());

        $this->fileHandler->writeTempFile($this->getAttribute('uniqueId') . '.opf', $this->opfRenderer->render($this->attributes, $this->content, $this->toc));
        $this->fileHandler->writeTempFile($this->getAttribute('uniqueId') . '.ncx', $this->ncxRenderer->render($this->attributes, $this->content));

        foreach($this->content as $content)
        {
            /** @var \Develpr\Phindle\ContentInterface $content */
            $this->fileHandler->writeTempFile($content->getUniqueIdentifier() . '.html', $content->getHtml());
        }

		die("HI");
        //Remove all temporary files
        $this->fileHandler->clean();

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

        return $errors;
    }


	/**
	 * Generate a unique id for this Phindle based on title and current timestamp.
	 *
	 * @return string
	 */
	private function generateUniqueId()
	{
		$fileTitlePrefix = preg_replace("/[^A-Za-z0-9]/", '', $this->getAttribute('title') . "");

		if(strlen($fileTitlePrefix) > 10)
				$fileTitlePrefix = substr($fileTitlePrefix, 0, 10);

		$uniqueId = $fileTitlePrefix . "" . date("ymdHis");

		$this->setAttribute('uniqueId', $uniqueId);

		return $uniqueId;
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
	 * @param TableOfContentsInterface $toc
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

    /**
     * Method used for sorting with usort by position of Content
     *
     * @param ContentInterface $c1
     * @param ContentInterface $c2
     * @return int
     */
    private function sortByPosition(ContentInterface $c1, ContentInterface $c2)
	{
		if($c1->getPosition() == $c2->getPosition())
			return 0;

		return ($c1->getPosition() < $c2->getPosition()) ? -1 : 1;
	}

    /**
     * Sorts the content of the Phindle file based on the order provided by each content's getPosition
     * response.
     *
     * @return $this
     */
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



	private function attributeExists($attribute)
	{
		return !(!array_key_exists($attribute, $this->attributes) || strlen($this->attributes[$attribute]) < 1);
	}


    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
    }

    public function setToc(ContentInterface $toc)
    {
        $this->toc = $toc;
    }

    public function getToc()
    {
        return $this->toc;
    }


}
