<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'company' => 'primary',
                        'seo' => 'success',
                        'tokens' => 'danger',
                        'custom' => 'info',
                        'reports' => 'warning',
                        'mail' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable()
                    ->copyable()
                    ->weight('medium')
                    ->sortable(),
                TextColumn::make('value')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->is_encrypted ? '•••• encrypted' : $record->value)
                    ->formatStateUsing(fn ($state, $record) => $record->is_encrypted ? '••••••••' : $state)
                    ->searchable(),
                IconColumn::make('is_encrypted')
                    ->label('Encrypted')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(30)
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'company' => 'company',
                        'seo' => 'seo',
                        'tokens' => 'tokens',
                        'custom' => 'custom',
                        'reports' => 'reports',
                        'mail' => 'mail',
                        'general' => 'general',
                    ]),
                \Filament\Tables\Filters\TernaryFilter::make('is_encrypted'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group')
            ->searchable();
    }
}
