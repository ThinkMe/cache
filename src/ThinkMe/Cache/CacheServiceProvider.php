<?php namespace ThinkMe\Cache;

use Illuminate\Support\ServiceProvider;
use Memcached;

class CacheServiceProvider extends ServiceProvider {

    /**
     * 指定是否延缓提供者加载。
     *
     * @var bool
     */
    protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        $cacheServiceProvider = new \Illuminate\Cache\CacheServiceProvider($this->app);

        $cacheServiceProvider->register();

        \Cache::extend('saslMemcached', function($app){
            // 利用 Illuminate\Cache\MemcachedConnector 类来创建新的 Memcached 对象
            //$memcached = $this->app['memcached.connector']->connect($this->app['app']['config']['cache.stores.memcached.servers']);
            $memcached = $this->connect($app['app']['config']['cache.stores.memcached.servers']);

            // 如果服务器上的 PHP Memcached 扩展支持 SASL 认证
            //ini_get('memcached.use_sasl')
            if($app['app']['config']['cache.stores.memcached_sasl'] == true){

                // 从配置文件中读取 sasl 认证用户名
                $user = $app['app']['config']['cache.stores.memcached_user'];

                // 从配置文件中读取 sasl 认证密码
                $pass = $app['app']['config']['cache.stores.memcached_pass'];

                // 禁用 Memcached 压缩 （阿里云的文档里这样做了……）
                $memcached->setOption(Memcached::OPT_COMPRESSION, false);

                // 指定 Memcached 使用 binary protocol ( sasl 认证要求 )
                $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

                // 指定用于 sasl 认证的账号密码
                $memcached->setSaslAuthData($user, $pass);
            }

            // 从配置文件中读取缓存前缀
            $prefix = $app['app']['config']['cache.prefix'];

            // 创建 MemcachedStore 对象
            $store = new \Illuminate\Cache\MemcachedStore($memcached, $prefix);

            // 创建 Repository 对象，并返回
            return new \Illuminate\Cache\Repository($store);


        });
	}

    /**
     * 取得提供者所提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return ['cache'];
    }

    /**
     * Create a new Memcached connection.
     *
     * @param  array  $servers
     * @return \Memcached
     *
     * @throws \RuntimeException
     */
    protected function connect(array $servers)
    {
        $memcached = $this->getMemcached();

        // For each server in the array, we'll just extract the configuration and add
        // the server to the Memcached connection. Once we have added all of these
        // servers we'll verify the connection is successful and return it back.
        foreach ($servers as $server)
        {
            $memcached->addServer(
                $server['host'], $server['port'], $server['weight']
            );
        }

        $memcachedStatus = $memcached->getVersion();

        if ( ! is_array($memcachedStatus))
        {
            throw new RuntimeException("No Memcached servers added.");
        }

        /*if (in_array('255.255.255', $memcachedStatus) && count(array_unique($memcachedStatus)) === 1)
        {
            throw new RuntimeException("Could not establish Memcached connection.");
        }*/

        return $memcached;
    }

    /**
     * Get a new Memcached instance.
     *
     * @return \Memcached
     */
    protected function getMemcached()
    {
        return new Memcached;
    }

}
