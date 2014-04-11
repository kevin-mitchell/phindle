<?php namespace Develpr\Phindle;

use \DOMDocument;
use \DOMNamedNodeMap;
use \DOMElement;
use \DOMNodeList;
/**
 * Extracts information on various HTML elements kindlegen needs to be aware of
 *
 * Class HtmlElementExtractor
 * @package Develpr\Phindle
 */
class HtmlElementExtractor{

	/**
	 * Extract a tag (<a> <img> <h2> etc) from
	 *
	 * @param $tag
	 * @param $html
	 * @return DOMNodeList
	 */
	public function extractTags($tag, $html)
	{
		$DOM = new DOMDocument;
		$DOM->loadHTML($html);

		$items = $DOM->getElementsByTagName($tag);

		return $items;
	}

	public function extractAttribute($attribute, $items)
	{
		$return = array();

		//display all H1 text
		for ($i = 0; $i < $items->length; $i++){
			/** @var DOMElement $element */
			$element = $items->item($i);

			/** @var DOMNamedNodeMap $attributes */
			$attributes = $element->attributes;

			$interestedAttribute = $attributes->getNamedItem($attribute);

			if(!is_null($interestedAttribute))
				$return[] = $interestedAttribute->nodeValue;
		}

		return $return;
	}


	public function extractCss($html)
	{
		$items = $this->extractTags('link', $html);

		return $this->extractAttribute('href', $items);

	}

	public function extractImg($html)
	{
		$items = $this->extractTags('img', $html);

		return $this->extractAttribute('src', $items);

	}

}