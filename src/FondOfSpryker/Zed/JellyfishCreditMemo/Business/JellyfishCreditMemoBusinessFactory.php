<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporter;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporterInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapper;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepository getRepository()
 */
class JellyfishCreditMemoBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporterInterface
     */
    public function createCreditMemoExporter(): CreditMemoExporterInterface
    {
        return new CreditMemoExporter(
            $this->createJellyfishCreditMemoMapper(),
            $this->getRepository()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    protected function createJellyfishCreditMemoMapper(): JellyfishCreditMemoMapperInterface
    {
        return new JellyfishCreditMemoMapper();
    }

}
