<?php

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

            'imageBaseUrl' => 'img/',

            'meta' => [
                'description' => 'Arc Point Group product configurator plugin for CakePHP.',
            ],

            'text' => [
                'submit' => 'Submit',
            ],

            // Form templates to use for the admin pages
            'adminTemplates' => 'ARC/ProductConfigurator.templates_admin',
            // Form templates to use for the build page
            'buildTemplates' => 'ARC/ProductConfigurator.templates_build',
        ],
    ],
];
