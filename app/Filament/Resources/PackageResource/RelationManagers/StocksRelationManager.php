<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use App\Enums\PackageActionEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Hidehalo\Nanoid\Client as Nanoid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';

    protected static ?string $title = 'Stok';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('action')
                    ->label('Jenis Perubahan')
                    ->options(PackageActionEnum::class)
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                Forms\Components\Hidden::make('public_id')
                    ->default($this->getPublicId()),
            ]); 
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['public_id'] = $this->getPublicId();

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('public_id')
            ->columns([
                Tables\Columns\TextColumn::make('public_id')
                    ->label('Public ID')
                    ->fontFamily(FontFamily::Mono)
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('action')
                    ->label('Jenis Perubahan')
                    ->badge(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Input')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private function getPublicId(): string
    {
        $nanoid = new Nanoid();

        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        return $nanoid->formattedId($alphabet, 15);
    }
}
