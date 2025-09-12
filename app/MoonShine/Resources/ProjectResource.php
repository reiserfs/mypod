<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Container;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use Illuminate\Contracts\Database\Eloquent\Builder;

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
use MoonShine\UI\Fields\Fieldset;

/**
 * @extends ModelResource<Project>
 */
class ProjectResource extends ModelResource
{
    protected string $model = Project::class;

    protected string $title = 'Projects';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            Text::make(__('moonshine::ui.resource.name'), 'name')->sortable(),
            Text::make(__('moonshine::ui.resource.description'), 'description')->sortable(), 
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
                    Tab::make(__('moonshine::ui.resource.project'), [
                        ID::make()->sortable(),


                        Flex::make([
                            Text::make(__('moonshine::ui.resource.name'), 'name')
                                ->required(),
                        ]),

                        Textarea::make(__('moonshine::ui.resource.description'), 'description')
                            ->customAttributes([
                            'rows' => 2,
                            ]),                        

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
                Text::make(__('moonshine::ui.resource.description'), 'description'),
                HasMany::make(__('moonshine::ui.resource.container'), 'containers', formatted: 'name', resource: ContainerResource::class),
        ];
    }

    /**
     * @param Project $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
 
    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        // if (Auth::check() && Auth::user()?->userRole?->name === 'Admin') {
        //     return $builder;
        // }

        return $builder->where('user_id', Auth::id());
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        // if (Auth::check() && Auth::user()?->userRole?->name === 'Admin') {
        //     return $builder;
        // }

        return $builder->where('user_id', Auth::id());
    }    

    protected function beforeUpdating(mixed $item): mixed
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Não autorizado a alterar este projeto.');
        }
        return $item;
    }

    protected function beforeCreating(mixed $item): mixed
    {
        $item->user_id = Auth::id();
        return $item;
    }

}
