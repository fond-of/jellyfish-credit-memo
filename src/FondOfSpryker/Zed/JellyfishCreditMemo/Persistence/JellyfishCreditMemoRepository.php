<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CreditMemoCollectionTransfer;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer;
use Orm\Zed\CreditMemo\Persistence\FosCreditMemo;
use Orm\Zed\CreditMemo\Persistence\FosCreditMemoItem;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoPersistenceFactory getFactory()
 */
class JellyfishCreditMemoRepository extends AbstractRepository implements JellyfishCreditMemoRepositoryInterface
{
    protected const JELLYFISH_PENDING_EXPORT_STATE = null;
    protected const FIELD_CREATED_AT = 'created_at';
    protected const FIELD_UPDATED_AT = 'updated_at';

    /**
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     */
    public function findPendingCreditMemoCollection(): CreditMemoCollectionTransfer
    {
        $query = $this->getFactory()
            ->createCreditMemoQuery()
            ->leftJoinWithAddress()
            ->leftJoinWithFosCreditMemoItem()
            ->leftJoinWithSpyLocale()
            ->filterByJellyfishExportState(static::JELLYFISH_PENDING_EXPORT_STATE);

        $entityTransferCollection = $this->buildQueryFromCriteria($query)->find();

        return $this->mapCreditMemoEntityTransferCollectionToCreditMemoCollectionTransfer($entityTransferCollection);
    }

