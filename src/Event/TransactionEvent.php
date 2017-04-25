<?php
/**
 * TransactionEvent
 * @version     0.3.2
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Event;

use yii\base\Event;

/**
 * Class TransactionEvent
 */
class TransactionEvent extends Event
{
    public $accountId;
    public $transactionData;
    public $transactionId;
}
