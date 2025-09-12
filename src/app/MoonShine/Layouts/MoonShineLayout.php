<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When};
use App\MoonShine\Resources\UserResource;
use MoonShine\MenuManager\MenuItem;
use MoonShine\MenuManager\MenuGroup;
use App\MoonShine\Resources\UserRolesResource;
use App\MoonShine\Resources\PlanoResource;
use App\MoonShine\Resources\ProjectResource;
use App\MoonShine\Resources\ContainerResource;
use App\MoonShine\Resources\VolumeResource;
use App\MoonShine\Resources\NetworkResource;
use App\MoonShine\Resources\StatusResource;
use App\MoonShine\Resources\ShellResource;
use App\MoonShine\Resources\LogsResource;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            //...parent::menu(),
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make('Users', UserResource::class)->icon('users'),
                MenuItem::make('Roles', UserRolesResource::class)->icon('bookmark'),
                MenuItem::make('Planos', PlanoResource::class)->icon('currency-dollar'),
            ])->icon('cog'),                

            MenuGroup::make(static fn () => 'Projetos', [
                MenuItem::make('Projects', ProjectResource::class)->icon('briefcase'),
                MenuItem::make('Containers', ContainerResource::class)->icon('cube'),
                MenuItem::make('Volumes', VolumeResource::class)->icon('circle-stack'),
                MenuItem::make('Network', NetworkResource::class)->icon('wifi'),
            ])->icon('folder-open'),   
            
            MenuGroup::make(static fn () => 'Painel de Controle', [
                MenuItem::make('Status', StatusResource::class)->icon('play-pause'),
                MenuItem::make('Shell', ShellResource::class)->icon('command-line'),
                MenuItem::make('Logs', LogsResource::class)->icon('document-arrow-down'),
            ])->icon('wrench'),                                      
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
