<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/6/5
 * Time: 上午9:34
 */

namespace app\common\helpers;


use Illuminate\Support\Str;

class Cache
{

    public static function setUniacid()
    {
        \Log::debug('---------cache uniacid--------', \YunShop::app()->uniacid);
        return \YunShop::app()->uniacid . '_';
    }

    /**
     * Get a cache store instance by name.
     *
     * @param string|null $name
     * @return mixed
     * @static
     */
    public static function store($name = null)
    {
        return \Cache::store($name);
    }

    /**
     * Get a cache driver instance.
     *
     * @param string $driver
     * @return mixed
     * @static
     */
    public static function driver($driver = null)
    {
        return \Cache::driver($driver);
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param \Illuminate\Contracts\Cache\Store $store
     * @return \Illuminate\Cache\Repository
     * @static
     */
    public static function repository($store)
    {
        return \Cache::repository($store);
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     * @static
     */
    public static function getDefaultDriver()
    {
        return \Cache::getDefaultDriver();
    }

    /**
     * Set the default cache driver name.
     *
     * @param string $name
     * @return void
     * @static
     */
    public static function setDefaultDriver($name)
    {
        \Cache::setDefaultDriver($name);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver
     * @param \Closure $callback
     * @return $this
     * @static
     */
    public static function extend($driver, $callback)
    {
        return \Cache::extend($driver, $callback);
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     * @static
     */
    public static function setEventDispatcher($events)
    {
        \Cache::setEventDispatcher($events);
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key
     * @return bool
     * @static
     */
    public static function has($key)
    {
        return \Cache::has(self::setUniacid() . $key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @static
     */
    public static function get($key, $default = null)
    {
        return \Cache::get(self::setUniacid() . $key, $default);
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param array $keys
     * @return array
     * @static
     */
    public static function many($keys)
    {
        return \Cache::many(self::setUniacid() . $keys);
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @static
     */
    public static function pull($key, $default = null)
    {
        return \Cache::pull(self::setUniacid() . $key, $default);
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param \DateTime|float|int $minutes
     * @return void
     * @static
     */
    public static function put($key, $value, $minutes = null)
    {
        \Cache::put(self::setUniacid() . $key, $value, $minutes);
    }


    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param array $values
     * @param float|int $minutes
     * @return void
     * @static
     */
    public static function putMany($values, $minutes)
    {
        \Cache::putMany($values, $minutes);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param string $key
     * @param mixed $value
     * @param \DateTime|float|int $minutes
     * @return bool
     * @static
     */
    public static function add($key, $value, $minutes)
    {
        return \Cache::add(self::setUniacid() . $key, $value, $minutes);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @return int|bool
     * @static
     */
    public static function increment($key, $value = 1)
    {
        return \Cache::increment(self::setUniacid() . $key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @return int|bool
     * @static
     */
    public static function decrement($key, $value = 1)
    {
        return \Cache::decrement(self::setUniacid() . $key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @static
     */
    public static function forever($key, $value)
    {
        \Cache::forever(self::setUniacid() . $key, $value);
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param string $key
     * @param \DateTime|float|int $minutes
     * @param \Closure $callback
     * @return mixed
     * @static
     */
    public static function remember($key, $minutes, $callback)
    {
        return \Cache::remember(self::setUniacid() . $key, $minutes, $callback);
    }

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     * @static
     */
    public static function sear($key, $callback)
    {
        return \Cache::sear(self::setUniacid() . $key, $callback);
    }

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     * @static
     */
    public static function rememberForever($key, $callback)
    {
        return \Cache::rememberForever(self::setUniacid() . $key, $callback);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     * @static
     */
    public static function forget($key)
    {
        return \Cache::forget(self::setUniacid() . $key);
    }

    /**
     * Begin executing a new tags operation if the store supports it.
     *
     * @param array|mixed $names
     * @return \Illuminate\Cache\TaggedCache
     * @throws \BadMethodCallException
     * @static
     */
    public static function tags($names)
    {
        return \Cache::tags($names);
    }

    /**
     * Get the default cache time.
     *
     * @return float|int
     * @static
     */
    public static function getDefaultCacheTime()
    {
        return \Cache::getDefaultCacheTime();
    }

    /**
     * Set the default cache time in minutes.
     *
     * @param float|int $minutes
     * @return void
     * @static
     */
    public static function setDefaultCacheTime($minutes)
    {
        \Cache::setDefaultCacheTime($minutes);
    }

    /**
     * Get the cache store implementation.
     *
     * @return \Illuminate\Contracts\Cache\Store
     * @static
     */
    public static function getStore()
    {
        return \Cache::getStore();
    }

    /**
     * Determine if a cached value exists.
     *
     * @param string $key
     * @return bool
     * @static
     */
    public static function offsetExists($key)
    {
        return \Cache::offsetExists(self::setUniacid() . $key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     * @return mixed
     * @static
     */
    public static function offsetGet($key)
    {
        return \Cache::offsetGet(self::setUniacid() . $key);
    }

    /**
     * Store an item in the cache for the default time.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @static
     */
    public static function offsetSet($key, $value)
    {
        \Cache::offsetSet(self::setUniacid() . $key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return void
     * @static
     */
    public static function offsetUnset($key)
    {
        \Cache::offsetUnset(self::setUniacid() . $key);
    }

    /**
     * Register a custom macro.
     *
     * @param string $name
     * @param callable $macro
     * @return void
     * @static
     */
    public static function macro($name, $macro)
    {
        \Cache::macro($name, $macro);
    }

    /**
     * Checks if macro is registered.
     *
     * @param string $name
     * @return bool
     * @static
     */
    public static function hasMacro($name)
    {
        return \Cache::hasMacro($name);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \BadMethodCallException
     * @static
     */
    public static function macroCall($method, $parameters)
    {
        return \Cache::macroCall($method, $parameters);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     * @static
     */
    public static function flush()
    {
        \Cache::flush();
    }

    /**
     * Get the Filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     * @static
     */
    public static function getFilesystem()
    {
        return \Cache::getFilesystem();
    }

    /**
     * Get the working directory of the cache.
     *
     * @return string
     * @static
     */
    public static function getDirectory()
    {
        return \Cache::getDirectory();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     * @static
     */
    public static function getPrefix()
    {
        return \Cache::getPrefix();
    }

}