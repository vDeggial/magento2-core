<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class MpRewardsHelper extends BaseHelper
{
    protected $tableRewardTransactions;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableRewardTransactions = $this->helperDb->getSqlTableName("mageplaza_reward_transaction");
    }
}
