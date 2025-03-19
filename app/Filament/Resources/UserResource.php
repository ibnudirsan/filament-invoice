<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Rawilk\FilamentPasswordInput\Password;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\RolesRelationManager;
use Illuminate\Validation\Rules\Password as PasswordRule;
use SolutionForest\FilamentAccessManagement\Support\Utils;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique('users', 'email', ignoreRecord: true)
                ->maxLength(255),

            DateTimePicker::make('email_verified_at')
                ->label('Email Verified At')
                ->native(false)
                ->default(now())
                ->timezone('Asia/Jakarta')
                ->displayFormat('d-m-Y H:i:s')
                ->readOnly(fn (string $operation): bool => $operation === 'create')
                ->required(),

            Password::make('password')
                ->label('Password')
                ->rules([PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                ])
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->maxLength(255)
                ->hidePasswordManagerIcons()
                ->inlineSuffix()
                ->copyable(color: 'secondary')
                ->copyMessage('Successfully Copied')
                ->copyMessageDuration(3000)
                ->regeneratePassword(color: 'withe')
                ->maxLength(20),

            Password::make('confirm_password')
                ->label('Confirm Password')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->same('password')
                ->maxLength(20)
                ->hidePasswordManagerIcons()
                ->inlineSuffix(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('No')
                    ->state(fn ($rowLoop) => $rowLoop->iteration)
                    ->sortable(false)
                    ->size('sm')
                    ->width('5px')
                    ->extraAttributes(['class' => 'py-1']),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->size('sm')
                    ->width('150px')
                    ->grow(false),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->size('sm')
                    ->width('200px')
                    ->grow(false),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->options([
                        'heroicon-o-check-circle',
                        'heroicon-o-x-circle' => fn ($state): bool => $state === null,
                    ])
                    ->size('sm')
                    ->width('20px')
                    ->colors([
                        'success',
                        'danger' => fn ($state): bool => $state === null,
                    ])
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.verified_at'))),

                Tables\Columns\TagsColumn::make('roles.name')
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.roles')))
                    ->size('sm')
                    ->width('300px'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->label(strval(__('filament-access-management::filament-access-management.field.user.created_at')))
                    ->size('sm')
                    ->width('100px')
                    ->sortable(),
            ])
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->whereDoesntHave('roles', function (Builder $query) {
                    $query->where('name', 'super-admin');
                });
            })
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-s-pencil'),
                    DeleteAction::make()
                        ->label('Delete')
                        ->icon('heroicon-s-trash'),
                ])
                ->label('Action')
                ->icon('heroicon-s-ellipsis-vertical')
                ->button()
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create User'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }
    
    public static function getNavigationIcon(): string
    {
        return config('filament-access-management.filament.navigationIcon.user') ?? parent::getNavigationIcon();
    }

    public static function getModel(): string
    {
        return Utils::getUserModel() ?? parent::getModel();
    }

    public static function getNavigationGroup(): ?string
    {
        return strval(__('filament-access-management::filament-access-management.section.group'));
    }

    public static function getLabel(): string
    {
        return strval(__('filament-access-management::filament-access-management.section.user'));
    }

    public static function getPluralLabel(): string
    {
        return strval(__('filament-access-management::filament-access-management.section.users'));
    }
    
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/edit/{record}'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users';
    }
}
