<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Plano;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;

use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Text;


/**
 * @extends ModelResource<Plano>
 */
class PlanoResource extends ModelResource
{
    protected string $model = Plano::class;

    protected string $title = 'Planos';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')->sortable(),

            Text::make('Valor', 'valor', fn ($item) =>
                '€ ' . number_format((float) $item->valor, 2, ',', '.')
            ),

            Number::make('Containers Máx.', 'max_containers')->sortable(),
            Number::make('Memória Máx. (MB)', 'max_memoria')->sortable(),
            Number::make('Disco Máx. (GB)', 'max_disco')->sortable(),
            Number::make('CPU Máx. (%)', 'max_cpu')->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
           Box::make([            
                Text::make('Nome', 'name')->required(),

                // Input numérico para o valor com casas decimais
                Number::make('Valor', 'valor')
                    ->step(0.01)
                    ->buttons()
                    ->required(),
                
                Textarea::make('Descrição', 'descricao')
                    ->customAttributes([
                    'rows' => 2,
                    ]),

                Number::make('Containers Máx', 'max_containers')
                    ->min(1)->max(100)->step(1)->buttons(),

                Number::make('Memória Máx (MB)', 'max_memoria')
                    ->min(256)->max(65536)->step(256)->buttons(),

                Number::make('Disco Máx (GB)', 'max_disco')
                    ->min(10)->max(2048)->step(10)->buttons(),

                Number::make('CPU Máx (%)', 'max_cpu')
                    ->min(1)->max(100)->step(1)->buttons(),
           
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),

            Text::make('Nome', 'name'),

            Text::make('Valor', 'valor', fn ($item) =>
                '€ ' . number_format((float) $item->valor, 2, ',', '.')
            ),

            Number::make('Containers Máx.', 'max_containers'),
            Number::make('Memória Máx. (MB)', 'max_memoria'),
            Number::make('Disco Máx. (GB)', 'max_disco'),
            Number::make('CPU Máx. (%)', 'max_cpu'),
            
        ];
    }

    /**
     * @param Plano $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
