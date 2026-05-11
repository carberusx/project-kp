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
use Filament\Actions;
use Illuminate\Support\Facades\Auth;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    // ── Hanya Super Admin ─────────────────────────────────────────────────
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    // --- PERUBAHAN 1: Property Navigasi Diubah Menjadi Method ---
    public static function getNavigationIcon(): string|null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kalender Kegiatan';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan Web';
    }

    public static function getModelLabel(): string
    {
        return 'Kalender';
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
                Section::make('Data Kalender')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('tipe')
                            ->label('Jenis')
                            ->options([
                                'libur' => 'Hari Libur / Cuti Bersama',
                                'kerja_khusus' => 'Hari Khusus (Weekend Namun Absensi Dibuka)',
                            ])
                            ->default('libur')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Misal: Hari Raya Idul Fitri, Upacara Kemerdekaan, dll.'),
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
                    ->searchable()
                    ->description(fn($record) => \Carbon\Carbon::parse($record->tanggal)->isoFormat('dddd')),
                Tables\Columns\BadgeColumn::make('tipe')
                    ->label('Jenis')
                    ->formatStateUsing(fn($state) => $state === 'kerja_khusus' ? 'Hari Khusus' : 'Hari Libur')
                    ->color(fn($state) => $state === 'kerja_khusus' ? 'success' : 'danger'),
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