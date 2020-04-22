<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter;

use ArrayObject;
use FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoConfig;
use FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManagerInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\ItemStateTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoTransfer;
use Generated\Shared\Transfer\JellyfishOrderTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Oms\Business\OmsFacadeInterface;
use Spryker\Zed\Sales\Business\SalesFacadeInterface;

class CreditMemoExporter implements CreditMemoExporterInterface
{
    use LoggerTrait;

    protected const CREDIT_MEMO_EXPORT_STATE_COMPLETE = 'complete';

    /**
     * @var \FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManagerInterface
     */
    protected $jellyfishCreditMemoEntityManager;

    /**
     * @var \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    protected $jellyfishCreditMemoMapper;

    /**
     * @var \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface
     */
    protected $jellyfishCreditMemoRepository;

    /**
     * @var \FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoConfig
     */
    protected $jellyfishCreditMemoConfig;

    /**
     * CreditMemoExporter constructor.
     *
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface $jellyfishCreditMemoMapper
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface $jellyfishCreditMemoRepository
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoConfig $jellyfishCreditMemoConfig
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManagerInterface $jellyfishCreditMemoEntityManager
     * @param \FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface $adapter
     */
    public function __construct(
        JellyfishCreditMemoMapperInterface $jellyfishCreditMemoMapper,
        JellyfishCreditMemoRepositoryInterface $jellyfishCreditMemoRepository,
        JellyfishCreditMemoConfig $jellyfishCreditMemoConfig,
        JellyfishCreditMemoEntityManagerInterface $jellyfishCreditMemoEntityManager,
        AdapterInterface $adapter
    )
    {
        $this->adapter = $adapter;
        $this->jellyfishCreditMemoRepository = $jellyfishCreditMemoRepository;
        $this->jellyfishCreditMemoMapper = $jellyfishCreditMemoMapper;
        $this->jellyfishCreditMemoConfig = $jellyfishCreditMemoConfig;
        $this->jellyfishCreditMemoEntityManager = $jellyfishCreditMemoEntityManager;
    }

    /**
     * Export data
     * 
     * @throws \Exception
     */
    public function export(): void
    {
        try {
            $creditMemoCollectionTransfer = $this->jellyfishCreditMemoRepository->findPendingCreditMemoCollection();

            if ($creditMemoCollectionTransfer === null 
                || $creditMemoCollectionTransfer->getCreditMemos()->count() === 0) {
                return;
            }

            foreach ($creditMemoCollectionTransfer->getCreditMemos() as $creditMemoTransfer) {
                
                if ($this->isValidForExport($creditMemoTransfer) === false) {
                    continue;
                }

                $jellyfishCreditMemo = $this->map($creditMemoTransfer);
                $this->adapter->sendRequest($jellyfishCreditMemo);
                
                $jellyfishCreditMemo->setExportState(static::CREDIT_MEMO_EXPORT_STATE_COMPLETE);
                $this->jellyfishCreditMemoEntityManager->updateExportState($jellyfishCreditMemo);

            }

        } catch (\Exception $exception) {
            $this->getLogger()->error(sprintf(
                'CreditMemo could not expoted to JellyFish! Message: %s', $exception->getMessage()), $exception->getTrace()
            );
            throw $exception;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CreditMemoTransfer $creditMemoTransfer
     *
     * @return \Generated\Shared\Transfer\JellyfishCreditMemoTransfer
     */
    protected function map(CreditMemoTransfer $creditMemoTransfer): JellyfishCreditMemoTransfer
    {
        $jellyfishCreditMemoTransfer = $this->jellyfishCreditMemoMapper
            ->mapCreditMemoTransferToJellyfishCreditMemoTransfer($creditMemoTransfer);

        return $jellyfishCreditMemoTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CreditMemoTransfer $creditMemoTransfer
     *
     * @return bool
     */
    protected function isValidForExport(CreditMemoTransfer $creditMemoTransfer): bool
    {
        if ($creditMemoTransfer->getItems()->count() === 0) {
            return false;
        }

        foreach ($creditMemoTransfer->getItems() as $itemTransfer) {
            if ($this->getSalesOrderItemStateName($itemTransfer) !==
                $this->jellyfishCreditMemoConfig->getSalesOrderItemStateRefunded()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getSalesOrderItemStateName(ItemTransfer $itemTransfer): string
    {
        $itemStateTransfer = $this->jellyfishCreditMemoRepository
            ->findSalesOrderItemStateByIdSalesOrderItem($itemTransfer);

        if ($itemStateTransfer === null) {
            return '';
        }

        return $itemStateTransfer->getName();
    }

}
