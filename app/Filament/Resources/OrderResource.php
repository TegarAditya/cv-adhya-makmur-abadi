<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Master Order';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->label('Nomor Order')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_name')
                    ->label('Nama Siswa')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_email')
                    ->email()
                    ->label('Email Siswa')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_phone')
                    ->tel()
                    ->label('Nomor Telepon')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('school_name')
                    ->required()
                    ->label('Sekolah')
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Select::make('package_id')
                    ->options(\App\Models\Package::all()->pluck('name', 'id')->toArray())
                    ->disabled()
                    ->required(),
                Forms\Components\Select::make('payment_method_id')
                    ->disabled()
                    ->options(\App\Models\PaymentMethod::all()->pluck('name', 'id')->toArray())
                    ->required(),
                Section::make('Konfirmasi Pembayaran:')
                    ->schema([
                        Forms\Components\FileUpload::make('payment_receipt')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('is_valid')
                            ->label('Pesanan Valid?')
                            ->boolean()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Order')
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#" . $state),
                Tables\Columns\TextColumn::make('student_name')
                    ->label('Nama Siswa')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('student_email')
                    ->label('Email')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('student_phone')
                    ->label('Nomor Telepon')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('school_name')
                    ->label('Sekolah')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Metode Pembayaran')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_valid')
                    ->label('Verifikasi')
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
                Tables\Filters\SelectFilter::make('is_valid')
                    ->label('Verifikasi')
                    ->options([
                        '1' => 'Valid',
                        '0' => 'Tidak Valid',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderResource\Widgets\OrderOverview::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
