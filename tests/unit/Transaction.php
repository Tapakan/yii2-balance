<?php
/**
 * Transaction
 * @version     0.1.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Transaction
 * @property integer $id
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
        return 'balance_history';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value'              => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['account_id', 'required'],
            [['value'], 'number'],
            [['account_id', 'order_id', 'site_id', 'ref', 'partner_id'], 'integer']
        ];
    }
}
