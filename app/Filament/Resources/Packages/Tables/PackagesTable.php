<?php
/**
 * File: app/Filament/Resources/Packages/Tables/PackagesTable.php
 * Purpose: Defines the Package listing table (admin/packages). Cleaned up as requested — grouped columns, sidebar filters.
 *          Shows key columns: title, category, destination, duration, price, featured, status, with toggles for others.
 *          Previously showed 30+ columns unsorted; now grouped and filtered.
 */

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PackagesTable
{
    /**
     * Configure the table for Package listing.
     * Groups columns logically and adds filters for easy management.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // === Group: Identification ===
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl('https://via.placeholder.com/40')
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('Package Title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->slug),

                // === Group: Categorization ===
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('destination.name')
                    ->label('Destination')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('region.name')
                    ->label('Region')
                    ->toggleable(isToggledHiddenByDefault: true),

                // === Group: Trip Details ===
                TextColumn::make('duration_days')
                    ->label('Days')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('max_altitude_m')
                    ->label('Max Alt')
                    ->numeric()
                    ->suffix('m')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('difficulty')
                    ->label('Difficulty')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'easy' => 'success',
                        'moderate' => 'info',
                        'hard' => 'warning',
                        'strenuous', 'challenging' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                // === Group: Pricing (Single/Group handled via pricings tab, but show base price here) ===
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->description(fn ($record) => $record->discount_price ? 'Sale: $' . $record->discount_price : null),

                TextColumn::make('pricings_count')
                    ->label('Tiers')
                    ->counts('pricings')
                    ->badge()
                    ->color('warning')
                    ->tooltip('Number of Single/Group pricing tiers (in Pricing tab)')
                    ->toggleable(),

                // === Group: Tags (SEO) ===
                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->toggleable(),

                // === Group: Status & Visibility ===
                IconColumn::make('featured')
                    ->label('Feat')
                    ->boolean()
                    ->tooltip('Featured on homepage'),

                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Grouped filters in sidebar as requested
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->preload(),

                SelectFilter::make('difficulty')
                    ->label('Difficulty')
                    ->options([
                        'easy' => 'Easy',
                        'moderate' => 'Moderate',
                        'hard' => 'Hard',
                        'strenuous' => 'Strenuous',
                        'challenging' => 'Challenging',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),

                SelectFilter::make('featured')
                    ->label('Featured')
                    ->options([
                        '1' => 'Featured',
                        '0' => 'Not Featured',
                    ])
                    ->query(fn ($query, $data) => $query->when($data['value'] !== null, fn ($q) => $q->where('featured', $data['value']))),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order'); // Drag to reorder in table
    }
}
