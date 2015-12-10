<?php

namespace m4grio\BangkokInsurance\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use m4grio\BangkokInsurance\ClientBuilder;


/**
 * Bangkok Insurance Service Provider
 *
 * @package m4grio\BangkokInsurance\Laravel\Providers
 * @author  Mario Álvarez <ahoy@m4grio.me>
 */
class BangkokInsuranceServiceProvider extends ServiceProvider
{
    /**
     * Deferred load
     *
     * @var bool
     */
    protected $defer = true;

    const BUILDER = 'bangkokinsurance-builder';
    const CONFIG  = 'bangkokinsurance';

    /**
     * Prepares a builder using the most generic configs
     *
     * @return ClientBuilder
     */
    public function register()
    {
        $this->app->singleton(self::PROVIDES, function ($app) {
            $config = $app['config'][self::CONFIG];

            $builder =  (new ClientBuilder)
                ->setEndpoint($config['endpoint'])
                ->setUserId($config['userid'])
                // @todo
                //->setLog(\Illuminate\Support\Facades\Log::getMonolog())
                ;

            return $builder;
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            self::BUILDER,
        ];
    }
}
