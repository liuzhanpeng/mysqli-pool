<?php

namespace EasySwoole\MysqliPool;

use EasySwoole\Component\Singleton;
use EasySwoole\Mysqli\Client;
use EasySwoole\Mysqli\Config;
use EasySwoole\Pool\Config as PoolConfig;

class Mysql
{
    use Singleton;

    private $list = [];

    function register(string $name, Config $config, ?string $cask = null): PoolConfig
    {
        if (isset($this->list[$name])) {
            //已经注册，则抛出异常
            throw new MysqlPoolException("mysqlpool:{$name} is already been register");
        }

        if ($cask) {
            $ref = new \ReflectionClass($cask);
            if (!$ref->isSubclassOf(Client::class)) {
                throw new MysqlPoolException('cask {$cask} not a sub class of EasySwoole\Mysqli\Client;');
            }
        }

        $pool = new MysqlPool($config, $cask);
        $this->list[$name] = $pool;

        return $pool->getConfig();
    }

    static function defer(string $name, $timeout = null): ?MysqlPool
    {
        $pool = static::getInstance()->pool($name);
        if ($pool) {
            return $pool::defer($timeout);
        } else {
            return null;
        }
    }

    static function invoke(string $name, callable $call, float $timeout = null)
    {
        $pool = static::getInstance()->pool($name);
        if ($pool) {
            return $pool->invoke($call, $timeout);
        } else {
            return null;
        }
    }

    public function get(string $name): ?MysqlPool
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }

    public function pool(string $name): ?MysqlPool
    {
        return $this->get($name);
    }
}
