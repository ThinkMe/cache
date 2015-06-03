## Overview

This package is an extension for Laravel5 SASL Memcached
You can use the  aliyun.com OCS(sasl memcached)

## Installation

To `composer.json` add: `"thinkme/cache": "dev-master"` and then run `composer update thinkme/cache`.
and
### For config/app.php
replace line `'Illuminate\Cache\CacheServiceProvider'` with
`'ThinkMe\Cache\CacheServiceProvider'`

### For config/cache.php
		'memcached' => [
			'driver'  => 'saslMemcached',
			'servers' => [
				[
					'host' => '127.0.0.1', 'port' => 11211, 'weight' => 100
				],
			],
		],
        'memcached_sasl' => 'true',
        'memcached_user' => 'FUCKUSER',
        'memcached_pass' => 'FUCKYOU2015',

## OK Let's Go!!!

## License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
