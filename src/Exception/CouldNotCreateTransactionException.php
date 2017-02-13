<?php
/**
 * CouldNotCreateTransactionException
 * @version     0.1.1
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

namespace Tapakan\Balance\Exception;

use yii\base\InvalidConfigException;

/**
 * Class TransactionException
 */
class CouldNotCreateTransactionException extends InvalidConfigException
{
    /**
     * @@inheritdoc
     */
    public function getName()
    {
        return 'Could not create transaction';
    }

}
