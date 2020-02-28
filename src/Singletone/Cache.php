<?php


namespace cbInformer\Singletone;

use DivineOmega\DOFileCache\DOFileCache;
use Exception;

class Cache
{
    private static ?DOFileCache $instance = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): DOFileCache
    {
        if (static::$instance instanceof DOFileCache) {
            return static::$instance;
        }
        static::$instance = new DOFileCache();
        static::$instance->changeConfig(["cacheDirectory" => BASE_DIR . '/tmp/apicache/']);
        return static::$instance;
    }
}
