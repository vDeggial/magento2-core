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

    protected function getSalesRuleStatus($rule)
    {
        switch ($rule !== null) {
          case true:
              return $rule->getIsActive();
      }
    }

    protected function isValidRuleDates($rule)
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

    protected function setSalesRuleStatus($rule, $status = 0)
    {
        switch ($rule !== null) {
          case true:
              $rule->setIsActive($status);
              $this->ruleRepository->save($rule);
              break;
      }
    }
}
