<?php
require_once('../vendor/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \Develpr\Phindle\Phindle;
use \Develpr\Phindle\Content;
use \Develpr\Phindle\FileHandler;

$html1 = file_get_contents(__DIR__ . '/UG-C3.html');
$html2 = file_get_contents(__DIR__ . '/UG-C6.html');

//$fileHandler = new FileHandler(__DIR__, 'blahblah');

$phindle = new Phindle(array(
	'title' => "First Mobi Book",
	'publisher' => "Develpr",
	'creator' => 'Kevin Mitchell',
	'language' => \Develpr\Phindle\OpfRenderer::LANGUAGE_ENGLISH_US,
	'subject' => 'Computers',
	'description' => 'A great book about stuff',
	'path'	=> __dir__,
    'cover' => 'GraphicsWelcome/WImage-cover.gif',
    'isbn'  => '123456789123456'
));


$content = new Content();

$content->setHtml($html1)->setTitle('Title 1')->setPosition(1)->setUniqueIdentifier('page_1');

$content1 = new Content();

$content1->setHtml($html2)->setTitle('Title 2')->setPosition(2)->setUniqueIdentifier('page_2');

$phindle->addContent($content1);
$phindle->addContent($content);




$phindle->process();



$hi = "HI";
