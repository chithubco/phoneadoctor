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
        'Twillio' => [
            'class' => 'common\components\Twillio',
            'sid' => 'AC5046ffa9fb660692f30caca6d085c6c3',
            'token' => '86b9ea83d02fa841c4a09865bc56beac',            
            ],
        'AuthoriseUser' => [
            'class' => 'common\components\userAuthentication'
            ],  
    ],
];
