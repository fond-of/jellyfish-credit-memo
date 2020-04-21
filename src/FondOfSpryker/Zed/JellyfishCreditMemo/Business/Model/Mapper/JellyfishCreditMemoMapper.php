<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper;

use ArrayObject;
use FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoAddressTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoCustomerTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoItemTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoTransfer;
use Generated\Shared\Transfer\JellyfishOrderAddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Sales\Business\SalesFacadeInterface;

class JellyfishCreditMemoMapper implements JellyfishCreditMemoMapperInterface
{
    /**
     * @var \FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * JellyfishCreditMemoMapper constructor.
     *
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface $salesFacade
     */
    public function __construct(JellyfishCreditMemoToSalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * @param \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\CreditMemoTransfer $creditMemoTransfer
     *
     * @return \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoTransfer
     */
    public function mapCreditMemoTransferToJellyfishCreditMemoTransfer(
        CreditMemoTransfer $creditMemoTransfer
    ): JellyfishCreditMemoTransfer {
        $orderTransfer = $this->salesFacade->findOrderByIdSalesOrder($creditMemoTransfer->getFkSalesOrder());

        $jellyfishCreditMemo = new JellyfishCreditMemoTransfer();
        $jellyfishCreditMemo->setId($creditMemoTransfer->getIdCreditMemo())
            ->setOrderReference($creditMemoTransfer->getOrderReference())
            ->setFirstName($creditMemoTransfer->getFirstName())
            ->setLastName($creditMemoTransfer->getLastName())
            ->setEmail($creditMemoTransfer->getEmail())
            ->setCustomer($this->mapOrderTransferToJellyfishCreditMemoCustomerTransfer($orderTransfer))
            ->setAddress($this->mapAddressToJellyfishCreditMemoAddress($creditMemoTransfer->getAddress()))
            ->setItems($this->getJellyfishCreditMemoItems($creditMemoTransfer->getItems()))
            ->setLocale($creditMemoTransfer->getLocale()->getLocaleName())
            ->setStore($creditMemoTransfer->getStore())
            ->setCreatedAt(date('Y-m-d H:i:s', strtotime($creditMemoTransfer->getCreatedAt())))
            ->setUpdatedAt(date('Y-m-d H:i:s', strtotime($creditMemoTransfer->getUpdatedAt())));

        return $jellyfishCreditMemo;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\JellyfishCreditMemoTransfer
     */
    protected function mapAddressToJellyfishCreditMemoAddress(
        AddressTransfer $addressTransfer
    ): JellyfishCreditMemoAddressTransfer {

        $jellyfishCreditMemoAddress = new JellyfishCreditMemoAddressTransfer();
        $jellyfishCreditMemoAddress->fromArray($addressTransfer->toArray(), true);

        return $jellyfishCreditMemoAddress;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\JellyfishCreditMemoItemTransfer[]
     */
    protected function getJellyfishCreditMemoItems(ArrayObject $items): ArrayObject
    {
        $jellyfishCreditMemoItems = new ArrayObject();

        if ($items->count() === 0) {
            return $jellyfishCreditMemoItems;
        }

        foreach ($items as $itemTransfer) {
            $jellyfishCreditMemoItems->append($this->mapItemTransferToJellyfishCreditMemoItem($itemTransfer));
        }

        return $jellyfishCreditMemoItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\JellyfishCreditMemoTransfer
     */
    protected function mapItemTransferToJellyfishCreditMemoItem(
        ItemTransfer $itemTransfer
    ): JellyfishCreditMemoItemTransfer {

        $jellyfishCreditMemoItemTransfer = new JellyfishCreditMemoItemTransfer();
        $jellyfishCreditMemoItemTransfer->setName($itemTransfer->getName());
        $jellyfishCreditMemoItemTransfer->setSku($itemTransfer->getSku());
        $jellyfishCreditMemoItemTransfer->setQuantity($itemTransfer->getQuantity());

        return $jellyfishCreditMemoItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\JellyfishCreditMemoCustomerTransfer
     */
    protected function mapOrderTransferToJellyfishCreditMemoCustomerTransfer(
        OrderTransfer $orderTransfer
    ): JellyfishCreditMemoCustomerTransfer {
        $jellyfishCreditMemoCustomerTransfer = new JellyfishCreditMemoCustomerTransfer();
        $jellyfishCreditMemoCustomerTransfer->setEmail($orderTransfer->getCustomer()->getEmail());

        return $jellyfishCreditMemoCustomerTransfer;
    }
}