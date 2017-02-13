# Yii2 Balance Manager

[![Build Status](https://travis-ci.org/Tapakan/yii2-balance.svg?branch=master)](https://travis-ci.org/Tapakan/yii2-balance) [![Coverage Status](https://coveralls.io/repos/github/Tapakan/yii2-balance/badge.svg?branch=master)](https://coveralls.io/github/Tapakan/yii2-balance?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/58a193e3940b230032da5925/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58a193e3940b230032da5925) [![Latest Stable Version](https://poser.pugx.org/tapakan/yii2-balance/v/stable)](https://packagist.org/packages/tapakan/yii2-balance) [![Total Downloads](https://poser.pugx.org/tapakan/yii2-balance/downloads)](https://packagist.org/packages/tapakan/yii2-balance)

Yii2 Balance can perform simple operations with the user's balance.
History of operations remains.

##Installation

Run following command 
```
composer require tapakan/yii2-balance
```
or add 
```
"tapakan/yii2-balance": "*"
```
to the require section of your composer.json

##Configuration

```
    'components' => [
        'balance' => [
            'class'                  => 'Tapakan\Balance\ManagerActiveRecord',
            'accountClass'           => 'common\models\UserBalance',
            'transactionClass'       => 'common\models\UserBalanceHistory',
            'accountLinkAttribute'   => 'account_id',
            'amountAttribute'        => 'value',
            'balanceAttribute'       => 'value',
            'accountUserIdAttribute' => 'user_id'
        ],
    ],
```

##Usage
Add some value to user
 ```php
Yii:$app->balance->increase($accountId_OR_userId_OR_condition, 500);
 ```
Or take
```php
Yii:$app->balance->decrease($accountId_OR_userId_OR_condition, 100);
```

Calculate balance from history
```php
echo Yii:$app->balance->calculateBalance($accountId_OR_userId); // 400
```

With additional information that may be stored in the balance history table
```php
Yii:$app->balance->increase($accountId_OR_userId_OR_condition, 750, [
    'order_id' => 1,
    // other usefull info
]);
```

#####Since 0.1.1  version you can revert a transaction.
Let's allow transaction #35 it is removal of 200 points from the account of the user. The following command will return them into the account.
```php
Yii:$app->balance->revert($transactionId)
```

#### Example of table structure
```php
        // History of operations
        $this->createTable('balance_history', [
            'account_id' => $this->integer(),
            'value'      => $this->decimal(13, 4),
            'order_id'   => $this->integer(),
            // Other usefull information
        ]);
        
        // Calculated balance
        $this->createTable('balance', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer(),
            'value'      => $this->decimal(13, 4)
        ]);
```
