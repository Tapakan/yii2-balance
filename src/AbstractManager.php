<?php
/**
 * AbstractManager
 * @version     0.2.0
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance;

use Tapakan\Balance\Event\TransactionEvent;
use Tapakan\Balance\Exception\CouldNotCreateTransactionException;
use yii\base\Component;
use yii\base\InvalidParamException;

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
    public $accountLinkAttribute = 'account_id';

    /**
     * @var string name of the account user identifier attribute, used for comfort and fast find account id.
     */
    public $accountUserIdAttribute = 'user_id';

    /**
     * @var bool Run validation when save ActiveRecord
     */
    public $runValidation = true;

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

        $data[$this->amountAttribute]      = $value;
        $data[$this->accountLinkAttribute] = $accountId;

        $data = $this->beforeCreateTransaction($accountId, $data);

        $this->incrementAccountBalance($accountId, $value);
        $transactionId = $this->createTransaction($data);

        if (!$transactionId) {
            throw new CouldNotCreateTransactionException("Couldn't create transaction");
        }

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
     * @inheritdoc
     */
    public function revert($transactionId, $data = [])
    {
        $transaction = $this->findTransaction($transactionId);
        if (!$transaction) {
            throw new InvalidParamException("Transaction '{$transactionId}' doesn't exists");
        }

        $value   = $transaction[$this->amountAttribute];
        $account = $transaction[$this->accountLinkAttribute];

        return $this->increase($account, (-1 * $value), $data);
    }

    /**
     * Find or create account.
     *
     * @param integer|array $idOrCondition Account id or condition
     *
     * @return int Returns account id
     * @throws InvalidParamException
     */
    protected function fetchAccountId($idOrCondition)
    {
        if (!is_array($idOrCondition)) {
            if (isset($this->accountUserIdAttribute) &&
                ($accountId = $this->findAccountIdByUserIdentifier($idOrCondition))
            ) {
                $idOrCondition = $accountId;
            }

            return $idOrCondition;
        }

        if (!$accountId = $this->findAccountId($idOrCondition)) {
            if (!$accountId = $this->createAccount($idOrCondition)) {
                throw new InvalidParamException("Can not instantiate account");
            }
        }

        return $accountId;
    }

    /**
     * Find exist account
     *
     * @param integer|array $idOrCondition Primary key or condition
     *
     * @return mixed
     */
    abstract protected function findAccountId($idOrCondition);

    /**
     * Find exist account by user identifier
     *
     * @param integer $userId
     *
     * @return int|mixed
     */
    abstract protected function findAccountIdByUserIdentifier($userId);

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
