<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermissionsRelationManager;
use SolutionForest\FilamentAccessManagement\Support\Utils;
use SolutionForest\FilamentAccessManagement\Resources\RoleResource\RelationManagers;

class RoleResource extends Resource
{
    protected static ?int $navigationSort = 2;
    protected static ?string $model = Role::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(strval(__('filament-access-management::filament-access-management.field.name')))
                                    ->required(),
                                Forms\Components\TextInput::make('guard_name')
                                    ->label(strval(__('filament-access-management::filament-access-management.field.guard_name')))
                                    ->required()
                                    ->default(Utils::getFilamentAuthGuard()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('No')
                    ->state(fn ($rowLoop) => $rowLoop->iteration)
                    ->sortable(false)
                    ->size('sm')
                    ->width('5px')
                    ->extraAttributes(['class' => 'py-1']),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-access-management::filament-access-management.field.name')),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label(__('filament-access-management::filament-access-management.field.guard_name')),

                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime('Y-m-d H:i:s')
                    ->label(strval(__('filament-access-management::filament-access-management.field.created_at'))),
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(function (Builder $query): Builder {
                if (auth()->user()->hasRole('super-admin')) {
                    return $query;
                }
                return $query->where('name', '!=', 'super-admin');
            })
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-s-pencil'),
                    DeleteAction::make()
                        ->visible(fn (Role $record) => $record->name !== 'super-admin'),
                ])
                ->label('Action')
                ->icon('heroicon-s-ellipsis-vertical')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/edit/{record}'),
        ];
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-access-management.filament.navigationIcon.role') ?? parent::getNavigationIcon();
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel() ?? parent::getModel();
    }

    public static function getNavigationGroup(): ?string
    {
        return strval(__('filament-access-management::filament-access-management.section.group'));
    }

    public static function getLabel(): string
    {
        return strval(__('filament-access-management::filament-access-management.section.role'));
    }

    public static function getPluralLabel(): string
    {
        return strval(__('filament-access-management::filament-access-management.section.roles'));
    }
}
