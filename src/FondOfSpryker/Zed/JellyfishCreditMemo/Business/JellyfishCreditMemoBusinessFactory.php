<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

use FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface;
use FondOfSpryker\Zed\Jellyfish\Dependency\Service\JellyfishToUtilEncodingServiceInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Api\Adapter\CreditMemoAdapter;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporter;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporterInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapper;
use FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoDependencyProvider;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepository getRepository()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManager getEntityManager()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoConfig getConfig()
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
            $this->getRepository(),
            $this->getConfig(),
            $this->getEntityManager(),
            $this->createCreditMemoAdapter()
        );
    }

    /**
     * @return \FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface
     */
    protected function createCreditMemoAdapter(): AdapterInterface
    {
        return new CreditMemoAdapter(
            $this->getUtilEncodingService(),
            $this->createHttpClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createHttpClient(): HttpClientInterface
    {
        return new HttpClient([
            'base_uri' => $this->getConfig()->getBaseUri(),
            'timeout' => $this->getConfig()->getTimeout(),
        ]);
    }

    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    protected function createJellyfishCreditMemoMapper(): JellyfishCreditMemoMapperInterface
    {
        return new JellyfishCreditMemoMapper($this->getSalesFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\Jellyfish\Dependency\Service\JellyfishToUtilEncodingServiceInterface
     */
    protected function getUtilEncodingService(): JellyfishToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(JellyfishCreditMemoDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface
     */
    protected function getSalesFacade(): JellyfishCreditMemoToSalesFacadeInterface
    {
        return $this->getProvidedDependency(JellyfishCreditMemoDependencyProvider::FACADE_SALES);
    }
}
