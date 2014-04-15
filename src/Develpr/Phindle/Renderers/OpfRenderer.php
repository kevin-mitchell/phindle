<?php namespace Develpr\Phindle;

/**
 * This class is responsible for rendering a complete Open Packaging Format (OPF) file
 *
 * The OPF file contains various meta data about the book, and is a requirement to successfully create a complete
 * mobi ebook using kindlegen
 *
 * Class OpfRenderer
 * @package Develpr\Phindle
 */
class OpfRenderer extends XmlRenderer{

	const LANGUAGE_ENGLISH_US = 'en-us';
	const LANGUAGE_ENGLISH_GB = 'en-gb';
	const LANGUAGE_EN = 'en';
	const LANGUAGE_GERMAN = 'de';
	const LANGUAGE_SPANISH = 'es';
	const LANGUAGE_FRENCH = 'fr';

	const MANIFEST_ITEM_PREFIX = 'item';

	private $htmlParser;
	private $content;

	/**
	 * Construction for OpfRenderer requires a html element parser for building list of resources
	 *
	 * @param Templatish $templatish
	 * @param HtmlElementExtractor $htmlParser
	 */
	public function __construct(Templatish $templatish = null, HtmlElementExtractor $htmlParser = null)
	{
		parent::__construct($templatish);

		if(is_null($htmlParser))
			$this->htmlParser = new HtmlElementExtractor();
		else
			$this->htmlParser = $htmlParser;
	}

	public function render(array $attributes, array $content)
	{
		$this->data = $attributes;
		$this->content = $content;

		$this->prepareForRender();

		$this->templatish->setData($this->data);
		$this->templatish->setTemplate('open_packaging_format.opf');
		$opfContent = $this->templatish->buildTemplate();

		return $opfContent;
	}

	/**
	 * Prepare the opf related data/fields for rendering
	 */
	protected function prepareForRender()
	{
		if(!$this->getValue('publicationDate'))
			$this->setValue('publicationDate', date("Y-m-d"));

		//If a static resource path is set (for css/images) then we want to make sure we provided a uniform path to prepend
		if($this->getValue('staticResourcePath'))
			$this->setValue('staticResourcePath', $this->addTrailingSlash($this->getValue('staticResourcePath')));
		else
			$this->setValue('staticResourcePath', '');

		$this->buildManifest();
		$this->buildSpine();

        if($this->getValue('isbn'))
        {
            $this->setValue('isbn', '<dc:identifier id="'.$this->getValue('uniqueId').'" opf:scheme="ISBN">'.$this->getValue('isbn').'</dc:identifier>');
        }
        else
        {
            $this->setValue('isbn', '');
        }

	}

	private function buildManifest()
	{
		$manifest = "";

		//Normally we'll use a template manifest item snippet, but we'll allow customization by the user
		if(!$this->getValue('manifestTemplate'))
			$template = '<item id="{id}" media-type="{type}" href="{path}"/>' . "\n";
		else
			$template = $this->getValue('manifestTemplate');


		$imageFiles = array();
		foreach($this->content as $content)
		{
			/** @var ContentInterface $content */

			//Add content to manifest (html files)
			$manifest .= $this->templatish->buildTemplate($template, array(
				'type' => 'application/xhtml+xml',
				'id'	=> self::MANIFEST_ITEM_PREFIX . $content->getUniqueIdentifier(),
				'path'	=> $content->getAnchorPath()
			));

			$images = $this->htmlParser->extractImg($content->getHtml());
			$imageFiles = array_unique(array_merge($imageFiles, $images));

		}

		array_walk($imageFiles, function(&$value)
		{
			$value = array(
				'path' => $this->getValue('staticResourcePath') . $value,
				'type' => $this->estimateMediaType($value),
				'id'	=> rand(11111111,99999999)
			);
		});

		foreach($imageFiles as $imageFile)
		{
			$manifest .= $this->templatish->buildTemplate($template, $imageFile);
		}

        if($this->getValue('cover') !== false)
        {
            $manifest .= $this->templatish->buildTemplate($template, array(
                'path'  => $this->getValue('staticResourcePath') . $this->data['cover'],
                'type'  => $this->estimateMediaType($this->data['cover']),
                'id'    => 'cover'
            )) . "\n";
            $this->setValue('cover', '<meta name="cover" content="cover" />');
        }
        else
        {
            $this->data('cover', '');
        }

        $manifest .= $this->templatish->buildTemplate($template, array(
            'path'  => $this->getValue('uniqueId') . '.ncx',
            'type'  => 'application/x-dtbncx+xml',
            'id'    => $this->getValue('uniqueId') . '_TOC'
        )) . "\n";

		$this->data['manifest'] = $manifest;

	}

	private function buildSpine()
	{
		$spines = "";
		foreach($this->content as $content)
		{
			/** @var ContentInterface $content */
			if(!$this->getValue('manifestTemplate'))
				$template = '<item id="{id}" media-type="{type}" href="{path}"/>' . "\n";
			else
				$template = $this->getValue('manifestTemplate');

			$spines .= $this->templatish->buildTemplate($template, array(
				'type' => 'application/xhtml+xml',
				'id'	=> self::MANIFEST_ITEM_PREFIX . $content->getUniqueIdentifier(),
				'path'	=> $content->getAnchorPath()
			));

		}



	}

	/**
	 * Estimate the name of the image based on filetype
	 * or file path
	 *
	 * todo: this is slipity-slipity, make it better or remove it
	 *
	 * @param $fileName
	 */
	private function estimateMediaType($fileName)
	{
		if(strpos($fileName, '.gif') !== false)
			return 'image/gif';
		else if(strpos($fileName, '.png') !== false)
			return 'image/png';
		else if(strpos($fileName, '.svg') !== false)
			return 'image/svg+xml';
		else if(strpos($fileName, '.jpg') !== false || strpos($fileName, '.jpeg') !== false)
			return 'image/jpeg';

	}

	public function validate()
	{
		if(!$this->getValue('uniqueId'))
			throw new \Exception("A uniqueId must be given to the OpfRenderer. No uniqueId present. Please supply in constructor or by setValue()");
	}

    public function getXml()
    {

    }

}