<?php
require_once('../vendor/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \Develpr\Phindle\Phindle;
use \Develpr\Phindle\Content;
use \Develpr\Phindle\FileHandler;

$html1 = file_get_contents(__DIR__ . '/Welcome.html');
$html2 = file_get_contents(__DIR__ . '/UG-C1.html');
$html3 = file_get_contents(__DIR__ . '/UG-C2.html');
$html4 = file_get_contents(__DIR__ . '/UG-C3.html');
$html5 = file_get_contents(__DIR__ . '/UG-C4.html');
$html6 = file_get_contents(__DIR__ . '/UG-C5.html');
$html7 = file_get_contents(__DIR__ . '/UG-C6.html');
$html8 = file_get_contents(__DIR__ . '/UG-C7.html');
$html9 = file_get_contents(__DIR__ . '/UG-C8.html');
$html10 = file_get_contents(__DIR__ . '/UG-C9.html');
$html11 = file_get_contents(__DIR__ . '/UG-C10.html');

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
    'isbn'  => '123456789123456',
    'staticResourcePath'    => '/var/www/phindle/tmp'
));


$content = new Content();

$content->setHtml($html1)->setTitle('Title 1')->setPosition(1)->setUniqueIdentifier('page_1');
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

$content2 = new Content();
$content3 = new Content();
$content4 = new Content();
$content5 = new Content();
$content6 = new Content();
$content7 = new Content();
$content8 = new Content();
$content9 = new Content();
$content10 = new Content();
$content11 = new Content();

$content2->setHtml($html2)->setTitle('Title 2')->setPosition(2)->setUniqueIdentifier('page_2');
$content3->setHtml($html3)->setTitle('Title 3')->setPosition(3)->setUniqueIdentifier('page_3');
$content4->setHtml($html4)->setTitle('Title 4')->setPosition(4)->setUniqueIdentifier('page_4');
$content5->setHtml($html5)->setTitle('Title 5')->setPosition(5)->setUniqueIdentifier('page_5');
$content6->setHtml($html6)->setTitle('Title 6')->setPosition(6)->setUniqueIdentifier('page_6');
$content7->setHtml($html7)->setTitle('Title 7')->setPosition(7)->setUniqueIdentifier('page_7');
$content8->setHtml($html8)->setTitle('Title 8')->setPosition(8)->setUniqueIdentifier('page_8');
$content9->setHtml($html9)->setTitle('Title 9')->setPosition(9)->setUniqueIdentifier('page_9');
$content10->setHtml($html10)->setTitle('Title 10')->setPosition(10)->setUniqueIdentifier('page_10');
$content11->setHtml($html11)->setTitle('Title 11')->setPosition(11)->setUniqueIdentifier('page_11');


$phindle->addContent($content5);
$phindle->addContent($content4);
$phindle->addContent($content3);
$phindle->addContent($content2);
$phindle->addContent($content);
$phindle->addContent($content6);
$phindle->addContent($content8);
$phindle->addContent($content7);
$phindle->addContent($content9);
$phindle->addContent($content10);
$phindle->addContent($content11);




$phindle->process();



$hi = "HI";
