<?php

namespace venomousboy\yii2-ar-query-cache;

use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;

class CachedActiveQuery extends ActiveQuery implements ActiveQueryInterface
{
    private $param;
    private $cache;

    /**
     * CachedActiveQuery constructor.
     * @param string $modelClass
     * @param string $param
     * @param array $config
     */
    public function __construct($modelClass, $param, GeneratorARCache $generatorARCache, array $config = [])
    {
        $this->param = $param;
        $this->cache = $generatorARCache;
        parent::__construct($modelClass, $config);
    }

    /**
     * @param null $db
     * @return array|null|\yii\db\ActiveRecord|\yii\db\ActiveRecordInterface
     * @throws \yii\db\Exception
     */
    public function one($db = null)
    {
        $command = $this->createCommand($db);
        if ($this->queryCacheDuration) {
            $row = $this->cache->cachedQuery(
                $command,
                $this->modelClass,
                'queryOne',
                $this->param,
                $this->queryCacheDuration
            );
        } else {
            $row = $command->queryOne();
        }

        if ($row !== false) {
            $models = $this->populate([$row]);
            return reset($models) ?: null;
        }

        return null;
    }

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     * @throws \yii\db\Exception
     */
    public function all($db = null)
    {
        $command = $this->createCommand($db);
        if ($this->queryCacheDuration) {
            $rows = $this->cache->cachedQuery(
                $command,
                $this->modelClass,
                'queryAll',
                $this->param,
                $this->queryCacheDuration
            );
        } else {
            $rows = $command->queryAll();
        }

        return $this->populate($rows);
    }
}
