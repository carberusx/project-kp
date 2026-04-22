<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use Filament\Forms;
use Filament\Schemas\Schema; // <-- Versi v5
use Filament\Schemas\Components\Section; // <-- Versi v5
use Filament\Schemas\Components\Utilities\Get; // <-- Versi v5
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions; // <-- Untuk Action Edit/Delete/View

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    // --- Mengubah Property Menjadi Method (Standar v5) ---
    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationLabel(): string
    {
        return 'Log Absensi';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Absensi';
    }

    // --- Parameter Form Menjadi Schema ---
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Utama')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Mahasiswa')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->required(),
                    Forms\Components\TimePicker::make('jam_masuk')
                        ->label('Jam Masuk'),
                    Forms\Components\TimePicker::make('jam_keluar')
                        ->label('Jam Keluar'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'hadir' => 'Hadir',
                            'izin'  => 'Izin',
                            'sakit' => 'Sakit',
                            'alpha' => 'Alpha',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Bukti Kehadiran (Selfie)')
                ->schema([
                    Forms\Components\Placeholder::make('foto_masuk_view')
                        ->label('Foto Masuk')
                        ->content(fn ($record) => $record && $record->foto_masuk 
                            ? new \Illuminate\Support\HtmlString("<img src='".asset('storage/'.$record->foto_masuk)."' alt='Foto Masuk' class='rounded-xl border border-slate-200 w-full object-cover' style='max-height: 300px;'>") 
                            : 'Belum ada foto'),
                    
                    Forms\Components\Placeholder::make('foto_keluar_view')
                        ->label('Foto Keluar')
                        ->content(fn ($record) => $record && $record->foto_keluar 
                            ? new \Illuminate\Support\HtmlString("<img src='".asset('storage/'.$record->foto_keluar)."' alt='Foto Keluar' class='rounded-xl border border-slate-200 w-full object-cover' style='max-height: 300px;'>") 
                            : 'Belum ada foto'),
                ])->columns(2),

            Section::make('Informasi Lokasi GPS')
                ->schema([
                    Forms\Components\TextInput::make('alamat_masuk')
                        ->label('Alamat Masuk')
                        ->columnSpanFull()
                        ->disabled(),
                    Forms\Components\TextInput::make('alamat_keluar')
                        ->label('Alamat Keluar')
                        ->columnSpanFull()
                        ->disabled(),
                        
                    Forms\Components\Placeholder::make('map_masuk_view')
                        ->label('Peta Lokasi Masuk')
                        ->content(fn ($record) => $record && $record->latitude_masuk 
                            ? new \Illuminate\Support\HtmlString("<iframe width='100%' height='250' class='rounded-xl border border-slate-200' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.com/maps?q={$record->latitude_masuk},{$record->longitude_masuk}&z=15&output=embed'></iframe>") 
                            : '—'),
                            
                    Forms\Components\Placeholder::make('map_keluar_view')
                        ->label('Peta Lokasi Keluar')
                        ->content(fn ($record) => $record && $record->latitude_keluar 
                            ? new \Illuminate\Support\HtmlString("<iframe width='100%' height='250' class='rounded-xl border border-slate-200' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.com/maps?q={$record->latitude_keluar},{$record->longitude_keluar}&z=15&output=embed'></iframe>") 
                            : '—'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.universitas')
                    ->label('Universitas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk'),
                Tables\Columns\TextColumn::make('jam_keluar')
                    ->label('Keluar'),
                Tables\Columns\ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->square()
                    ->size(80)
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('foto_keluar')
                    ->label('Foto Keluar')
                    ->disk('public')
                    ->square()
                    ->size(80)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('alamat_masuk')
                    ->label('Lokasi Masuk')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alamat_masuk)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('latitude_masuk')
                    ->label('Map')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->latitude_masuk
                            ? ' Lihat Map'
                            : '—'
                    )
                    ->url(fn ($record) =>
                        $record->latitude_masuk
                            ? "https://maps.google.com/?q={$record->latitude_masuk},{$record->longitude_masuk}"
                            : null
                    )
                    ->openUrlInNewTab()
                    ->toggleable(),
                
                // Mengganti BadgeColumn ke TextColumn badge v5
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin'  => 'warning',
                        'sakit' => 'info',
                        'alpha' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                    
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin'  => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn($query) => $query->whereDate('tanggal', today())),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit'   => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}