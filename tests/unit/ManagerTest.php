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
use Yii;
use yii\db\ActiveRecord;

/**
 * Class ManagerTest
 */
class ManagerTest extends TestCase
{
    use Specify;

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        parent::_before();
        $this->setupTestDbData();
    }

    /**
     * Setup tables for test ActiveRecord
     */
    protected function setupTestDbData()
    {
        $db = Yii::$app->getDb();
        // Structure :
        $table   = 'BalanceAccount';
        $columns = [
            'id'      => 'pk',
            'user_id' => 'integer',
            'value'   => 'integer DEFAULT 0',
        ];
        $db->createCommand()->createTable($table, $columns)->execute();
        $table   = 'BalanceTransaction';
        $columns = [
            'id'         => 'pk',
            'account_id' => 'integer',
            'value'      => 'integer',
            'data'       => 'text',
        ];
        $db->createCommand()->createTable($table, $columns)->execute();
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
        $this->specify("Increase balance on 50", function () {
            $this->createManager()->increase(1, 50);
            verify($this->getLastTransaction()->value)->equals(50);
        });

        $this->specify("Decrease balance on 25", function () {
            $this->createManager()->decrease(1, 25);
            verify($this->getLastTransaction()->value)->equals(-25);
        });
    }
}
