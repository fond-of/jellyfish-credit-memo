<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManagerInterface;
use Generated\Shared\Transfer\JellyfishCreditMemoTransfer;
use Orm\Zed\CreditMemo\Persistence\Map\FosCreditMemoTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoPersistenceFactory getFactory()
 */
class JellyfishCreditMemoEntityManager extends AbstractEntityManager implements JellyfishCreditMemoEntityManagerInterface
{
    protected const COLUMN_JELLYFISH_EXPORT_STATE = 'JellyfishExportState';
    
    /**
     * @param \Spryker\Zed\CompanyUser\Persistence\JellyfishCreditMemoTransfer $jellyfishCreditMemoTransfer
     *
     * @return \Spryker\Zed\CompanyUser\Persistence\JellyfishCreditMemoTransfer
     */
    public function updateExportState(
        JellyfishCreditMemoTransfer $jellyfishCreditMemoTransfer
    ): JellyfishCreditMemoTransfer {
        $this->getFactory()
            ->createCreditMemoQuery()
            ->filterByIdCreditMemo(
                $jellyfishCreditMemoTransfer->getId()
            )->update([
                static::COLUMN_JELLYFISH_EXPORT_STATE => $jellyfishCreditMemoTransfer->getExportState(),
            ]);

        return $jellyfishCreditMemoTransfer;
    }
}
