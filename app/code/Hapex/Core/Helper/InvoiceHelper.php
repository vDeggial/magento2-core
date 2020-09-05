<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class InvoiceHelper extends BaseHelper
{
    protected $tableInvoice;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableInvoice = $this->helperDb->getSqlTableName("sales_invoice");
    }
}
