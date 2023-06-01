<?php

namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\QuoteFactory;

class QuoteHelper extends BaseHelper
{
    protected $tableQuote;
    protected $tableQuoteItem;
    protected $quoteFactory;
    public function __construct(Context $context, ObjectManagerInterface $objectManager, QuoteFactory $quoteFactory)
    {
        parent::__construct($context, $objectManager);
        $this->quoteFactory = $quoteFactory->create();
        $this->tableQuote = $this->helperDb->getSqlTableName('quote');
        $this->tableQuoteItem = $this->helperDb->getSqlTableName('quote_item');
    }

    public function getQuote($quoteId = 0)
    {
        return $this->getQuoteById($quoteId);
    }

    public function quoteExists($quoteId = 0)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableQuote . " quote where quote.entity_id = $quoteId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function getQuoteById($quoteId = 0)
    {
        $quote = null;
        try {
            $quote = $this->quoteExists($quoteId) ? $this->quoteFactory->load($quoteId) : null;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $quote = null;
        } finally {
            return $quote;
        }
    }
}
