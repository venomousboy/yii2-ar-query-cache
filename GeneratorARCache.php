<?php

namespace venomousboy\yii2-ar-query-cache;

use Yii;

class GeneratorARCache
{
    const CACHE_PREFIX = 'cache_';

    private $command;
    private $class;
    private $method;
    private $token;

    public function cachedQuery($command, $class, $method, $token, $queryCacheDuration)
    {
        $this->command = $command;
        $this->class = $class;
        $this->method = $method;
        $this->token = $token;

        $hash = $this->generateHash();
        $key = $this->generateKey($hash);
        $cache = $this->get($key);

        if (!isset($cache[$hash])) {
            $rows = $command->$method();
            $cache[$hash] = $rows;
            Yii::$app->cache->set($key, $cache, $queryCacheDuration);
        } else {
            $rows = $cache[$hash];
        }

        return $rows;
    }

    private function get($key)
    {
        $cache = Yii::$app->cache->get($key);
        return $cache === false ? [] : $cache;
    }

    private function generateHash()
    {
        return md5($this->class . '-' . $this->method . '_' . $this->command->getRawSql());
    }

    private function generateKey($hash)
    {
        return self::CACHE_PREFIX . '_' . $this->token . '_' . $hash;
    }
}
