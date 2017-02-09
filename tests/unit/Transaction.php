<?php
/**
 * Transaction
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use yii\db\ActiveRecord;

/**
 * Class Transaction
 * @property integer $account_id
 * @property integer $value
 * @property string  $data
 */
class Transaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BalanceTransaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'value', 'data'], 'safe']
        ];
    }
}
