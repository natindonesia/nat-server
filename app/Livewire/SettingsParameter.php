<?php

namespace App\Livewire;

use App\Http\Controllers\StatusController;
use App\Models\AppSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class SettingsParameter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $parameter_profile = [];
        $pool_profile_parameter = [];
        foreach (AppSettings::getParameterProfile() as $key => $value) {
            $parameter_profile[] = [
                'name' => $key,
                'value' => $value,
            ];
        }

        foreach (AppSettings::getPoolProfileParameter() as $key => $value) {
            $pool_profile_parameter[] = [
                'nama' => $key,
                'value' => $value,
            ];
        }

        $this->form->fill([
            'parameter_profile' => $parameter_profile,
            'pool_profile_parameter' => $pool_profile_parameter,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Parameter Profile')->schema([

                Repeater::make('parameter_profile')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Name'),
                        Repeater::make('value')
                            ->schema([
                                Select::make('sensor')->options($this->getSensors()),
                                TextInput::make('min')
                                    ->required()
                                    ->numeric()
                                    ->lte('max'), // less than or equal
                                TextInput::make('max')
                                    ->required()
                                    ->numeric()
                                    ->gte('min'), // greater than or equal
                                TextInput::make('score')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1),
                            ])->columns(4)
                    ])->reorderable(false)->collapsible()->collapsed()
                ]),
                Section::make('Pool Parameter')->schema(([
                    Repeater::make('pool_profile_parameter')
                        ->addable(false)
                        ->deletable(false)
                        ->schema([
                            TextInput::make('nama')
                                ->disabled()
                                ->required(),
                            Select::make('value')
                                ->options($this->getParameters())
                        ])
                ]))
            ])
            ->statePath('data');
    }

    public function getSensors()
    {
        $sensors = [];
        foreach (StatusController::$sensors as $sensor) {
            $sensors[$sensor] = __('translation.' . $sensor);
        }
        return $sensors;
    }

    public function getParameters()
    {
        $parameters = [];
        foreach (AppSettings::getParameterProfile() as $parameter => $value) {
            $parameters[$parameter] = $parameter;
        }
        return $parameters;
    }

    public function create(): void
    {
        $state = $this->form->getState();
        $parameter_profile = [];
        foreach ($state['parameter_profile'] as $key => $value) {
            $parameter_profile[$value['name']] = $value['value'];
        }
        AppSettings::updateOrInsert([
            'key' => 'parameter_profile',
        ], [
            'value' => $parameter_profile,
        ]);
        $pool_profile_parameter = [];

        $keys_for_pool_profile_parameter = array_keys(AppSettings::getPoolProfileParameter()); // what you mean i can't use disabled field as key
        foreach ($state['pool_profile_parameter'] as $key => $value) {
            $pool_profile_parameter[$keys_for_pool_profile_parameter[$key]] = $value['value'];
        }

        AppSettings::updateOrInsert([
            'key' => 'pool_profile_parameter',
        ], [
            'value' => $pool_profile_parameter,
        ]);
        \Filament\Notifications\Notification::make()->title('Parameter profile updated successfully.')->success()->send();
    }

    public function render()
    {
        return view('livewire.settings-parameter');
    }
}
