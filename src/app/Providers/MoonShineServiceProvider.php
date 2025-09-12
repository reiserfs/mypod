<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\UserRolesResource;
use App\MoonShine\Resources\PlanoResource;
use App\MoonShine\Resources\ProjectResource;
use App\MoonShine\Resources\ContainerResource;
use App\MoonShine\Resources\VolumeResource;
use App\MoonShine\Resources\NetworkResource;
use App\MoonShine\Resources\StatusResource;
use App\MoonShine\Resources\ShellResource;
use App\MoonShine\Resources\LogsResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
        $core
            ->resources([
                UserResource::class,
                UserRolesResource::class,
                PlanoResource::class,
                ProjectResource::class,
                ContainerResource::class,
                VolumeResource::class,
                NetworkResource::class,
                StatusResource::class,
                ShellResource::class,
                LogsResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
