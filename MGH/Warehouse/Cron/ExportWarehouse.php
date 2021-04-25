<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MGH\Warehouse\Cron;

use Psr\Log\LoggerInterface;

class ExportWarehouse
{

    protected $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        // TODO : add export loginc in cron mode
        // We have just to call same command logic and give possibility for additional parmas
        $this->logger->addInfo("Cronjob ExportWarehouse is executed.");
    }
}

