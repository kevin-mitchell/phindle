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
class HtmlHelper{

	private $tempDirectory;
	private $absoluteStaticResourcePath;
	private $relativePath;
    private $downloadImages;

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

    /**
     * This can be used (optionally!) to extract "sections" from an html file so that Phindle can attempt to
     * build a better/automatic section navigation within each content object. Think of this as sections in a
     * chapter, where the content might represent an entire chapter.
     *
     * @param $html
     * @return array
     */
    public function extractSections($html)
    {
        $items = $this->extractTags('link', $html);

        return $this->extractAttribute('href', $items);
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

	public function appendRelativeResourcePaths($html)
	{

		if(!$this->getAbsoluteStaticResourcePath() || !$this->getTempDirectory())
			return $html;


		$DOM = new \DOMDocument;
		$DOM->loadHTML($html);

		$images = $DOM->getElementsByTagName('img');

		foreach($images as $image)
		{
			/** @var \DOMElement  $image */
			$src = $this->removeLeadingSlash($image->getAttribute('src'));
			$image->removeAttribute('src');

            if($this->isUrl($src))
            {
                if($this->getDownloadImages())
                {
                    $filename = basename($src);
                    if(!file_exists($this->getTempDirectory() . '../' . $filename))
                        file_put_contents($this->getTempDirectory() . '../' . $filename, fopen($src, 'r'));
                    $image->setAttribute('src', '../' . $filename);
                }
            }
            else
            {
                $image->setAttribute('src', $this->getRelativeResourcePath($src));
            }
		}

		$links = $DOM->getElementsByTagName('link');
		foreach($links as $link)
		{
			/** @var \DOMElement $link */
			$href = $this->removeLeadingSlash($link->getAttribute('href'));
			$link->removeAttribute('href');
			$link->setAttribute('href', $this->getRelativeResourcePath($href));
		}

		$DOM->saveHTML();

		$html = $DOM->saveHTML();

		return $html;

	}

	public function getRelativeResourcePath($path)
	{
		return $this->getRelativePath() . $path;
	}

    public function isUrl($candidate)
    {
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

        return preg_match("/^$regex$/", $candidate);

    }

	/**
	 * Calculates a relative path between two absolute baths.
	 *
	 * Thanks to Gordon: http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php
	 *
	 * Note: The reason we have this is that kindlegen requires relative paths, so we need to calculate relative
	 * paths for each static resource.
	 *
	 * @param $from
	 * @param $to
	 * @return string
	 */
	private function calculateRelativePath()
	{
		if(!$this->getAbsoluteStaticResourcePath() || !$this->getTempDirectory())
			return '';

		$from = $this->getTempDirectory();
		$to = $this->getAbsoluteStaticResourcePath();

		// some compatibility fixes for Windows paths
		$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
		$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
		$from = str_replace('\\', '/', $from);
		$to   = str_replace('\\', '/', $to);

		$from     = explode('/', $from);
		$to       = explode('/', $to);
		$relPath  = $to;

		foreach($from as $depth => $dir) {
			// find first non-matching dir
			if($dir === $to[$depth]) {
				// ignore this directory
				array_shift($relPath);
			} else {
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;
				if($remaining > 1) {
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * -1;
					$relPath = array_pad($relPath, $padLength, '..');
					break;
				} else {
					$relPath[0] = './' . $relPath[0];
				}
			}
		}
		$this->setRelativePath($this->addTrailingSlash(implode('/', $relPath)));

		return $this->addTrailingSlash(implode('/', $relPath));
	}

	//todo: this isn't dry, it's a copy/paste from the xmlrenderer
	/**
	 * Removing a leading slash from a path
	 * @param $path
	 * @return mixed
	 */
	protected function removeLeadingSlash($path)
	{
		return (substr($path, 0, 1) == '/' ? substr_replace($path, "", 0, 1) : $path);
	}

	/**
	 * Add a trailing slash to a path
	 *
	 * @param $path
	 * @return string
	 */
	protected function addTrailingSlash($path)
	{
		return (substr($path, -1) == '/' ? $path : $path . '/');
	}

	/**
	 * @param mixed $absoluteStaticResourcePath
	 */
	public function setAbsoluteStaticResourcePath($absoluteStaticResourcePath)
	{
		$this->absoluteStaticResourcePath = $absoluteStaticResourcePath;
		$this->calculateRelativePath();

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAbsoluteStaticResourcePath()
	{
		return $this->absoluteStaticResourcePath;
	}

	/**
	 * @param mixed $tempDirectory
	 */
	public function setTempDirectory($tempDirectory)
	{
		$this->tempDirectory = $tempDirectory;
		$this->calculateRelativePath();

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTempDirectory()
	{
		return $this->tempDirectory;
	}

	/**
	 * @param mixed $relativePath
	 */
	public function setRelativePath($relativePath)
	{
		$this->relativePath = $relativePath;
	}

	/**
	 * @return mixed
	 */
	public function getRelativePath()
	{
		return $this->relativePath;
	}

    /**
     * @param mixed $downloadImages
     */
    public function setDownloadImages($downloadImages)
    {
        $this->downloadImages = $downloadImages;
    }

    /**
     * @return mixed
     */
    public function getDownloadImages()
    {
        return $this->downloadImages;
    }





}