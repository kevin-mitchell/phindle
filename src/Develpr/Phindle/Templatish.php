<?php namespace Develpr\Phindle;

class Templatish{

	private $templatePath;
	private $data;

	const PATH_TEMPLATES = '/../../templates/';

	private function getTemplatesDirectory()
	{
		return __DIR__ . self::PATH_TEMPLATES;
	}

	public function setTemplate($templateName)
	{
		$this->templatePath = $this->getTemplatesDirectory() . $templateName;

		return $this;
	}

	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	public function buildTemplate($template = false, $data = false)
	{
		if($template === false)
			$template = file_get_contents($this->templatePath);

		if($data === false)
			$data = $this->data;

		foreach($data as $key => $value)
		{
			$template = str_replace('{'.$key.'}', $value, $template);
		}

		return $template;
	}

}