<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use ArrayObject;
use FondOfPHP\GoogleCustomSearch\Result\Item;
use Generated\Shared\Transfer\CreditMemoCollectionTransfer;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\FosCreditMemoEntityTransfer;
use Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\SpyOmsOrderItemStateEntityTransfer;
use Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer;
use Orm\Zed\CreditMemo\Persistence\Map\FosCreditMemoAddressTableMap;
use Orm\Zed\CreditMemo\Persistence\Map\FosCreditMemoTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoPersistenceFactory getFactory()
 */
class JellyfishCreditMemoRepository extends AbstractRepository implements JellyfishCreditMemoRepositoryInterface
{
    protected const JELLYFISH_PENDING_EXPORT_STATE = null;

    /**
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     * 
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
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
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @return \Generated\Shared\Transfer\ItemStateTransfer|null
     * 
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function findSalesOrderItemStateByIdSalesOrderItem(ItemTransfer $itemTransfer): ?ItemStateTransfer
    {
        $query = $this->getFactory()
            ->getSalesOrderItemQuery()
            ->leftJoinWithState()
            ->filterByIdSalesOrderItem($itemTransfer->getFkSalesOrderItem());

        /** @var SpySalesOrderItemEntityTransfer $salesOrderItemEntityTransfer*/
        $salesOrderItemEntityTransfer = $this->buildQueryFromCriteria($query)->findOne();

        if ($salesOrderItemEntityTransfer === null) {
            return null;
        }

        return $this->mapSpySalesOrderItemEntityTransferToItemStateTransfer($salesOrderItemEntityTransfer);
    }

    /**
     * @param  \Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer $entityTransferCollection
     * @return \Generated\Shared\Transfer\CreditMemoCollectionTransfer
     */
    protected function mapCreditMemoEntityTransferCollectionToCreditMemoCollectionTransfer (
        array $entityTransferCollection
    ): CreditMemoCollectionTransfer{

        $creditMemoEntityCollectionTransfer = new CreditMemoCollectionTransfer();

        foreach ($entityTransferCollection as $creditMemoEntityTransfer)
        {
            /** @var \Generated\Shared\Transfer\FosCreditMemoEntityTransfer $creditMemoEntityTransfer*/
            $creditMemoTransfer = (new CreditMemoTransfer())
                ->fromArray($creditMemoEntityTransfer->toArray(), true);

            $creditMemoTransfer->setLocale($this->mapCreditMemoEntityTransferToLocaleTransfer($creditMemoEntityTransfer));
            $creditMemoTransfer->setItems(
                $this->getCreditMemoItems($creditMemoEntityTransfer->getFosCreditMemoItems())
            );

            $creditMemoEntityCollectionTransfer->addCreditMemo($creditMemoTransfer);
        }
        
        return $creditMemoEntityCollectionTransfer;
    }

    /**
     * @param $creditMemoItemEntityTransferCollection
     *
     * @return \Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer[]|ArrayObject
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
     * @param \Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer $fosCreditMemoItemEntityTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function mapCreditMemoItemEntityTransferToItemTransfer(
        FosCreditMemoItemEntityTransfer $fosCreditMemoItemEntityTransfer
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
     * @param \Generated\Shared\Transfer\FosCreditMemoEntityTransfer $creditMemoEntityTransfer
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    protected function mapCreditMemoEntityTransferToLocaleTransfer(
        FosCreditMemoEntityTransfer $creditMemoEntityTransfer
    ): LocaleTransfer {
        $localeTransfer = new LocaleTransfer();
        $localeTransfer->setIdLocale($creditMemoEntityTransfer->getFkLocale());
        $localeTransfer->setLocaleName($creditMemoEntityTransfer->getSpyLocale()->getLocaleName());

        return $localeTransfer;
    }

}
