<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Persistence;

use FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapper;
use FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleQuery;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToCompanyUserQuery;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToPermissionQuery;
use Orm\Zed\CreditMemo\Persistence\FosCreditMemoQuery;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRoleCompanyMapper;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRoleCompanyUserMapper;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRoleCompanyUserMapperInterface;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRoleMapper;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRoleMapperInterface;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRolePermissionMapper;
use Spryker\Zed\CompanyRole\Persistence\Mapper\CompanyRolePermissionMapperInterface;
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
     * @return \FondOfSpryker\Zed\Jellyfish\Business\Model\Mapper\JellyfishCreditMemoMapperInterface
     */
    public function createJellyfishCreditMemoMapper(): JellyfishCreditMemoMapperInterface
    {
        return new JellyfishCreditMemoMapper();
    }
}
