<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

interface JellyfishCreditMemoFacadeInterface
{
    /**
     * Export Credit Memos To Jellyfish
     */
    public function exportCreditMemos(): void;

}
