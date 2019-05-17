<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Versatile and Interactive Display - List Component for the Frontend',
    'description' => 'Generic List Component for the Frontend where content can be filtered in an advanced way... Veni, vidi, vici!',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'category' => 'plugin',
    'author_company' => 'Ecodev',
    'state' => 'stable',
    'version' => '2.0.3',
    'autoload' =>
        [
            'psr-4' => ['Fab\\VidiFrontend\\' => 'Classes']
        ],
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'vidi' => '3.0.0-0.0.0',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'suggests' => [
    ],
];
