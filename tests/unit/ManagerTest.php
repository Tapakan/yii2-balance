<?php
/**
 * ManagerTest
 * @version     0.3.2
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use Codeception\Specify;
use Tapakan\Balance\AbstractManager;
use Tapakan\Balance\ManagerActiveRecord;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

/**
 * Class ManagerTest
 */
class ManagerTest extends TestCase
{
    use Specify;

    /**
     * Generating user id for new account
     * @var integer
     */
    protected $userId;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->userId = $this->faker->randomDigitNotNull;
    }

    /**
     * @return Transaction|ActiveRecord last saved transaction data.
     */
    protected function getLastTransaction()
    {
        return Transaction::find()
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param integer $id Transaction id
     *
     * @return Transaction|ActiveRecord
     */
    protected function getTransaction($id)
    {
        return Transaction::find()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @param $userId
     *
     * @return Account|null
     */
    protected function getAccount($userId)
    {
        $account = Account::findOne(['user_id' => $userId]);

        return $account;
    }

    /**
     * @return ManagerActiveRecord test manager instance.
     */
    protected function createManager()
    {
        $manager                   = new ManagerActiveRecord();
        $manager->accountClass     = Account::className();
        $manager->transactionClass = Transaction::className();

        return $manager;
    }

    /**
     * Test increase balance
     */
    public function testIncrease()
    {
        $this->specify("Account #1. Increase balance on 50", function () {
            $this->createManager()->increase(1, 50);
            verify($this->getLastTransaction()->value)->equals(50);
        });

        $this->specify("Account #999. Increase balance on 50", function () {
            $this->createManager()->increase(999, 50);
            verify($this->getLastTransaction()->value)->equals(50);
        });
    }

    /**
     * Test decrease balance
     */
    public function testDecrease()
    {
        $this->specify("Account #1. Decrease balance on 7399", function () {
            $this->createManager()->decrease(['user_id' => 1], 7399);
            verify($this->getLastTransaction()->value)->equals(-7399);
        });
    }

    /**
     * Test create account
     */
    public function testCreateAccountOnDecreaseBalance()
    {
        $this->specify("Account #777. Increase balance on 1", function () {
            $this->createManager()->decrease(['user_id' => 777], 7399);

            verify($this->getLastTransaction()->value)->equals(-7399);
            $this->tester->canSeeRecord(Account::className(), ['user_id' => 777]);
        });
    }

    /**
     * Test create account
     */
    public function testCreateAccount()
    {
        $manager = $this->createManager();

        $manager->increase(['user_id' => $this->userId], 777, [
            'order_id' => $this->faker->randomDigitNotNull,
            'site_id'  => $this->faker->randomDigitNotNull,
        ]);
        $account = $this->getAccount($this->userId);
        verify($account)->isInstanceOf(Account::className());
    }

    /**
     * Test create account by user identifier
     */
    public function testFindAccountByUserIdentifier()
    {
        $manager = $this->createManager();

        // First we create
        $manager->increase(['user_id' => $this->userId], 777, [
            'order_id' => $this->faker->randomDigitNotNull,
            'site_id'  => $this->faker->randomDigitNotNull,
        ]);
        $manager->increase($this->userId, 777, [
            'order_id' => $this->faker->randomDigitNotNull,
            'site_id'  => $this->faker->randomDigitNotNull,
        ]);
        $account = $this->getAccount($this->userId);
        verify($account)->isInstanceOf(Account::className());
    }

    /**
     * Test calculating Transactions and compare with value from Account
     */
    public function testCalculateBalance()
    {
        $userId  = $this->faker->randomDigit;
        $manager = $this->createManager();
        for ($i = 0; $i <= 50; $i++) {
            $value   = mt_rand(1, 100);
            $data    = [
                'order_id' => $this->faker->randomDigitNotNull,
                'site_id'  => $this->faker->randomDigitNotNull,
            ];
            $account = [
                'user_id' => $userId
            ];
            $i % 2 == 0 ? $manager->increase($account, $value, $data) : $manager->decrease($account, $value, $data);
        }
        $account = $this->getAccount($userId);
        $value   = $manager->calculateBalance($account->id);

        verify($account->value)->equals($value);
    }

    /**
     * Test revert user balance
     */
    public function testRevertTransaction()
    {
        $transactionId = mt_rand(1, 10);
        $manager       = $this->createManager();

        for ($i = 0; $i <= 10; $i++) {
            $value   = mt_rand(1, 100);
            $data    = [
                'order_id' => $this->faker->randomDigitNotNull,
                'site_id'  => $this->faker->randomDigitNotNull,
            ];
            $account = [
                'user_id' => $this->userId
            ];
            $manager->increase($account, $value, $data);
        }

        $beforeRevert  = $this->getTransaction($transactionId);
        $transactionId = $manager->revert($transactionId);
        $afterRevert   = $this->getTransaction($transactionId);

        verify(abs($afterRevert->value))->equals(abs($beforeRevert->value));
        verify(abs($afterRevert->id))->notEquals(abs($beforeRevert->id));
    }

    /**
     * Testing revert transactions
     */
    public function testManualRevertTransaction()
    {
        $manager   = $this->createManager();
        $accountId = 1;

        $manager->increase($accountId, 150); // transId #1 - 150
        $manager->increase($accountId, 730); // transId #1 - 730
        $manager->increase($accountId, 500); // transId #3 - 500
        $manager->decrease($accountId, 199); // transId #4 - 199

        $balance = $manager->calculateBalance($accountId); // 1181
        verify($balance)->equals(1181);

        $manager->revert(1);

        $balanceStepTwo = $manager->calculateBalance($accountId); // 1031
        verify($balanceStepTwo)->equals(1031);

        $manager->revert(4);

        $balanceStepThree = $manager->calculateBalance($accountId); // 1230
        verify($balanceStepThree)->equals(1230);
    }

    /**
     * Test roll back transaction. Will throw an exception.
     */
    public function testRollBackTransaction()
    {
        $manager      = $this->createManager();
        $transactions = $this->tester->grabNumRecords(Transaction::tableName());
        codecept_debug($this->tester->grabNumRecords(Transaction::tableName()));

        $manager->on(AbstractManager::EVENT_BEFORE_CREATE_TRANSACTION, function ($e) {
            throw new Exception("Die transaction");
        });

        try {
            $manager->increase($this->userId, 80000);
        } catch (\Exception $exception) {
            codecept_debug("I'm here hater");
        }

        verify($transactions)->equals($this->tester->grabNumRecords(Transaction::tableName()));
    }

    /**
     * Test revert not existing transaction
     */
    public function testRevertNotExistingTransaction()
    {
        $this->expectException(InvalidParamException::class);
        $manager = $this->createManager();
        $manager->revert($this->faker->randomDigitNotNull);
    }
}
