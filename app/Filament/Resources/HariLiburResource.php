<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HariLiburResource\Pages;
use App\Models\HariLibur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Hari Libur';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $modelLabel = 'Hari Libur';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Hari Libur')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal Libur')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Misal: Hari Raya Idul Fitri, Cuti Bersama, dll.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
                //
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
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHariLiburs::route('/'),
            'create' => Pages\CreateHariLibur::route('/create'),
            'edit' => Pages\EditHariLibur::route('/{record}/edit'),
        ];
    }
}
