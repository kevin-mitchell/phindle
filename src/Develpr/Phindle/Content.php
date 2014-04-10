<?php namespace Develpr\Phindle;

class Content implements ContentInterface{

	private $html;
	private $sections;
	private $title;
	private $uniqueIdentifier;

	function __construct()
	{
		//We will use this unique identifier for the static resources that we'll need to generate
		$this->uniqueIdentifier = round(time()/2) . "" . rand(11111, 99999);
	}

	/**
	 * Set the HTML that will be output
	 *
	 * @param mixed $html
	 */
	public function setHtml($html)
	{
		$this->html = $html;
	}

	/**
	 * Set the sections that exist within this content object
	 * @param mixed $sections
	 */
	public function setSections($sections)
	{
		$this->sections = $sections;
	}

	/**
	 * Set the title of this object
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}



	/**
	 * Echo the body of the content as HTML
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * Get the title of this content object
	 *
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Returns an anchor tag to the content object for use when creating links.
	 *
	 * @param string $id optionally provide the id (id="chapter_1_1") which will be added to anchor tag
	 * @return string
	 */
	public function getAnchor($id = "")
	{
		return strlen($id > 0) ? $this->uniqueIdentifier . '.html#' . $id : $this->uniqueIdentifier . '.html';
	}

	/**
	 * Provides the relative position of the content. This is for sorting purposes and is what will determine
	 * where each content object ends up in the final ebook.
	 *
	 * @return int
	 */
	public function getPosition()
	{
		// TODO: Implement getPosition() method.
	}

	/**
	 * note: we're using a "manual" approach here because the other options are
	 * 1) require a user nest ContentInterface objects within each other, which increases the complexity
	 * 2) parse the html for this ContentInterface's toHtml result and figure out where the ids are, which is not
	 *    possible without coming up with a convention which again creates extra work/learning on users part
	 *
	 * Returns a mapping of position => section data, where section data contains
	 *    sectionTitle        => 1.1 Detail Point
	 *  id                    => blah_11
	 *  content (optional)    => array( sectionTitle => ... id => ... content => array( ... ) )
	 * @return mixed
	 */
	public function getSections()
	{
		// TODO: Implement getSections() method.
	}
}