<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CreditMemoItemHelper extends BaseHelper
{
    protected $tableCreditMemoItem;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableCreditMemoItem = $this->helperDb->getSqlTableName("sales_creditmemo_item");
    }
}
