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
    const CONFIG_NAMESPACE  = 'bangkokinsurance';
    const CONFIG_USERID     = 'userid';
    const CONFIG_AGENT_CODE = 'agent_code';
    const CONFIG_AGENT_SEQ  = 'agent_seq';

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
        $this->app->bind(static::BUILDER, function ($app) {
            $config = $app['config'][static::CONFIG];

            $builder = (new ClientBuilder)->setEndpoint($config['endpoint'])
                ->setUserId($config[static::CONFIG_USERID])
                ->setAgentSeq($config[static::CONFIG_AGENT_SEQ])
                ->setAgentCode($config[static::CONFIG_AGENT_CODE])
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
        $this->app->singleton(static::PREMIUM, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(static::BUILDER);
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
        $this->app->singleton(static::MODEL, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(static::BUILDER);
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
        $this->app->singleton(static::TRANSFER, function ($app) {
            /** @var ClientBuilder $builder */
            $builder = $app->make(static::BUILDER);
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
            static::BUILDER,
            static::PREMIUM,
            static::MODEL,
            static::TRANSFER,
        ];
    }
}
