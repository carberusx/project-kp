<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranResource\Pages;
use App\Models\Pendaftaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pendaftar';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel      = 'Pendaftar';
    protected static ?string $pluralModelLabel = 'Daftar Pendaftar';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'menunggu')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('universitas')
                        ->label('Universitas/Institusi')
                        ->required(),
                    Forms\Components\TextInput::make('jurusan')
                        ->label('Jurusan/Program Studi')
                        ->required(),
                    Forms\Components\Textarea::make('motivasi')
                        ->label('Motivasi')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Keputusan Admin')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'menunggu'   => 'Menunggu',
                            'wawancara'  => 'Wawancara',
                            'diterima'   => 'Diterima',
                            'ditolak'    => 'Ditolak',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3),
                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai Magang'),
                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai Magang'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->label('Universitas')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'menunggu',
                        'info'    => 'wawancara',
                        'success' => 'diterima',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'menunggu'  => 'Menunggu',
                        'wawancara' => 'Wawancara',
                        'diterima'  => 'Diterima',
                        'ditolak'   => 'Ditolak',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu'   => 'Menunggu',
                        'wawancara'  => 'Wawancara',
                        'diterima'   => 'Diterima',
                        'ditolak'    => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('terima')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Pendaftaran $record) => $record->update(['status' => 'diterima']))
                    ->visible(fn(Pendaftaran $record) => $record->status === 'menunggu'),
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Pendaftaran $record) => $record->update(['status' => 'ditolak']))
                    ->visible(fn(Pendaftaran $record) => $record->status === 'menunggu'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPendaftarans::route('/'),
            'create' => Pages\CreatePendaftaran::route('/create'),
            'edit'   => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
