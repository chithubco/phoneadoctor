<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=gaiadb',
            'username' => 'root',
            'password' => 'letmein',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'REST' => [
             'class' => 'common\components\REST'
                  ],   
        'xmlDom' => [
             'class' => 'common\components\XmlDomConstructCustomizedForAPI'
                  ],  
        'twiliosms' => [
             'class' => 'common\components\twiliosms'
                  ],
    ],
];
