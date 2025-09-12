<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Volume;
use App\Models\Container;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use Illuminate\Contracts\Database\Eloquent\Builder;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Fieldset;

/**
 * @extends ModelResource<Volume>
 */
class VolumeResource extends ModelResource
{
    protected string $model = Volume::class;

    protected string $title = 'Volumes';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make(__('moonshine::ui.resource.name'), 'name')->sortable(),
            Text::make(__('moonshine::ui.resource.size'), 'size', static fn($item) => 
                $item->size !== null
                    ? number_format((int) $item->size, 0, ',', '.') . ' MB'
                    : '—'
            )->sortable(),

            BelongsTo::make(__('moonshine::ui.resource.container'), 'container', formatted: 'name', resource: ContainerResource::class)
                ->valuesQuery(static function (Builder $q) {
                    return $q->select(['containers.id', 'containers.name'])
                        ->whereHas('projetos', function ($sub) {
                            $sub->where('user_id', Auth::id());
                        });
                })->sortable(),
        ];        
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.volume'), [
                        ID::make(),

                        Text::make(__('moonshine::ui.resource.name'), 'name')
                            ->required(),
                        Select::make(__('moonshine::ui.resource.tipo'), 'type')
                            ->options([
                                'persistent' => 'Persistente',
                                'ephemeral'  => 'Efêmero',
                            ])
                            ->required(),
                        Select::make(__('moonshine::ui.resource.storage_class'), 'storage_class')
                            ->options([
                                'standard'   => 'Standard',
                                'fast'       => 'Fast',
                                'slow'       => 'Slow',
                            ])
                            ->required(),                                                           
                        BelongsTo::make(__('moonshine::ui.resource.container'), 'container', formatted: 'name', resource: ContainerResource::class)
                            ->valuesQuery(static function (Builder $q) {
                                return $q->select(['containers.id', 'containers.name'])
                                    ->whereHas('projetos', function ($sub) {
                                        $sub->where('user_id', Auth::id());
                                    });
                            }),
                        
                        Number::make(__('moonshine::ui.resource.size'), 'size')
                            ->min(1)               // tamanho mínimo
                            ->max(102400)          // tamanho máximo (ex: 100 GB)
                            ->step(1)              // incremento
                            ->customAttributes([
                                'placeholder' => 'Informe o tamanho em MB',
                            ])
                            ->suffix('MB')
                            ->default('100'),
                                                         
                    ])->icon('briefcase'),
                ]),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [ 
            Fieldset::make(__('moonshine::ui.resource.main_information'))->fields([           

                Text::make(__('moonshine::ui.resource.name'), 'name'),

                Text::make(__('moonshine::ui.resource.tipo'), 'type'),

                Text::make(__('moonshine::ui.resource.storage_class'), 'tystorage_classpe')

            ]),
            Fieldset::make(__('moonshine::ui.resource.container'))->fields([           
                BelongsTo::make(__('moonshine::ui.resource.container'), 'container', formatted: 'name', resource: ContainerResource::class)
                    ->valuesQuery(static function (Builder $q) {
                        return $q->select(['containers.id', 'containers.name'])
                            ->whereHas('projetos', function ($sub) {
                                $sub->where('user_id', Auth::id());
                            });
                    }),
            ]),
          
        ];
    }

    /**
     * @param Volume $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        // Admin vê todos
        if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
            return $builder;
        }

        // Usuário normal só vê volumes de containers de projetos dele
        return $builder->whereHas('container.projetos', function ($q) {
            $q->where('user_id', Auth::id());
        });
    }

    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        // Admin vê todos
        if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
            return $builder;
        }

        // Só volumes de containers de projetos do usuário
        return $builder->whereHas('container.projetos', function ($q) {
            $q->where('user_id', Auth::id());
        });
    }

    protected function beforeCreating(mixed $item): mixed
    {
        $containerId = request()->input('container_id');

        // Verifica se o container pertence a um projeto do usuário
        if (! Container::where('id', $containerId)
            ->whereHas('projetos', fn($q) => $q->where('user_id', Auth::id()))
            ->exists()
        ) {
            abort(403, 'Não autorizado a criar volume neste container.');
        }

        // Força o container correto no item
        $item->container_id = $containerId;

        return $item;
    }

    protected function beforeUpdating(mixed $item): mixed
    {
        // Verifica se o volume pertence a um container de projeto do usuário
        if (! Container::where('id', $item->container_id)
            ->whereHas('projetos', fn($q) => $q->where('user_id', Auth::id()))
            ->exists()
        ) {
            abort(403, 'Não autorizado a alterar este volume.');
        }

        return $item;
    }    
}
