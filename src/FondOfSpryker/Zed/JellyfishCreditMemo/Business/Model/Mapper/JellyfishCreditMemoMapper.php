<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoAddressTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoItemTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoTransfer;
use Generated\Shared\Transfer\JellyfishOrderAddressTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;

class JellyfishCreditMemoMapper implements JellyfishCreditMemoMapperInterface
{
    /**
     * @param \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\CreditMemoTransfer $creditMemoTransfer
     *
     * @return \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoTransfer
     */
    public function mapCreditMemoTransferToJellyfishCreditMemoTransfer(
        CreditMemoTransfer $creditMemoTransfer
    ): JellyfishCreditMemoTransfer {

        $jellyfishCreditMemo = new JellyfishCreditMemoTransfer();
        $jellyfishCreditMemo->setId($creditMemoTransfer->getIdCreditMemo())
            ->setFirstName($creditMemoTransfer->getFirstName())
            ->setLastName($creditMemoTransfer->getLastName())
            ->setEmail($creditMemoTransfer->getEmail())
            ->setBillingAddress($this->mapAddressToJellyfishCreditMemoAddress($creditMemoTransfer->getAddress()))
            ->setItems($this->getJellyfishCreditMemoItems($creditMemoTransfer->getItems()));

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
}