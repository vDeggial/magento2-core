<?php
namespace Hapex\Core\Helper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class QuoteHelper extends BaseHelper
{
	public function __construct(Context $context, ObjectManagerInterface $objectManager)
	{

		parent::__construct($context, $objectManager);
	}
}
