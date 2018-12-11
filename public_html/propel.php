<?php
return [
    'propel' => [
        'database' => [
            'connections' => [
                'mysource' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\DebugPDO',
                    'dsn'        => 'mysql:host=localhost;dbname=labcheck',
                    'user'       => 'root',
                    'password'   => '',
                    'attributes' => []
                ]
            ]
            
        ],
        'runtime' => [
            'defaultConnection' => 'mysource',
            'connections' => ['mysource']
        ],
        'generator' => [
            'defaultConnection' => 'mysource',
            'connections' => ['mysource']
        ]
    ]
];