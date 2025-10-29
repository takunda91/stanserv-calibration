<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('roles')
                            ->relationship('roles', 'name') // Assumes a 'roles' relationship on your User model
                            ->required()
                            ->multiple()
                            ->preload(),

                        Fieldset::make('Security')
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                                    ->dehydrated(fn(?string $state): bool => filled($state))
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->maxLength(255),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->same('password')
                                    ->dehydrated(false)
                                    ->required(fn(string $operation): bool => $operation === 'create'),
                            ])->columns(2)
                            ->columnSpan(2)
                            ->visibleOn('create'),
                    ])->columnSpanFull()->columns(2)
            ]);
    }
}
