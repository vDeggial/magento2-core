<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CreditMemoGridHelper extends BaseHelper
{
    protected $tableCreditMemoGrid;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableCreditMemoGrid = $this->helperDb->getSqlTableName("sales_creditmemo_grid");
    }
}
