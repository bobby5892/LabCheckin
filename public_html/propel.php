<?php
// When you're part of a team, you could want to define a common configuration file and commit it into your VCS. But, of
// course, there can be some properties you don't want to share, e.g. database passwords. Propel helps you and looks for
// a propel.yml.dist file too, merging its properties with propel.yml ones. So you can define shared configuration
// properties in propel.yml.dist, committing it in your VCS, and keep propel.yml as private. The properties loaded from
// propel.yml overwrite the ones with the same name, loaded from propel.yml.dist.
//
// For a complete references see: http://propelorm.org/documentation/reference/configuration-file.html
return [
    'propel' => [
        'paths' => [
            // The directory where Propel expects to find your `schema.xml` file.
            'schemaDir' => 'C:\wamp64\www\LabCheckin\LabCheckin',

            // The directory where Propel should output generated object model classes.
            'phpDir' => 'C:\wamp64\www\LabCheckin\LabCheckin\models',
        ],
        'database' => [
            'connections' => [
                'default' => [
                    'adapter' => 'mysql',
                    'dsn' => 'mysql:host=localhost;port=3306;dbname=default',
                    'user' => 'root',
                    'password' => '',
                    'settings' => [
                        'charset' => 'utf8'
                    ]
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'default',
            'connections' => ['default']
        ],
        'generator' => [
            'defaultConnection' => 'default',
            'connections' => ['default']
        ]
    ]
];
