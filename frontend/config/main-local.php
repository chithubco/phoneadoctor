<?php

$config = [
    'components' => [
    	'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'enableStrictParsing' => false,
            
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'NaDbaS1vson8YJl8E-U1Vm0PU4g2UGub',
            // unique CSRF cookie parameter for frontend (set by kartik-v/yii2-app-practical)
            'csrfParam' => '_frontendCsrf1',
        ],
        // unique identity cookie configuration for frontend (set by kartik-v/yii2-app-practical)
        'user' => [
            'identityCookie' => [
                'name' => '_frontendUser', // unique for frontend
                'path' => '/' // set it to correct path for frontend app.
            ]
        ],
        // unique session configuration for frontend (set by kartik-v/yii2-app-practical)
        'session' => [
            'name' => '_frontendSessionId', // unique for frontend
            'savePath' => __DIR__ . '/../runtime/sessions' // set it to correct path for frontend app.
        ]
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    //$config['bootstrap'][] = 'debug';
    //$config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
