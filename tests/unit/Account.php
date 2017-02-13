<?php
/**
 * Account
 * @version     0.1.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Account
 * @property integer $id
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
        return 'balance';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'updated_at',
                'updatedAtAttribute' => 'updated_at',
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
            ['user_id', 'default', 'value' => null],
            ['value', 'default', 'value' => 0]
        ];
    }
}
