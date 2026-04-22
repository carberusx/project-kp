<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HariLiburResource\Pages;
use App\Models\HariLibur;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions; // <-- Tambahan PENTING untuk v5

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    // --- PERUBAHAN 1: Property Navigasi Diubah Menjadi Method ---
    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationLabel(): string
    {
        return 'Hari Libur';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    public static function getModelLabel(): string
    {
        return 'Hari Libur';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    // --- PERUBAHAN 2: Parameter Form Menjadi Schema & Section ---
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Hari Libur')
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
                // PERBAIKAN: Menggunakan namespace Actions v5
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // PERBAIKAN: Menggunakan namespace Actions v5
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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