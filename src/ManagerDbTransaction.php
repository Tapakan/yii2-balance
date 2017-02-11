<?php
/**
 * ManagerDbTransaction
 * @version     0.1.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance;

/**
 * Class ManagerDbTransaction
 */
abstract class ManagerDbTransaction extends AbstractManager
{
    /**
     * @var array
     */
    private $transactions = [];

    /**
     * @inheritdoc
     */
    public function increase($account, $amount, $data = [])
    {
        $this->beginDbTransaction();
        try {

            $result = parent::increase($account, $amount, $data);
            $this->commitDbTransaction();

            return $result;
        } catch (\Exception $exception) {
            $this->rollBackDbTransaction();
            throw $exception;
        }
    }

    /**
     * Begins transaction.
     */
    protected function beginDbTransaction()
    {
        $this->transactions[] = $this->createDbTransaction();
    }

    /**
     * Commit last transaction
     */
    protected function commitDbTransaction()
    {
        if ($transaction = array_pop($this->transactions)) {
            $transaction->commit();
        }
    }

    /**
     * RollBack last transaction
     */
    protected function rollBackDbTransaction()
    {
        if ($transaction = array_pop($this->transactions)) {
            $transaction->rollBack();
        }
    }

    /**
     * Creates transaction instance, actually beginning transaction.
     * If transactions are not supported, `null` will be returned.
     * @return object|\yii\db\Transaction|null transaction instance, `null` if transaction is not supported.
     */
    abstract protected function createDbTransaction();
}
