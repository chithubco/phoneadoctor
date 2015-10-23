<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
<<<<<<< HEAD
    require(__DIR__ . '/../../common/config/params-local.php'),       
=======
    require(__DIR__ . '/../../common/config/params-local.php'),
>>>>>>> b203d8daaca56614d3198886704d852b2beb2f54
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-practical-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
<<<<<<< HEAD
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
=======
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
>>>>>>> b203d8daaca56614d3198886704d852b2beb2f54
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
<<<<<<< HEAD
            ],  
        ],    
     
=======
            ],
        ],
>>>>>>> b203d8daaca56614d3198886704d852b2beb2f54
        'errorHandler' => [
            'errorAction' => 'site/error',
        ]
    ],
<<<<<<< HEAD
         'modules'=>array(
        'gii'=>array(
        'class'=>'system.gii.GiiModule',
        'password'=>'admin',
        )),
=======
>>>>>>> b203d8daaca56614d3198886704d852b2beb2f54
    'params' => $params,
];
