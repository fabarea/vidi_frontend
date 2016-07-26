<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'Versatile and Interactive Display - List Component for the Frontend',
	'description' => 'Generic List Component for the Frontend where content can be filtered in an advanced way... Veni, vidi, vici! - demo https://speciality.ecodev.ch/content-elements/dynamic-list-files/',
	'author' => 'Fabien Udriot',
	'author_email' => 'fabien@ecodev.ch',
	'category' => 'plugin',
	'author_company' => 'Ecodev',
	'state' => 'beta',
	'version' => '1.2.0-dev',
	'autoload' => 
		[
            		'psr-4' => ['Fab\\VidiFrontend\\' => 'Classes']
		],
	'constraints' => [
		'depends' => [
			'typo3' => '7.6.0-7.99.99',
			'vidi' => '2.1.0-2.99.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
	'suggests' => [
	],
];
