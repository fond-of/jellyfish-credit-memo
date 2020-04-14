<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use Generated\Shared\Transfer\CreditMemoCollectionTransfer;

interface JellyfishCreditMemoRepositoryInterface
{
    /**
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     */
    public function findPendingCreditMemoCollection(): CreditMemoCollectionTransfer;
}
