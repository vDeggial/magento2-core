<?php
namespace Hapex\Core\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class SalesRuleHelper extends BaseHelper
{
    protected $ruleRepository;

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $objectManager);
        $this->ruleRepository = $this->generateClassObject("Magento\SalesRule\Model\RuleRepository");
    }

    protected function getSalesRules($salesRuleId = 0)
    {
        $rule = null;
        switch (is_array($salesRuleId)) {
          case true:
              $rule = [];
              foreach ($salesRuleId as $ruleId) {
                  $rule[] = $this->ruleRepository->getById($ruleId);
              }
              break;

          default:
              $rule = $salesRuleId != 0 ? $this->ruleRepository->getById($salesRuleId) : null;
              break;
      }
        return $rule;
    }

    protected function validateSalesRule(&$qty = 0, &$promoAvailable = false, &$salesRule = null)
    {
        switch (!$promoAvailable && $salesRule && $this->getSalesRuleStatus($salesRule)) {
          case true:
              $this->setSalesRuleStatus($salesRule, 0);
              break;
      }

        switch ($salesRule && !$this->isValidRuleDates($salesRule)) {
          case true:
              $qty = 0;
              $promoAvailable = false;
              break;
      }
    }

    private function getSalesRuleStatus($rule)
    {
        switch ($rule !== null) {
          case true:
              return $rule->getIsActive();
      }
    }

    private function isValidRuleDates($rule)
    {
        switch ($rule !== null) {
          case true:
              $fromDate = $rule->getFromDate();
              $toDate = $rule->getToDate();
              return $this->isCurrentDateWithinRange($fromDate, $toDate);

          default:
              return false;
      }
    }

    private function setSalesRuleStatus($rule, $status = 0)
    {
        switch ($rule !== null) {
          case true:
              $rule->setIsActive($status);
              $this->ruleRepository->save($rule);
              break;
      }
    }
}
