<?php

namespace m4grio\BangkokInsurance\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use m4grio\BangkokInsurance\ClientBuilder;
use m4grio\BangkokInsurance\Process\ModelProcess;
use m4grio\BangkokInsurance\Process\PremiumProcess;
use m4grio\BangkokInsurance\Process\TransferProcess;


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
    const PREMIUM = 'bangkokinsurance-premium';
    const MODEL   = 'bangkokinsurance-model';
    const TRANSFER = 'bangkokinsurance-transfer';
    const CONFIG  = 'bangkokinsurance';

    /**
     * Prepares a builder using the most generic configs
     *
     * @return ClientBuilder
     */
    public function register()
    {
        $this->registerBuilder();
        $this->registerPremiumBuilder();
        $this->registerModelBuilder();
        $this->registerTransferBuilder();
    }

    /**
     * Register general builder
     *
     * @return void
     */
    protected function registerBuilder()
    {
        $this->app->bind(self::BUILDER, function ($app) {
            $config = $app['config'][self::CONFIG];

            $builder = (new ClientBuilder)->setEndpoint($config['endpoint'])
                ->setUserId($config['userid'])
                // @todo
                //->setLog(\Illuminate\Support\Facades\Log::getMonolog())
            ;

            return $builder;
        });
    }

    /**
     * Register Premium Client builder
     */
    protected function registerPremiumBuilder()
    {
        $this->app->singleton(self::PREMIUM, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(self::BUILDER);
            $client = $builder->setProcess(new PremiumProcess)
                ->build()
            ;

            return $client;
        });
    }

    /**
     * Register Model Client builder
     */
    protected function registerModelBuilder()
    {
        $this->app->singleton(self::MODEL, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(self::BUILDER);
            $client = $builder->setProcess(new ModelProcess)
                ->build()
            ;

            return $client;
        });
    }

    /**
     * Register Transfer Client builder
     */
    protected function registerTransferBuilder()
    {
        $this->app->singleton(self::TRANSFER, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(self::BUILDER);
            $client = $builder->setProcess(new TransferProcess)
                ->build()
            ;

            return $client;
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            self::BUILDER,
            self::PREMIUM,
            self::MODEL,
            self::TRANSFER,
        ];
    }
}
