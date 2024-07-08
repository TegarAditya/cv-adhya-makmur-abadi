<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Filament\Resources\PackageResource\RelationManagers\StocksRelationManager;
use App\Models\EducationGrade;
use App\Models\EducationLevel;
use App\Models\Package;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Termwind\Enums\Color;

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
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('education_grade_id', null);
                                $set('education_level_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('education_grade_id')
                            ->label('Jenjang Edukasi')
                            ->relationship('educationGrade', 'name')
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('education_level_id', null);
                            })
                            ->disabled(fn (Get $get) => $get('semester_id') === null)
                            ->required(),
                        Forms\Components\Select::make('education_level_id')
                            ->label('Kelas Edukasi')
                            ->relationship('educationLevel', 'name')
                            ->live()
                            ->disabled(fn (Get $get) => $get('education_grade_id') === null)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $semester = Semester::find($get('semester_id'))->name ?? '-';
                                $level = EducationLevel::find($get('education_level_id'))->name ?? '-';
                                $grade = EducationGrade::find($get('education_grade_id'))->name ?? '-';
                                $set('name', 'BUKU BUPIN ' . $grade . ' ' . $level . ' ' . $semester);
                            })
                            ->required(),
                        Forms\Components\Hidden::make('name'),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('ph')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->content(function (Get $get, Set $set) {
                                        $semester = Semester::find($get('semester_id'))->name ?? '-';
                                        $level = EducationLevel::find($get('education_level_id'))->name ?? '-';
                                        $grade = EducationGrade::find($get('education_grade_id'))->name ?? '-';

                                        $name = 'BUKU BUPIN ' . $grade . ' ' . $level . ' ' . $semester;

                                        $set('name', $name);

                                        return new HtmlString('<span class="font-bold text-lg mx-auto">' . $name . '</span>');
                                    }),
                            ]),
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
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_stock')
                    ->label('Stok')
                    ->default(fn (Package $record) => $record->getRemainingStock())
                    ->sortable(),
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
                    Tables\Actions\BulkAction::make('editPrice')
                        ->label('Ubah Harga')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->form(function (Collection $records) {
                            return [
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                            ];
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StocksRelationManager::class,
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
