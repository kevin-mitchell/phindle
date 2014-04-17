<?php namespace Develpr\Phindle;

class NcxRenderer extends XmlRenderer{
    /**
     * Construction for OpfRenderer requires a html element parser for building list of resources
     *
     * @param Templatish $templatish
     * @param HtmlElementExtractor $htmlParser
     */
    public function __construct(Templatish $templatish = null)
    {
        parent::__construct($templatish);

    }

    public function render(array $attributes, array $content)
    {
        $this->data = $attributes;
        $this->content = $content;

        $this->prepareForRender();

        $this->templatish->setData($this->data);
        $this->templatish->setTemplate('navigation_control_xml.ncx');

        return $this->templatish->buildTemplate();
    }

	protected function prepareForRender()
	{
		$depth = 1;
        $playOrder = 1;

        $nav = '';

        foreach($this->content as $content)
        {
            /** @var ContentInterface $content */

            $this->templatish->setData(array(
                'type'  =>  'chapter',
                'id'    => $content->getUniqueIdentifier(),
                'order' => $playOrder,
                'label' => $content->getTitle(),
                'anchor'=> $content->getAnchorPath(),
                'children' => ''
            ));

            $this->templatish->setTemplate('nav_point.xml');

            $nav .= $this->templatish->buildTemplate();

            $playOrder++;
        }

        $this->data['navMap'] = $nav;
        $this->data['depth'] = $depth;
 	}

}