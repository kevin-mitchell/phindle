<?php namespace Develpr\Phindle;

class TableOfContents implements ContentInterface
{

    private $html = "";
    private $templatish;
    private $htmlExtractor;

    const tableOfContentsUniqueId = 'toc';


    function __construct(Templatish $templatish, HtmlElementExtractor $elementExtractor)
    {
        $this->templatish = $templatish;
        $this->htmlExtractor = $elementExtractor;
    }


    public function getUniqueIdentifier()
    {
        return self::tableOfContentsUniqueId;
    }

    /**
     * Echo the body of the content as HTML
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Generate the table of contents 
     * @param $contents
     */
    public function generate($contents)
    {

    }

    /**
     * Get the title of this content object
     *
     * @return mixed
     */
    public function getTitle()
    {
        return "Table of Contents";
    }

    /**
     * Returns an anchor tag to the content object for use when creating links.
     *
     * @param string $id optionally provide the id (id="chapter_1_1") which will be added to anchor tag
     * @return string
     */
    public function getAnchorPath($id = "")
    {
        return self::tableOfContentsUniqueId . '.html' . (strlen($id) > 0) ? '#' . $id : '';
    }

    /**
     * Provides the relative position of the content. This is for sorting purposes and is what will determine
     * where each content object ends up in the final ebook.
     *
     * @return int
     */
    public function getPosition()
    {
        return -1;
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
        return array();
    }

}