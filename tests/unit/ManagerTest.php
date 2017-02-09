<?php
/**
 * ManagerTest
 * @version     0.0.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Tests\unit;

use Codeception\Specify;
use Tapakan\Balance\ManagerActiveRecord;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

/**
 * Class ManagerTest
 */
class ManagerTest extends TestCase
{
    use Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

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

        $this->userId = $this->faker->randomDigit;
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
        $account = $this->getAccount($this->userId);

        $manager->increase(['user_id' => $this->userId], 777, [
            'order_id' => $this->faker->randomDigitNotNull,
            'site_id'  => $this->faker->randomDigitNotNull,
        ]);
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
     * Method not implemented yet
     * @expectedException NotSupportedException
     */
    public function testRevert()
    {
        $this->expectException(NotSupportedException::class);
        $this->createManager()->revert(777);
    }
}
