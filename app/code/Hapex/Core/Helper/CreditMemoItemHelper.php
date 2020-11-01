<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CreditMemoItemHelper extends BaseHelper
{
    protected $tableItem;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableItem = $this->helperDb->getSqlTableName("sales_creditmemo_item");
    }
}
