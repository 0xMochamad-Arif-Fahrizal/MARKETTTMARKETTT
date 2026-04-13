<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_variant_id')
                    ->required()
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('variant.product.images.image_url')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => $record->variant->product->images()->orderBy('sort_order')->first()?->image_url)
                    ->square()
                    ->size(60),
                Tables\Columns\TextColumn::make('variant.product.name')
                    ->label('Product')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('variant.size')
                    ->label('Size')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('variant.color')
                    ->label('Color')
                    ->badge()
                    ->color('gray')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->getStateUsing(fn ($record) => $record->unit_price * $record->quantity)
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Disable creating items from admin
            ])
            ->actions([
                // Disable editing/deleting items from admin
            ])
            ->bulkActions([
                // Disable bulk actions
            ])
            ->paginated(false);
    }
}
