<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo;

use FondOfSpryker\Shared\JellyfishCreditMemo\JellyfishCreditMemoConstants;
use FondOfSpryker\Zed\Jellyfish\JellyfishConfig;

class JellyfishCreditMemoConfig extends JellyfishConfig
{
    /**
     * @return string
     */
    public function getSalesOrderItemStateRefunded()
    {
        return $this->get(JellyfishCreditMemoConstants::SALES_ODRE_ITEM_STATE_REFUNDED, 'refunded');
    }
}
