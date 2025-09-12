<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Network;
use App\Models\Container;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use Illuminate\Contracts\Database\Eloquent\Builder;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Fieldset;
use Illuminate\Support\Facades\Auth;

/**
 * @extends ModelResource<Network>
 */
class NetworkResource extends ModelResource
{
    protected string $model = Network::class;

    protected string $title = 'Ingress Rules';

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make('Nome', 'name')->sortable(),
            Text::make('Host', 'host')->sortable(),
            Number::make('Porta', 'port')->sortable(),
            Text::make('Path', 'path')->sortable(),
            Text::make('Path Type', 'pathType')->sortable(),
            BelongsTo::make('Container', 'container', formatted: 'name', resource: ContainerResource::class)
                ->valuesQuery(static function (Builder $q) {
                    return $q->select(['id', 'name'])
                        ->whereHas('projetos', function ($sub) {
                            $sub->where('user_id', Auth::id());
                        });
                })
                ->sortable(),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Fieldset::make('Informações do Ingress')->fields([
                Text::make('Nome', 'name')->required(),
                Text::make('Host', 'host')->required(),
                Number::make('Porta', 'port')->min(1)->max(65535)->required(),
                Text::make('Path', 'path')->nullable(),
                Select::make('Path Type', 'pathType')
                    ->options([
                        'Prefix' => 'Prefix',
                        'Exact' => 'Exact',
                    ])
                    ->required(),
                BelongsTo::make('Container', 'container', formatted: 'name', resource: ContainerResource::class)
                    ->valuesQuery(static function (Builder $q) {
                        return $q->select(['id', 'name'])
                            ->whereHas('projetos', function ($sub) {
                                $sub->where('user_id', Auth::id());
                            });
                    })
                    ->required(),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            Fieldset::make('Ingress Rule')->fields([
                Text::make('Nome', 'name'),
                Text::make('Host', 'host'),
                Number::make('Porta', 'port'),
                Text::make('Path', 'path'),
                Text::make('Path Type', 'pathType'),
                BelongsTo::make('Container', 'container', formatted: 'name', resource: ContainerResource::class),
            ]),
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
            return $builder;
        }

        return $builder->whereHas('container.projetos', function ($q) {
            $q->where('user_id', Auth::id());
        });
    }

    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
            return $builder;
        }

        return $builder->whereHas('container.projetos', function ($q) {
            $q->where('user_id', Auth::id());
        });
    }

    protected function beforeCreating(mixed $item): mixed
    {
        $containerId = request()->input('container_id');

        if (! Container::where('id', $containerId)
            ->whereHas('projetos', fn($q) => $q->where('user_id', Auth::id()))
            ->exists()
        ) {
            abort(403, 'Não autorizado a criar ingress rule neste container.');
        }

        $item->container_id = $containerId;

        return $item;
    }

    protected function beforeUpdating(mixed $item): mixed
    {
        if (! Container::where('id', $item->container_id)
            ->whereHas('projetos', fn($q) => $q->where('user_id', Auth::id()))
            ->exists()
        ) {
            abort(403, 'Não autorizado a alterar esta ingress rule.');
        }

        return $item;
    }
}
