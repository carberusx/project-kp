<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranResource\Pages;
use App\Models\Pendaftaran;
use Filament\Forms;
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\Section; 
use Filament\Schemas\Components\Utilities\Get; // <-- Import Get versi Schemas
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;

    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pendaftar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getModelLabel(): string
    {
        return 'Pendaftar';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Pendaftar';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'menunggu')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('nim')
                        ->label('NIM')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('no_telpon')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->maxLength(20),
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
                    Forms\Components\FileUpload::make('file_cv')
                        ->label('Dokumen / CV')
                        ->disk('public')
                        ->directory('pendaftaran/dokumen')
                        ->downloadable()
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'])
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Keputusan Admin')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'menunggu'   => 'Menunggu',
                            'diterima'   => 'Diterima',
                            'ditolak'    => 'Ditolak',
                        ])
                        ->required()
                        ->live(), // Tambahkan live agar reaktif saat status berubah
                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3),
                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Masuk Magang')
                        // KOREKSI TYPE-HINT GET DI SINI
                        ->required(fn(Get $get) => $get('status') === 'diterima'),
                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Keluar Magang')
                        // KOREKSI TYPE-HINT GET DI SINI
                        ->required(fn(Get $get) => $get('status') === 'diterima'),
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
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('no_telpon')
                    ->label('No. Telpon')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->label('Universitas')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tgl Masuk')
                    ->date('d M Y')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('file_cv')
                    ->label('File CV')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tgl Keluar')
                    ->date('d M Y')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'diterima' => 'success',
                        'ditolak'  => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu'   => 'Menunggu',
                        'diterima'   => 'Diterima',
                        'ditolak'    => 'Ditolak',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                
                \Filament\Actions\Action::make('terima')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Pendaftar')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Masuk')
                            ->default(fn (Pendaftaran $record) => $record->tanggal_mulai)
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Keluar')
                            ->default(fn (Pendaftaran $record) => $record->tanggal_selesai)
                            ->required(),
                    ])
                    ->action(function (array $data, Pendaftaran $record) {
                        if ($record->user_id && $record->user) {
                            Notification::make()->title('Akun Sudah Ada')->warning()->send();
                            return;
                        }

                        $tempPassword = Str::random(8);

                        $user = \App\Models\User::create([
                            'name'        => $record->nama_lengkap,
                            'email'       => $record->email,
                            'password'    => Hash::make($tempPassword),
                            'role'        => 'mahasiswa',
                            'nim'         => $record->nim,
                            'universitas' => $record->universitas,
                            'jurusan'     => $record->jurusan,
                            'telepon'     => $record->no_telpon,
                        ]);

                        $record->update([
                            'status'          => 'diterima',
                            'user_id'         => $user->id,
                            'tanggal_mulai'   => $data['tanggal_mulai'],
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'catatan_admin'   => 'Akun dibuat otomatis. Password sementara: ' . $tempPassword,
                        ]);

                        \Illuminate\Support\Facades\Mail::to($record->email)->send(
                            new \App\Mail\PendaftaranDiterima($record, $tempPassword)
                        );

                        Notification::make()->title('Pendaftar Diterima')->success()->send();
                    })
                    ->visible(fn(Pendaftaran $record) => $record->status === 'menunggu'),

                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Alasan Penolakan')
                            ->rows(3),
                    ])
                    ->action(function(array $data, Pendaftaran $record) {
                        $record->update(['status' => 'ditolak', 'catatan_admin' => $data['catatan_admin'] ?? null]);

                        \Illuminate\Support\Facades\Mail::to($record->email)->send(
                            new \App\Mail\PendaftaranDitolak($record)
                        );

                        Notification::make()->title('Pendaftar Ditolak')->success()->send();
                    })
                    ->visible(fn(Pendaftaran $record) => $record->status === 'menunggu'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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