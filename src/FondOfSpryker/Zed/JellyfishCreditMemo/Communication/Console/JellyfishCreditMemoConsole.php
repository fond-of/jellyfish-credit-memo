<?php

namespace FondOfSpryker\Zed\JellyfishCreditMemo\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Business\JellyfishCreditMemoFacadeInterface getFacade()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Persistence\JellyfishCreditMemoRepositoryInterface getRepository()
 * @method \FondOfSpryker\Zed\JellyfishCreditMemo\Communication\JellyfishCreditMemoCommunicationFactory getFactory()
 */
class JellyfishCreditMemoConsole extends Console
{
    private const COMMAND_NAME = 'jellyfish:credit-memo:export';
    private const DESCRIPTION = 'Exports credit memo entries to jellyfish';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getFacade()->exportCreditMemos();
    }
}
