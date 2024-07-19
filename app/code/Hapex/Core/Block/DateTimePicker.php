<?php
namespace Hapex\Core\Block;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DateTimePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context  $context,
        Registry $coreRegistry,
        array    $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDateFormat(\Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT); //set date as per requirment
        $element->setTimeFormat('HH:mm:ss'); //set date and time as per your need
        $element->setShowsTime(true);
        return parent::render($element);
    }
}