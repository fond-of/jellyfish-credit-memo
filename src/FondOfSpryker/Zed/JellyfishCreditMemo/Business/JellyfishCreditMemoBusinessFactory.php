<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

use FondOfSpryker\Zed\Jellyfish\Business\Api\Adapter\AdapterInterface;
use FondOfSpryker\Zed\Jellyfish\Dependency\Service\JellyfishToUtilEncodingServiceInterface;
use FondOfSpryker\Zed\JellyfishB2B\Business\Api\Adapter\CompanyUserAdapter;
use FondOfSpryker\Zed\JellyfishB2B\JellyfishB2BDependencyProvider;
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
use Spryker\Zed\Oms\Business\OmsFacade;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepository getRepository()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoEntityManager getEntityManager()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoConfig getConfig()
 */
class JellyfishCreditMemoBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Business\Model\Exporter\CreditMemoExporterInterface
     * 
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
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
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
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
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createJellyfishCreditMemoMapper(): JellyfishCreditMemoMapperInterface
    {
        return new JellyfishCreditMemoMapper($this->getSalesFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\Jellyfish\Dependency\Service\JellyfishToUtilEncodingServiceInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getUtilEncodingService(): JellyfishToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(JellyfishCreditMemoDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \FondOfSpryker\Zed\JellyfishCreditMemo\Dependency\Facade\JellyfishCreditMemoToSalesFacadeInterface
     *
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): JellyfishCreditMemoToSalesFacadeInterface
    {
        return $this->getProvidedDependency(JellyfishCreditMemoDependencyProvider::FACADE_SALES);
    }
}
