<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Business\JellyfishCreditMemoBusinessFactory getFactory()
 */
class JellyfishCreditMemoFacade extends AbstractFacade implements JellyfishCreditMemoFacadeInterface
{
    /**
     * @inheritDoc
     *
     * @api
     */
    public function exportCreditMemos(): void
    {
        $this->getFactory()->createCreditMemoExporter()->export();
    }
}
