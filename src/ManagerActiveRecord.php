<?php
/**
 * ActiveRecordManager
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance;

use yii\db\ActiveRecord;

/**
 * Class ActiveRecordManager
 */
class ManagerActiveRecord extends ManagerDbTransaction
{
    /**
     * @var string
     */
    public $accountClass;

    /**
     * @var string
     */
    public $transactionClass;

    /**
     * @inheritdoc
     */
    public function findAccountId($condition)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;
        $model = $class::find()->where($condition)->one();

        return $model ? $model->getPrimaryKey(false) : false;
    }

    /**
     * @inheritdoc
     */
    protected function findTransaction($condition)
    {
        /** @var ActiveRecord $class */
        $class = $this->transactionClass;

        return $class::findOne($condition);
    }

    /**
     * @inheritdoc
     */
    protected function createAccount($data)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;

        $class = new $class();
        $class->setAttributes($data);
        $class->save(false);

        return $class->getPrimaryKey(false);
    }

    /**
     * @inheritdoc
     */
    protected function createTransaction($data)
    {
        /** @var ActiveRecord $class */
        $class = $this->transactionClass;

        $class = new $class();
        $class->setAttributes($data);
        $class->save(false);

        return $class->getPrimaryKey(false);
    }

    /**
     * @inheritdoc
     */
    protected function incrementAccountBalance($accountId, $value)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;

        $primaryKeys = $class::primaryKey();
        $primaryKey  = array_shift($primaryKeys);

        $class::updateAllCounters([$this->amountAttribute => $value], [$primaryKey => $accountId]);
    }

    /**
     * @inheritdoc
     */
    public function calculateBalance($account)
    {
        /** @var ActiveRecord $class */
        $accountId = $this->fetchAccountId($account);
        $class     = $this->transactionClass;

        return $class::find()
            ->andWhere([$this->accountAttribute => $accountId])
            ->sum($this->amountAttribute);
    }

    /**
     * @inheritdoc
     */
    public function revert($transactionId, $data = [])
    {
        // TODO: Implement revert() method.
    }

    /**
     * @inheritdoc
     *
     * @return \yii\db\Transaction
     */
    protected function createDbTransaction()
    {
        return \Yii::$app->db->getTransaction();
    }
}
