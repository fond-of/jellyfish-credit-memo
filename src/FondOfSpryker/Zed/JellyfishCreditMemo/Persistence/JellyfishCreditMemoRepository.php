<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\CreditMemoCollectionTransfer;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\FosCreditMemoItemEntityTransfer;
use Generated\Shared\Transfer\ItemTransfer;
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
            ->filterByJellyfishExportState(static::JELLYFISH_PENDING_EXPORT_STATE);

        $entityTransferCollection = $this->buildQueryFromCriteria($query)->find();

        return $this->mapCreditMemoEntityTransferCollectionToCreditMemoCollectionTransfer($entityTransferCollection);
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

        return $itemTransfer;
    }


}
