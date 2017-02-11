<?php
/**
 * ActiveRecordManager
 * @version     0.1.1
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
    public function calculateBalance($account)
    {
        /** @var ActiveRecord $class */
        $accountId = $this->fetchAccountId($account);
        $class     = $this->transactionClass;

        return $class::find()
            ->andWhere([$this->accountLinkAttribute => $accountId])
            ->sum($this->amountAttribute);
    }

    /**
     * @inheritdoc
     */
    protected function findAccountId($idOrCondition)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;
        $model = $class::findOne($idOrCondition);

        if (!is_object($model)) {
            return null;
        }

        return $model->getPrimaryKey(false);
    }

    /**
     * @inheritdoc
     */
    protected function findAccountIdByUserIdentifier($userId)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;
        $model = $class::findOne([$this->accountUserIdAttribute => $userId]);

        if (!is_object($model)) {
            return null;
        }

        return $model->getPrimaryKey(false);
    }

    /**
     * @inheritdoc
     */
    protected function findTransaction($condition)
    {
        /** @var ActiveRecord $class */
        $class = $this->transactionClass;
        $model = $class::findOne($condition);

        if (!is_object($model)) {
            return null;
        }

        return $model->getAttributes();
    }

    /**
     * @inheritdoc
     */
    protected function createAccount($data)
    {
        /** @var ActiveRecord $class */
        $class = $this->accountClass;

        $class = new $class();
        $class->setAttributes($data, false);
        $class->save(true);

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
        $class->setAttributes($data, false);
        $class->save(true);

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
     *
     * @return \yii\db\Transaction
     */
    protected function createDbTransaction()
    {
        return \Yii::$app->db->getTransaction();
    }
}
