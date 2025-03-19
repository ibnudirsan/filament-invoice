<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use SolutionForest\FilamentAccessManagement\Facades\FilamentAuthenticate;
use SolutionForest\FilamentAccessManagement\Resources\UserResource\RelationManagers\RolesRelationManager as RolesRelation;

class RolesRelationManager extends RolesRelation
{
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label(strval(__('filament-access-management::filament-access-management.field.id'))),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(strval(__('filament-access-management::filament-access-management.field.name'))),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label(strval(__('filament-access-management::filament-access-management.field.guard_name'))),

                Tables\Columns\TagsColumn::make('permissions.name')
                    ->label(strval(__('filament-access-management::filament-access-management.field.permissions'))),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->label(strval(__('filament-access-management::filament-access-management.field.created_at'))),
            ])
            ->filters([
                # code...
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        static::afterSave();
                }),
                AttachAction::make()
                ->recordSelect(function (Select $select) {
                    return $select
                        ->label('Role')
                        ->searchable()
                        ->preload()
                        ->options(
                            Role::query()
                                ->where('name', '!=', 'super-admin')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->multiple();
                })
                ->after(function () {
                    static::afterSave();
                }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

    protected static function afterSave(): void
    {
        FilamentAuthenticate::clearPermissionCache();
    }
}