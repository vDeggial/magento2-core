<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class CreditMemoCommentHelper extends BaseHelper
{
    protected $tableComment;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableComment = $this->helperDb->getSqlTableName("sales_creditmemo_comment");
    }
}
