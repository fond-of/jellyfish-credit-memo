<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo;

use FondOfSpryker\Shared\JellyfishCreditMemo\JellyfishCreditMemoConstants;
use FondOfSpryker\Zed\Jellyfish\JellyfishConfig;

class JellyfishCreditMemoConfig extends JellyfishConfig
{
    protected const DEFAULT_SALES_ORDER_ITEM_STATE_REFUNDED = 'refunded';
    
    /**
     * @return string
     */
    public function getSalesOrderItemStateRefunded()
    {
        return $this->get(
            JellyfishCreditMemoConstants::SALES_ORDER_ITEM_STATE_REFUNDED,
            static::DEFAULT_SALES_ORDER_ITEM_STATE_REFUNDED
        );
    }
}
