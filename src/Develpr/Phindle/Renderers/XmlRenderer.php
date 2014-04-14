<?php namespace Develpr\Phindle;


abstract class XmlRenderer{

	protected $templatish;
	protected $templateFile;
	protected $data;

	public function __construct(Templatish $templatish = null)
	{
		$this->data = array();

		if(is_null($templatish))
			$this->templatish = new Templatish();
		else
			$this->templatish = $templatish;

		$this->setupTemplate();
	}

	abstract public function render(array $attributes, array $content, ContentInterface $tableOfContents = null);

	abstract protected function prepareForRender();

	public function getValue($key)
	{
		if(array_key_exists($key, $this->data))
			return $this->data[$key];
		else
			return false;
	}

	public function setValue($key, $value)
	{
		$this->data[$key] = $value;
	}

	protected function setupTemplate()
	{
		$this->templateFile = '';
	}

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
}