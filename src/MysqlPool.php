<?php

namespace EasySwoole\MysqliPool;

use EasySwoole\Mysqli\Config;
use EasySwoole\Pool\MagicPool;

class MysqlPool extends MagicPool
{
    public function __construct(Config $config, ?string $cask = null)
    {
        parent::__construct(function () use ($config, $cask) {
            if ($cask) {
                return new $cask($config);
            }

            return new \EasySwoole\Mysqli\Client($config);
        }, null);
    }
}
