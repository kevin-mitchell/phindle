<?php namespace Develpr\Phindle;

class TableOfContents implements ContentInterface
{

    const DEFAULT_TOC_TEMPLATE = 'table_of_contents.html';

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
        $html = '';

        foreach($contents as $content)
        {
            $html .= $this->getContentToC($content);
        }

        $this->templatish->setData(array('toc' => $html));
        $this->templatish->setTemplate(self::DEFAULT_TOC_TEMPLATE);
        $this->html = $this->templatish->buildTemplate();

        return true;
    }

    public function getContentToC(ContentInterface $content)
    {
        $html = '';

        $html .= '<h3><strong><a href="'.$content->getAnchorPath().'">'.$content->getTitle().'</a></strong></h3>' . "\r\n";

        $sections = $content->getSections();

        if($sections)
            $html .= $this->getSectionHtml($sections, $content);

        $html .= "<br /><br />"  . "\r\n";

        return $html;
    }

    public function getSectionHtml(array $sections, ContentInterface $content)
    {
        $html = '';

        ksort($sections);

        if(count($sections) > 0)
            $html .= '<br /><ul>';

        foreach($sections as $section)
        {
            //todo: this is just an array and could contain anything. Too much of a convention.
            //todo: consider using an array of some new object?
            $html .= '<li><a href="'.$content->getAnchorPath($section['id']).'">'.$section['title'].'</a></li>' . "\r\n";

        }

        if(count($sections) > 0)
            $html .= '</ul>' . "\r\n";

        return $html;
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
        $base = $this->getUniqueIdentifier() . '.html';
        $id = (strlen($id) > 0) ? '#' . $id : '';

        return $base . $id;
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