<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Sales\Business\SalesFacadeInterface;

class JellyfishCreditMemoToSalesFacadeBridge implements JellyfishCreditMemoToSalesFacadeInterface
{
    /**
     * @var \Spryker\Zed\Sales\Business\SalesFacadeInterface 
     */
    protected $salesFacade;

    /**
     * JellyfishCreditMemoToSalesFacadeBridge constructor.
     *
     * @param \Spryker\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\OrderTransfer|null
     */
    public function findOrderByIdSalesOrder(int $idSalesOrder): ?OrderTransfer
    {
        return $this->salesFacade->findOrderByIdSalesOrder($idSalesOrder);
    }
}
