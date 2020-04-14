<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter;

use ArrayObject;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface;
use Generated\Shared\Transfer\CreditMemoTransfer;
use Generated\Shared\Transfer\JellyfishCreditMemoTransfer;
use Generated\Shared\Transfer\JellyfishOrderTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreditMemoExporter implements CreditMemoExporterInterface
{
    use LoggerTrait;

    /**
     * @var \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    protected $jellyfishCreditMemoMapper;

    /**
     * @var \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface
     */
    protected $jellyfishCreditMemoRepository;

    /**
     * CreditMemoExporter constructor.
     *
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface $jellyfishCreditMemoMapper
     * @param \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface $jellyfishCreditMemoRepository
     */
    public function __construct(
        JellyfishCreditMemoMapperInterface $jellyfishCreditMemoMapper,
        JellyfishCreditMemoRepositoryInterface $jellyfishCreditMemoRepository
    )
    {
        $this->jellyfishCreditMemoRepository = $jellyfishCreditMemoRepository;
        $this->jellyfishCreditMemoMapper = $jellyfishCreditMemoMapper;
    }

    /**
     * @throws \Exception
     */
    public function export(): void
    {
        $creditMemoCollectionTransfer = $this->jellyfishCreditMemoRepository->findPendingCreditMemoCollection();

        if ($creditMemoCollectionTransfer === null || $creditMemoCollectionTransfer->getCreditMemos()->count() === 0) {
            return;
        }
        
        try {

            foreach ($creditMemoCollectionTransfer->getCreditMemos() as $creditMemoTransfer) {
                $jellyfishCreditMemo = $this->map($creditMemoTransfer);
                $this->adapter->sendRequest($jellyfishCreditMemo);
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

}
