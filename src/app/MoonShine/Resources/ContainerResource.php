<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Container;
use App\Models\Project;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\HasMany;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use MoonShine\UI\Fields\Number;


/**
 * @extends ModelResource<Container>
 */
class ContainerResource extends ModelResource
{
    protected string $model = Container::class;

    protected string $title = 'Containers';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make(__('moonshine::ui.resource.name'), 'name'),
            Text::make(__('moonshine::ui.resource.name'), 'replicas'),

            BelongsTo::make(__('moonshine::ui.resource.project'), 'projetos', formatted: 'name', resource: ProjectResource::class)
                ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])->where('user_id', Auth::id())), 
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
                    Tab::make(__('moonshine::ui.resource.container'), [
                        ID::make(),

                        Text::make(__('moonshine::ui.resource.name'), 'name')
                            ->required(),
                        Text::make(__('moonshine::ui.resource.image'), 'imagem')
                            ->required(),                                
                        BelongsTo::make(__('moonshine::ui.resource.project'), 'projetos', formatted: 'name', resource: ProjectResource::class)
                            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])->where('user_id', Auth::id())),  
                        Number::make(__('moonshine::ui.resource.replicas'), 'replicas')
                            ->min(1)->max(10)->step(1)->buttons()->default(1),                                                         

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
                Text::make(__('moonshine::ui.resource.name'), 'name'),
                Text::make(__('moonshine::ui.resource.image'), 'imagem'),
                HasMany::make(__('moonshine::ui.resource.volume'), 'volumes', formatted: 'name', resource: VolumeResource::class),
                HasMany::make(__('moonshine::ui.resource.network'), 'network', formatted: 'name', resource: NetworkResource::class),
        ];
    }

    /**
     * @param Container $item
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
        // // Admin vê todos
        // if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
        //     return $builder;
        // }

        return $builder->whereIn(
            'project_id',
            Project::where('user_id', Auth::id())->pluck('id')
        );
    }

    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        // // Admin vê todos
        // if (Auth::check() && Auth::user()?->userRole?->name === 'admin') {
        //     return $builder;
        // }

        return $builder->whereIn(
            'project_id',
            Project::where('user_id', Auth::id())->pluck('id')
        );
    }

    protected function beforeCreating(mixed $item): mixed
    {
        $projectId = request()->input('project_id'); // pega da request

        if (! Project::where('id', $projectId)
            ->where('user_id', Auth::id())
            ->exists()
        ) {
            abort(403, 'Não autorizado a criar container neste projeto.');
        }

        // força garantir o project_id correto no item
        $item->project_id = $projectId;

        return $item;
    }


    protected function beforeUpdating(mixed $item): mixed
    {
        // Garante que não altere containers de projetos que não são dele
        if (! Project::where('id', $item->project_id)
            ->where('user_id', Auth::id())
            ->exists()
        ) {
            abort(403, 'Não autorizado a alterar container deste projeto.');
        }

        return $item;
    } 
}
