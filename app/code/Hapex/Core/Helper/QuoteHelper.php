<?php
namespace Hapex\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class QuoteHelper extends BaseHelper
{
    protected $tableQuote;
    protected $tableQuoteItem;
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->tableQuote = $this->helperDb->getSqlTableName('quote');
        $this->tableQuoteItem = $this->helperDb->getSqlTableName('quote_item');
    }

    public function getQuote($quoteId)
    {
        return $this->getQuoteById($quoteId);
    }

    public function quoteExists($quoteId)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableQuote . " quote where quote.entity_id = $quoteId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function getQuoteById($quoteId)
    {
        $quote = null;
        try {
            $quoteFactory = $this->generateClassObject(\Magento\Quote\Model\QuoteFactory::class);
            $quote = $this->quoteExists($quoteId) ? $quoteFactory->create()->load($quoteId) : null;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $quote = null;
        } finally {
            return $quote;
        }
    }
}
