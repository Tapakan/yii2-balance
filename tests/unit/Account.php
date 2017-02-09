<?php
/**
 * Account
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use yii\db\ActiveRecord;

/**
 * Class Account
 * @property integer $user_id
 * @property integer $value
 */
class Account extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BalanceAccount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'value'], 'safe']
        ];
    }
}
