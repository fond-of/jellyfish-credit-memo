<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use Generated\Shared\Transfer\CreditMemoCollectionTransfer;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface JellyfishCreditMemoRepositoryInterface
{
    /**
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     */
    public function findPendingCreditMemoCollection(): CreditMemoCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemStateTransfer|null
     */
    public function findSalesOrderItemStateByIdSalesOrderItem(ItemTransfer $itemTransfer): ?ItemStateTransfer;

}
