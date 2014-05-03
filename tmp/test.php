<?php
require_once('../vendor/autoload.php');

use \Develpr\Phindle\Phindle;
use \Develpr\Phindle\Content;
use \Develpr\Phindle\FileHandler;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$html1 = file_get_contents(__DIR__ . '/Welcome.html');
$html2 = file_get_contents(__DIR__ . '/UG-C1.html');
$html3 = file_get_contents(__DIR__ . '/UG-C2.html');
$html4 = file_get_contents(__DIR__ . '/UG-C3.html');
$html5 = file_get_contents(__DIR__ . '/UG-C4.html');

$htmlExtractor = new \Develpr\Phindle\HtmlElementExtractor();

$phindle = new Phindle(array(
	'title' => "First Mobi Book",
	'publisher' => "Develpr",
	'creator' => 'Kevin Mitchell',
	'language' => \Develpr\Phindle\OpfRenderer::LANGUAGE_ENGLISH_US,
	'subject' => 'Computers',
	'description' => 'A great book about stuff',
	'path'	=> __dir__,
    'cover' => 'GraphicsWelcome/WImage-cover.gif',
    'isbn'  => '123456789123456',
    'staticResourcePath'    => '/var/www/phindle/tmp'
));


$content = new Content($htmlExtractor);

$content->setStaticResourcePath('/var/www/phindle/tmp')->setHtml($html1)->setTitle('Title 1')->setPosition(1)->setUniqueIdentifier('page_1');
$content->setSections(array(
    1 => array(
       'id' => 'id_1.1',
       'title' => '1.1 Views of Kindle'
    ),
    2 => array(
       'id' => 'id_1.2',
       'title' => '1.2 Getting Around'
    ),
    3 => array(
        'id' => 'id_1.3',
        'title' => '1.3 Entering Text'
    ),
    4 => array(
        'id' => 'id_1.4',
        'title' => '1.4 Status Indicators'
    ),
));

$content2 = new Content($htmlExtractor);
$content3 = new Content($htmlExtractor);
$content4 = new Content($htmlExtractor);
$content5 = new Content($htmlExtractor);

$content2->setTitle('Title 2')->setPosition(2)->setUniqueIdentifier('UG-C1')->setStaticResourcePath('/var/www/phindle/tmp')->setHtml($html2);
$content3->setStaticResourcePath('/var/www/phindle/tmp')->setHtml($html3)->setTitle('Title 3')->setPosition(3)->setUniqueIdentifier('UG-C2');
$content4->setStaticResourcePath('/var/www/phindle/tmp')->setHtml($html4)->setTitle('Title 4')->setPosition(4)->setUniqueIdentifier('UG-C3');
$content5->setStaticResourcePath('/var/www/phindle/tmp')->setHtml($html5)->setTitle('Title 5')->setPosition(5)->setUniqueIdentifier('UG-C4');

$phindle->addContent($content5);
$phindle->addContent($content4);
$phindle->addContent($content3);
$phindle->addContent($content2);
$phindle->addContent($content);



$phindle->process();



$hi = "HI";
