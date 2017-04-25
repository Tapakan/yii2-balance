<?php
/**
 * ManagerInterface
 * @version     0.3.2
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance;

/**
 * Interface ManagerInterface
 */
interface ManagerInterface
{
    /**
     * Increase account current balance
     *
     * @param integer|array $account Id or condition to find account
     * @param float|integer $value   Amount to increase
     * @param array         $data    Additional data
     *
     * @return integer Transaction id
     */
    public function increase($account, $value, $data = []);

    /**
     * Decrease account current balance
     *
     * @param integer|array $account Id or condition to find account
     * @param float|integer $value   Amount to increase
     * @param array         $data    Additional data
     *
     * @return integer Transaction id
     */
    public function decrease($account, $value, $data = []);

    /**
     * Calculates all transactions
     *
     * @param integer|array $account Id or condition to find account
     *
     * @return mixed
     */
    public function calculateBalance($account);

    /**
     * Revert account balance value to tra
     *
     * @param integer|array $transactionId Id of transaction
     * @param array         $data          Additional data
     *
     * @return mixed
     */
    public function revert($transactionId, $data = []);
}
