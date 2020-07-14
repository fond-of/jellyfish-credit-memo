<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Business\JellyfishCreditMemoBusinessFactory getFactory()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManagerInterface getEntityManager()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface getRepository()
 */
class JellyfishCreditMemoFacade extends AbstractFacade implements JellyfishCreditMemoFacadeInterface
{
    /**
     * @inheritDoc
     *
     * @api
     */
    public function exportCreditMemos(): void
    {
        $this->getFactory()->createCreditMemoExporter()->export();
    }

    /**
     * @param int $salesOderId
     * @param array $salesOrderItemIds
     *
     * @return void
     */
    public function exportCreditMemo(int $salesOderId, array $salesOrderItemIds): void
    {
        $this->getFactory()->createCreditMemoExporter()->exportBySalesOrderIdAndSalesOrderItemIds($salesOderId, $salesOrderItemIds);
    }
}
