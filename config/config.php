<?php

use ARC\ProductConfigurator\View\AppView;

return [
    'ARC' => [
        'ProductConfigurator' => [
            'common' => [
                'copyright' => 'Arc Point Group',
                'name' => 'Product Configurator',
                'website' => 'https://www.arcpointgroup.com/',
            ],

            'date' => [
                'default' => 'F j, Y g:ia',
            ],

            /**
             * Optionally inject content into specific template locations.
             *
             * @see AppView::elementHook()
             */
            'elementHook' => [
                'buildFormPost' => null,
                'buildPageHeader' => null,
                'layoutHeader' => 'Layout' . DS . 'header',
                'layoutMetaTags' => null,
                'layoutNavPre' => null,
                'layoutNavPost' => null,
                'layoutNavTop' => null,
            ],

            'imageBaseUrl' => 'img/',

            'imgix' => [
                'xs' => [
                    'w' => 100,
                    'h' => 100,
                ],
                'sm' => [
                    'w' => 200,
                    'h' => 200,
                ],
                'md' => [
                    'w' => 666,
                    'h' => 666,
                ],
                'lg' => [
                    'w' => 1000,
                    'h' => 1000,
                ],
                'xl' => [
                    'w' => 1500,
                    'h' => 1500,
                ],
                'swatch' => [
                    'w' => 100,
                    'h' => 100,
                ]
            ],

            'meta' => [
                'description' => 'Arc Point Group product configurator plugin for CakePHP.',
            ],

            'text' => [
                'save' => 'Your build has been submitted.',
                'submit' => 'Submit',
                'front' => 'Front',
                'back' => 'Back',
                'uploadFront' => 'Upload Front Image',
                'uploadBack' => 'Upload Back Image',
            ],

            'timezone' => 'UTC',

            // Form templates to use for the admin pages
            'adminTemplates' => 'ARC/ProductConfigurator.templates_admin',
            // Form templates to use for the build page
            'buildTemplates' => 'ARC/ProductConfigurator.templates_build',

            // determines if layers should be sorted asc or desc
            'layerDirection' => 'asc',

            // database connection to use
            'connection' => 'default',
        ],
    ],
];
