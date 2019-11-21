<?php declare(strict_types=1);


namespace Spagi\EmailParser;

use Illuminate\Foundation\Application;
use Webklex\PHPIMAP\ClientManager;

class ImapClientFactory
{
    private $config;

    /**
     * Create a new client manager instance.
     *
     */
    public function __construct()
    {
        $this->config = config('emailparser');
    }

    /**
     * @return \Webklex\PHPIMAP\ClientManager
     */
    public function getClientWithConfig(): ClientManager
    {
        return new ClientManager($this->config);
    }
}
