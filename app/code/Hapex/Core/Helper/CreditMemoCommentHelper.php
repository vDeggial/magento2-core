<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CreditMemoCommentHelper extends BaseHelper
{
    protected $tableCreditMemoComment;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableCreditMemoComment = $this->helperDb->getSqlTableName("sales_creditmemo_comment");
    }
}
