<?php declare(strict_types=1);

namespace Snowflake;

use Godruoyi\Snowflake\Snowflake;
use Illuminate\Database\Schema\Blueprint;
use Godruoyi\Snowflake\RandomSequenceResolver;
use Illuminate\Support\ServiceProvider as Provider;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap any package services.
     *
     */
    public function boot() : void
    {
        $this->macros();

        $this->publishes([__DIR__ . '/../config/snowflake.php' => config_path('snowflake.php')]);
    }

    /**
     * Register any package services.
     *
     */
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/snowflake.php', 'snowflake');

        $this->app->singleton('snowflake', fn() => $this->singleton());
    }

    /**
     * Register any custom macros.
     *
     */
    protected function macros() : void
    {
        Blueprint::macro('snowflake', function($column = 'id') {
            return $this->unsignedBigInteger($column);
        });

        Blueprint::macro('foreignSnowflake', function($column) {
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type'          => 'bigInteger',
                'name'          => $column,
                'autoIncrement' => false,
                'unsigned'      => true,
            ]));
        });

        Blueprint::macro('foreignSnowflakeFor', function($model, $column = null) {
            return $this->foreignSnowflake($column ?: (new $model())->getForeignKey());
        });
    }

    /**
     * Register the Snowflake singleton service.
     *
     */
    protected function singleton() : Snowflake
    {
        $distributed = config('snowflake.distributed', true);

        $service = new Snowflake(
            $distributed ? config('snowflake.data_center', 1) : null,
            $distributed ? config('snowflake.worker_node', 1) : null
        );

        $timestamp = strtotime(config('snowflake.start_timestamp', '2022-01-01')) * 1000;
        $resolver  = config('snowflake.sequence_resolver', RandomSequenceResolver::class);

        return $service
            ->setStartTimeStamp($timestamp)
            ->setSequenceResolver(new $resolver());
    }
}