    /**
     * @param int $salesOrderId
     * @param array $salesOrderItemIds
     *
     * @return \Generated\Shared\Transfer\CreditMemoTransfer|null
     */
    public function findCreditMemoBySalesOrderIdAndSalesOrderItemIds(
        int $salesOrderId,
        array $salesOrderItemIds
    ): ?CreditMemoTransfer {
        $query = $this->getFactory()
            ->createCreditMemoQuery()
            ->useFosCreditMemoItemQuery()->filterByFkSalesOrderItem_In($salesOrderItemIds)
            ->endUse()
            ->filterByFkSalesOrder($salesOrderId);

        $results = $query->find();

        if ($results === null || $results->getData() === null || $results->getData() === []) {
            return null;
        }

        /** @var \Orm\Zed\CreditMemo\Persistence\FosCreditMemo $fosCreditMemo */
        foreach ($results->getData() as $fosCreditMemo) {
            $items = $fosCreditMemo->getFosCreditMemoItems();
            if (count($items) === count($salesOrderItemIds)) {
                $found = true;
                foreach ($items as $creditMemoItem) {
                    if (in_array($creditMemoItem->getFkSalesOrderItem(), $salesOrderItemIds) === false) {
                        $found = false;

                        break;
                    }
                }
                if ($found === true) {
                    return $this->mapCreditMemoEntityToCreditMemoTransfer($fosCreditMemo);
                }
            }
        }

        return null;
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\CreditMemoTransfer|null
     */
    public function findCreditMemoBySalesOrderItemId(int $idSalesOrderItem): ?CreditMemoTransfer
    {
        $item = $this->getFactory()->createCreditMemoQuery()->useFosCreditMemoItemQuery()->findOneByFkSalesOrderItem($idSalesOrderItem);

        if ($item === null) {
            return null;
        }

        $fosCreditMemo = $this->getFactory()->createCreditMemoQuery()->findOneByIdCreditMemo($item->getFkCreditMemo());

        if ($fosCreditMemo === null) {
            return null;
        }

        return $this->mapCreditMemoEntityToCreditMemoTransfer($fosCreditMemo);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemStateTransfer|null
     */
    public function findSalesOrderItemStateByIdSalesOrderItem(ItemTransfer $itemTransfer): ?ItemStateTransfer
    {
        $query = $this->getFactory()
            ->getSalesOrderItemQuery()
            ->leftJoinWithState()
            ->filterByIdSalesOrderItem($itemTransfer->getFkSalesOrderItem());

        /** @var \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer $salesOrderItemEntityTransfer */
        $salesOrderItemEntityTransfer = $this->buildQueryFromCriteria($query)->findOne();

        if ($salesOrderItemEntityTransfer === null) {
            return null;
        }

        return $this->mapSpySalesOrderItemEntityTransferToItemStateTransfer($salesOrderItemEntityTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer|array $entityTransferCollection
     *
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     */
    protected function mapCreditMemoEntityTransferCollectionToCreditMemoCollectionTransfer(
        array $entityTransferCollection
    ): CreditMemoCollectionTransfer {
        $creditMemoEntityCollectionTransfer = new CreditMemoCollectionTransfer();

        foreach ($entityTransferCollection as $creditMemoEntityTransfer) {
            $creditMemoTransfer = $this->mapCreditMemoEntityToCreditMemoTransfer($creditMemoEntityTransfer);

            $creditMemoEntityCollectionTransfer->addCreditMemo($creditMemoTransfer);
        }

        return $creditMemoEntityCollectionTransfer;
    }

    /**
     * @param $creditMemoItemEntityTransferCollection
     *
     * @return \Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer[]|\ArrayObject
     */
    protected function getCreditMemoItems($creditMemoItemEntityTransferCollection): ArrayObject
    {
        $items = new ArrayObject();
        foreach ($creditMemoItemEntityTransferCollection as $creditMemoItemEntityTransfer) {
            $items->append(
                $this->mapCreditMemoItemEntityTransferToItemTransfer($creditMemoItemEntityTransfer)
            );
        }

        return $items;
    }

    /**
     * @param \Orm\Zed\CreditMemo\Persistence\FosCreditMemoItem $fosCreditMemoItemEntityTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function mapCreditMemoItemEntityTransferToItemTransfer(
        FosCreditMemoItem $fosCreditMemoItemEntityTransfer
    ): ItemTransfer {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setName($fosCreditMemoItemEntityTransfer->getName());
        $itemTransfer->setSku($fosCreditMemoItemEntityTransfer->getSku());
        $itemTransfer->setQuantity($fosCreditMemoItemEntityTransfer->getQuantity());
        $itemTransfer->setFkCreditMemo($fosCreditMemoItemEntityTransfer->getFkCreditMemo());
        $itemTransfer->setIdCreditMemoItem($fosCreditMemoItemEntityTransfer->getIdCreditMemoItem());
        $itemTransfer->setFkSalesOrderItem($fosCreditMemoItemEntityTransfer->getFkSalesOrderItem());

        return $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer $spySalesOrderItemEntityTransfer
     *
     * @return \Generated\Shared\Transfer\ItemStateTransfer
     */
    protected function mapSpySalesOrderItemEntityTransferToItemStateTransfer(
        SpySalesOrderItemEntityTransfer $spySalesOrderItemEntityTransfer
    ): ItemStateTransfer {
        $itemStateTransfer = new ItemStateTransfer();
        $itemStateTransfer->setName($spySalesOrderItemEntityTransfer->getState()->getName());
        $itemStateTransfer->setIdSalesOrder($spySalesOrderItemEntityTransfer->getFkSalesOrder());

        return $itemStateTransfer;
    }

    /**
     * @param \Orm\Zed\CreditMemo\Persistence\FosCreditMemo $creditMemoEntityTransfer
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    protected function mapCreditMemoEntityTransferToLocaleTransfer(
        FosCreditMemo $creditMemoEntityTransfer
    ): LocaleTransfer {
        $localeTransfer = new LocaleTransfer();

        $spyLocale = $creditMemoEntityTransfer->getSpyLocale();

        if ($spyLocale !== null) {
            $localeTransfer->fromArray($spyLocale->toArray(), true);
        }

        return $localeTransfer;
    }

    /**
     * @param \Orm\Zed\CreditMemo\Persistence\FosCreditMemo $creditMemoEntityTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function mapCreditMemoToAddressTransfer(
        FosCreditMemo $creditMemoEntityTransfer
    ): AddressTransfer {
        $addressTransfer = new AddressTransfer();

        $fosCreditMemoAddress = $creditMemoEntityTransfer->getAddress();

        if ($fosCreditMemoAddress !== null) {
            $addressTransfer->fromArray($fosCreditMemoAddress->toArray(), true);
        }

        return $addressTransfer;
    }

    /**
     * @param \Orm\Zed\CreditMemo\Persistence\FosCreditMemoItem[] $creditMemoItems
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function mapCreditMemoItemsToItemTransferCollection(
        array $creditMemoItems
    ): ArrayObject {
        $collection = new ArrayObject();

        foreach ($creditMemoItems as $creditMemoItem) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($creditMemoItem->toArray(), true);
            $collection->append($itemTransfer);
        }

        return $collection;
    }

    /**
     * @param \Orm\Zed\CreditMemo\Persistence\FosCreditMemo $creditMemoEntityTransfer
     *
     * @return \Generated\Shared\Transfer\CreditMemoTransfer
     */
    protected function mapCreditMemoEntityToCreditMemoTransfer(FosCreditMemo $creditMemoEntityTransfer): CreditMemoTransfer
    {
        $creditMemoTransfer = (new CreditMemoTransfer())
            ->fromArray($creditMemoEntityTransfer->toArray(), true);

        $creditMemoTransfer->setLocale($this->mapCreditMemoEntityTransferToLocaleTransfer($creditMemoEntityTransfer));
        $creditMemoTransfer->setAddress($this->mapCreditMemoToAddressTransfer($creditMemoEntityTransfer));
        $creditMemoTransfer->setItems(
            $this->getCreditMemoItems($creditMemoEntityTransfer->getFosCreditMemoItems())
        );

//        $virtualPropertiesCollection = $creditMemoEntityTransfer->virtualProperties();
//
//        if (isset($virtualPropertiesCollection[static::FIELD_CREATED_AT])) {
//            $creditMemoTransfer->setCreatedAt($virtualPropertiesCollection[static::FIELD_CREATED_AT]);
//        }
//
//        if (isset($virtualPropertiesCollection[static::FIELD_UPDATED_AT])) {
//            $creditMemoTransfer->setUpdatedAt($virtualPropertiesCollection[static::FIELD_UPDATED_AT]);
//        }
        return $creditMemoTransfer;
    }
}
