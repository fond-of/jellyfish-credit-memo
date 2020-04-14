<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter;

interface CreditMemoExporterInterface
{
    /**
     * Export credit memos
     */
    public function export(): void;
}
