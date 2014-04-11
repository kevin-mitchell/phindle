<?php namespace Develpr\Phindle;


class Phindle{

	private $content;
	private $toc;
	private $writer;

	public function __construct($data = array())
	{
		if(array_key_exists('outputDirectory', $data))
		{
			$this->outputDirectory = $data['outputDirectory'];
			$this->writer = 
		}
		else
		{
			
		}
			

		$this->content = array();
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


}