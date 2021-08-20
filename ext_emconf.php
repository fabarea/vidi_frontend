<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Versatile and Interactive Display - List Component for the Frontend',
    'description' => 'Generic List Component for the Frontend where content can be filtered in an advanced way... Veni, vidi, vici!',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'category' => 'plugin',
    'author_company' => 'Ecodev',
    'state' => 'stable',
    'version' => '4.0.0-dev',
    'autoload' =>
        [
            'psr-4' => ['Fab\\VidiFrontend\\' => 'Classes']
        ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'vidi' => '5.0.0-0.0.0',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'suggests' => [
    ],
];
