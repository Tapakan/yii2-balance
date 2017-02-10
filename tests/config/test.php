<?php
/**
 * Yii2 codeception config
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

use yii\helpers\ArrayHelper;

$params = [
    'id'         => 'testapp',
    'basePath'   => __DIR__,
    'components' => [
        'db' => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=localhost;dbname=travis_db',
            'username' => 'travis',
            'password' => 'mysql_native_password',
        ]
    ]
];

if (is_file(__DIR__ . '/test-local.php')) {
    $params = ArrayHelper::merge($params, require(__DIR__ . '/test-local.php'));
}

return $params;