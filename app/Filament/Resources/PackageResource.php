<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Paket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Paket')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->required(),
                        Forms\Components\Select::make('education_grade_id')
                            ->label('Jenjang Edukasi')
                            ->relationship('educationGrade', 'name')
                            ->required(),
                        Forms\Components\Select::make('education_level_id')
                            ->label('Kelas Edukasi')
                            ->relationship('educationLevel', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Informasi Harga')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga Paket')
                            ->required()
                            ->columnSpanFull()
                            ->numeric()
                    ]),
                Forms\Components\Section::make('Informasi Status')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Tampil di etalase?')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Semester')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('educationGrade.name')
                    ->label('Jenjang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('educationLevel.name')
                    ->label('Kelas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Paket')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Tampil')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
