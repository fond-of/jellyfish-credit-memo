<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapper;
use FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use FondOfSpryker\Zed\JellyfishCreditMemo\JellyfishCreditMemoDependencyProvider;
use Orm\Zed\CreditMemo\Persistence\FosCreditMemoQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\CompanyRole\CompanyRoleConfig getConfig()
 * @method \Spryker\Zed\CompanyRole\Persistence\CompanyRoleEntityManagerInterface getEntityManager()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface getRepository()
 */
class JellyfishCreditMemoPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\CreditMemo\Persistence\FosCreditMemoQuery
     */
    public function createCreditMemoQuery(): FosCreditMemoQuery
    {
        return FosCreditMemoQuery::create();
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function getSalesOrderItemQuery(): SpySalesOrderItemQuery
    {
        return $this->getProvidedDependency(JellyfishCreditMemoDependencyProvider::PROPEL_QUERY_SALES_ORDER_ITEM);
    }
    
    /**
     * @return \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    public function createJellyfishCreditMemoMapper(): JellyfishCreditMemoMapperInterface
    {
        return new JellyfishCreditMemoMapper();
    }
}
