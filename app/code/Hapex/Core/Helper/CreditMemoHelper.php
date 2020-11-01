<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\CreditmemoRepository;

class CreditMemoHelper extends BaseHelper
{
    protected $tableCreditMemo;
    protected $helperItem;
    protected $helperGrid;
    protected $helperComment;
    protected $creditMemoRepository;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CreditMemoItemHelper $helperItem,
        CreditMemoGridHelper $helperGrid,
        CreditMemoCommentHelper $helperComment,
        CreditmemoRepository $creditMemoRepository
    ) {
        parent::__construct($context, $objectManager);
        $this->tableCreditMemo = $this->helperDb->getSqlTableName("sales_creditmemo");
        $this->helperItem = $helperItem;
        $this->helperGrid = $helperGrid;
        $this->helperComment = $helperComment;
        $this->creditMemoRepository = $creditMemoRepository;
    }
}
