<?php

namespace Terravives\Fee\Model\Config\Source;

class DefaultOption implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray() : array
    {
        return [['value' => 'no', 'label'=> 'Default'], ['value' => 'min', 'label' => __('Min Value')],['value' => 'max', 'label' => __('Max Value')],['value' => 'median', 'label' => __('Median Value')]];
    }

    public function toArray() : array
    {
        return ['min' => __('Min Value'),'max' => __('Max Value'), 'median' => __('Median'),'no' => __('Not any')];
    }
}
