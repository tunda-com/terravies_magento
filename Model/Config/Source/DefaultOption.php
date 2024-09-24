<?php

namespace Terravives\Fee\Model\Config\Source;

class DefaultOption implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray() : array
    {
        return [['value' => 'min', 'label' => __('Min Value')],['value' => 'max', 'label' => __('Max Value')],['value' => 'no', 'label' => __('Not any')]];
    }

    public function toArray() : array
    {
        return ['min' => __('Min Value'),'max' => __('Max Value'),'no' => __('Not any')];
    }
}
