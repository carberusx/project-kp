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
                        ->label('Tanggal Masuk Magang')
                        ->required(fn(\Filament\Forms\Get $get) => $get('status') === 'diterima'),
                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Keluar Magang')
                        ->required(fn(\Filament\Forms\Get $get) => $get('status') === 'diterima'),
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
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tgl Keluar')
                    ->date('d M Y')
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
                    ->modalHeading('Terima Pendaftar')
                    ->modalDescription('Akun login akan otomatis dibuat. Silakan tentukan tanggal masuk dan keluar mahasiswa.')
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
                        // Cek apakah akun sudah pernah dibuat
                        if ($record->user_id && $record->user) {
                            \Filament\Notifications\Notification::make()
                                ->title('Akun Sudah Ada')
                                ->body("Akun mahasiswa untuk {$record->nama_lengkap} sudah pernah dibuat sebelumnya.")
                                ->warning()
                                ->send();
                            return;
                        }

                        // Generate password sementara
                        $tempPassword = \Illuminate\Support\Str::random(8);

                        // Buat akun mahasiswa dengan data lengkap dari pendaftaran
                        $user = \App\Models\User::create([
                            'name'        => $record->nama_lengkap,
                            'email'       => $record->email,
                            'password'    => \Illuminate\Support\Facades\Hash::make($tempPassword),
                            'role'        => 'mahasiswa',
                            'nim'         => $record->nim,
                            'universitas' => $record->universitas,
                            'jurusan'     => $record->jurusan,
                            'telepon'     => $record->no_telpon,
                        ]);

                        // Update pendaftaran: status diterima, link user, simpan password di catatan
                        $record->update([
                            'status'          => 'diterima',
                            'user_id'         => $user->id,
                            'tanggal_mulai'   => $data['tanggal_mulai'],
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'catatan_admin'   => 'Akun dibuat otomatis. Password sementara: ' . $tempPassword,
                        ]);

                        // Kirim email notifikasi diterima
                        \Illuminate\Support\Facades\Mail::to($record->email)->send(
                            new \App\Mail\PendaftaranDiterima($record, $tempPassword)
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Pendaftar Diterima')
                            ->body("Akun mahasiswa untuk {$record->nama_lengkap} berhasil dibuat.\nNIM: {$record->nim}\nTelepon: {$record->no_telpon}\nPassword: {$tempPassword}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Pendaftaran $record) => in_array($record->status, ['menunggu', 'wawancara'])),
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function(Pendaftaran $record) {
                        $record->update(['status' => 'ditolak']);
                        \Illuminate\Support\Facades\Mail::to($record->email)->send(
                            new \App\Mail\PendaftaranDitolak($record)
                        );
                    })
                    ->visible(fn(Pendaftaran $record) => in_array($record->status, ['menunggu', 'wawancara'])),
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
