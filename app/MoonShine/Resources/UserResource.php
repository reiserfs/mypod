<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\UserRoles;
use App\Models\Project;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

use MoonShine\Support\ListOf;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Color;

use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;

use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;


#[Icon('users')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(1)]

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Users';
    
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make(__('moonshine::ui.resource.name'), 'name'),

            Image::make(__('moonshine::ui.resource.avatar'), 'avatar')->modifyRawValue(fn (
                ?string $raw
            ): string => $raw ?? ''),

            Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                ->format("d.m.Y")
                ->sortable(),

            Email::make(__('moonshine::ui.resource.email'), 'email')
                ->sortable(),
            
            BelongsTo::make(__('moonshine::ui.resource.role'), 'userRoles', formatted: 'name', resource: UserRolesResource::class),
            BelongsTo::make(__('moonshine::ui.resource.plan'), 'userPlanos', formatted: static fn($plano) => $plano ? "{$plano->name} - {$plano->valor}" : '', resource: PlanoResource::class),
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
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        BelongsTo::make(__('moonshine::ui.resource.role'), 'userRoles', formatted: 'name', resource: UserRolesResource::class)
                            ->creatable()
                            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])),

                        BelongsTo::make(__('moonshine::ui.resource.plan'), 'userPlanos', formatted: static fn($plano) => $plano ? "{$plano->name} - {$plano->valor}" : '', resource: PlanoResource::class)
                            ->creatable()
                            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name', 'valor'])),

                        Flex::make([
                            Text::make(__('moonshine::ui.resource.name'), 'name')
                                ->required(),

                            Text::make(__('moonshine::ui.resource.surname'), 'surname')
                                ->required(),

                            Email::make(__('moonshine::ui.resource.email'), 'email')
                                ->required(),
                        ]),

                        Image::make(__('moonshine::ui.resource.avatar'), 'avatar')
                            ->disk(moonshineConfig()->getDisk())
                            ->dir('moonshine_users')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                        Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                            ->format("d.m.Y")
                            ->default(now()->toDateTimeString()),
                    ])->icon('user-circle'),

                    Tab::make(__('moonshine::ui.resource.password'), [
                        Collapse::make(__('moonshine::ui.resource.change_password'), [
                            Password::make(__('moonshine::ui.resource.password'), 'password')
                                ->customAttributes(['autocomplete' => 'new-password'])
                                ->eye(),

                            PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                                ->customAttributes(['autocomplete' => 'confirm-password'])
                                ->eye(),
                        ])->icon('lock-closed'),
                    ])->icon('lock-closed'),
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
                ID::make(),
                Image::make(__('moonshine::ui.resource.avatar'), 'avatar')->modifyRawValue(fn (
                    ?string $raw
                ): string => $raw ?? ''),
                
                Text::make(__('moonshine::ui.resource.name'), 'name'),

                Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                    ->format("d.m.Y")
                    ->sortable(),

                Email::make(__('moonshine::ui.resource.email'), 'email')
                    ->sortable(),
            ]),
            Fieldset::make(__('moonshine::ui.resource.role_plan'))->fields([           
                BelongsTo::make('Role', 'userRoles', formatted: 'name', resource: UserRolesResource::class),
                BelongsTo::make('Plano', 'userPlanos', formatted: static fn($plano) => $plano ? "{$plano->name} - {$plano->valor}" : '', resource: PlanoResource::class),            
            ]),

            Fieldset::make(__('moonshine::ui.resource.project'))->fields([
                HasMany::make('Projetos', 'projetos', formatted: 'name', resource: ProjectResource::class),
            ]),          
        ];
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
