<?php
/**
 * AbstractManager
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance;

use Tapakan\Balance\Events\TransactionEvent;
use yii\base\Component;

/**
 * Class AbstractManager
 */
abstract class AbstractManager extends Component implements ManagerInterface
{
    /**
     * @var string
     */
    public $transactionTable;

    /**
     * @var string
     */
    public $balanceTable;

    /**
     * @var string name of the transaction entity attribute, which should store amount.
     */
    public $amountAttribute = 'value';

    /**
     * @var string name of the account entity attribute, which should store current balance value.
     */
    public $balanceAttribute = 'value';

    /**
     * @var string name of the transaction entity attribute, which should be used to link transaction entity with
     * account entity (store associated account ID).
     */
    public $accountAttribute = 'account_id';

    /**
     * @event TransactionEvent an event raised before creating new transaction. You may adjust
     * [[TransactionEvent::transactionData]] changing actual data to be saved.
     */
    const EVENT_BEFORE_CREATE_TRANSACTION = 'beforeCreateTransaction';

    /**
     * @event TransactionEvent an event raised after new transaction has been created. You may use
     * [[TransactionEvent::transactionId]] to get new transaction ID.
     */
    const EVENT_AFTER_CREATE_TRANSACTION = 'afterCreateTransaction';

    /**
     * @inheritdoc
     */
    public function increase($account, $value, $data = [])
    {
        $accountId = $this->fetchAccountId($account);

        $data[$this->amountAttribute]  = $value;
        $data[$this->accountAttribute] = $accountId;

        $data = $this->beforeCreateTransaction($accountId, $data);

        $this->incrementAccountBalance($accountId, $value);
        $transactionId = $this->createTransaction($data);

        $this->afterCreateTransaction($transactionId, $accountId, $data);

        return $transactionId;
    }

    /**
     * @inheritdoc
     */
    public function decrease($account, $value, $data = [])
    {
        return $this->increase($account, (-1 * abs($value)), $data);
    }

    /**
     * Find or create account.
     *
     * @param integer|array $account Account id or condition
     *
     * @return integer Returns account id
     */
    protected function fetchAccountId($account)
    {
        if (!is_array($account)) {
            return $account;
        }

        if ($accountId = !$this->findAccountId($account)) {
            $accountId = $this->createAccount($account);
        }

        return $accountId;
    }

    /**
     * Find exist account
     *
     * @param integer|array $condition Primary key or condition
     *
     * @return mixed
     */
    abstract protected function findAccountId($condition);

    /**
     * Find exist transaction
     *
     * @param integer|array $condition Primary key or condition
     *
     * @return mixed
     */
    abstract protected function findTransaction($condition);

    /**
     * @param string|array $data
     *
     * @return mixed
     */
    abstract protected function createAccount($data);

    /**
     * @param string|array $data
     *
     * @return mixed
     */
    abstract protected function createTransaction($data);

    /**
     * Increases current account balance value.
     *
     * @param mixed         $accountId account ID.
     * @param integer|float $value     amount to be added to the current balance.
     */
    abstract protected function incrementAccountBalance($accountId, $value);

    /**
     * @param $accountId
     * @param $data
     *
     * @return mixed
     */
    protected function beforeCreateTransaction($accountId, $data)
    {
        $event = new TransactionEvent([
            'accountId'       => $accountId,
            'transactionData' => $data
        ]);
        $this->trigger(self::EVENT_BEFORE_CREATE_TRANSACTION, $event);

        return $event->transactionData;
    }

    /**
     * @param $transactionId
     * @param $accountId
     * @param $data
     */
    protected function afterCreateTransaction($transactionId, $accountId, $data)
    {
        $event = new TransactionEvent([
            'accountId'       => $accountId,
            'transactionId'   => $transactionId,
            'transactionData' => $data
        ]);
        $this->trigger(self::EVENT_BEFORE_CREATE_TRANSACTION, $event);
    }
}
