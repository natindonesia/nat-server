<?php

namespace App\Livewire;

use App\Models\AppSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @property Form form
 */
class SettingsParameter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    /**
     * Checks if the provided array has string keys.
     *
     * This function counts the number of keys in the array that are strings.
     * If the count is greater than 0, it returns true, indicating that the array has string keys.
     * Otherwise, it returns false.
     *
     * @param array $array The array to check for string keys.
     * @return bool True if the array has string keys, false otherwise.
     */
    public static function has_string_keys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    public static function kvToArray($kv)
    {
        if (!self::has_string_keys($kv)) {
            return $kv;
        }
        $array = [];
        foreach ($kv as $key => $value) {
            // check if value is Key Value
            if (is_array($value)) {
                $array[] = [
                    'name' => $key,
                    'value' => self::kvToArray($value),
                ];
                continue;
            }
            $array[] = [
                'name' => $key,
                'value' => $value,
            ];
        }

        return $array;
    }

    public static function arrayToKv($input)
    {
        $output = [];

        if (isset($input['name']) && isset($input['value'])) {
            $output[$input['name']] = $input['value'];
        } else {
            foreach ($input as $item) {
                if (!isset($item['name']) || !isset($item['value'])) {
                    $output[] = $item;
                } else if (is_array($item['value'])) {
                    $output[$item['name']] = self::arrayToKv($item['value']);
                } else {
                    $output[$item['name']] = $item['value'];
                }
            }
        }

        return $output;
    }

    public function mount(): void
    {


        $this->form->fill([
            'parameter_profile' => self::kvToArray(AppSettings::getParameterProfile()),
            'pool_profile_parameter' => self::kvToArray(AppSettings::getPoolProfileParameter()),
            'sensors_score_multiplier' => self::kvToArray(AppSettings::getSensorsScoreMultiplier()),
        ]);

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pool Parameter')->schema(([
                    Repeater::make('pool_profile_parameter')
                        ->hiddenLabel()
                        ->reorderableWithDragAndDrop(false)
                        ->addable(false)
                        ->deletable(false)
                        ->schema([
                            TextInput::make('name')
                                ->label('Pool Name')
                                ->required(),
                            Select::make('value')
                                ->label('Parameter Profile')
                                ->options($this->getParameters())
                        ])
                ])),
                Section::make('Pool Sensors Score Multiplier')->schema(([
                    Repeater::make('sensors_score_multiplier')
                        ->hiddenLabel()
                        ->reorderableWithDragAndDrop(false)
                        ->addable(false)
                        ->deletable(false)
                        ->collapsed()
                        ->collapsible()
                        ->schema([
                            TextInput::make('name')
                                ->label('Pool Name')
                                ->required(),
                            Repeater::make('value')
                                ->label("Sensor")
                                ->hiddenLabel()
                                ->columns(2)
                                ->reorderableWithDragAndDrop(false)
                                ->addable(false)
                                ->deletable(false)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Sensor')
                                        ->required(),
                                    TextInput::make('value')
                                        ->label('Multiplier')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100),
                                ])
                        ])
                ])),
                Section::make('Parameter Profile')->schema([

                Repeater::make('parameter_profile')
                    ->hiddenLabel()
                    ->reorderableWithDragAndDrop(false)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Name'),
                        Repeater::make('value')
                            ->label("Parameter")
                            ->columns(4)
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
                            ])
                    ])->reorderable(false)->collapsible()->collapsed()
                ]),

            ])
            ->statePath('data');
    }

    public function getSensors()
    {
        $sensors = [];
        foreach (AppSettings::$sensors as $sensor) {
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
        $stateArrayed = [];
        foreach ($state as $key => $value) {
            $stateArrayed[$key] = self::arrayToKv($value);
        }

        foreach ($stateArrayed as $key => $value) {
            AppSettings::updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
            ]);
        }
        \Filament\Notifications\Notification::make()->title('Parameter profile updated successfully.')->success()->send();
        // tell to reload
        if (request()->hasHeader('Referer'))
            redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.settings-parameter');
    }
}
