<?php

namespace App\Filament\Pages;

use App\Models\Pengaturan;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PengaturanWeb extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.pengaturan-web';

    public ?array $data = [];

    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    // ── Hanya Super Admin ─────────────────────────────────────────────────
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan Web';
    }

    public function getTitle(): string
    {
        return 'Pengaturan Sistem';
    }

    public static function getNavigationLabel(): string
    {
        return 'Setting Pendaftaran';
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'pengaturan-web';
    }

    public function mount(): void
    {
        $this->form->fill([
            'batas_max_pendaftar' => Pengaturan::getNilai('batas_max_pendaftar', 10),
            'status_pendaftaran' => Pengaturan::getNilai('status_pendaftaran', 'buka') === 'buka',
            'jam_masuk_standar' => Pengaturan::getNilai('jam_masuk_standar', '07:00:00'),
            'jam_pulang_standar' => Pengaturan::getNilai('jam_pulang_standar', '15:30:00'),
            'nomor_wa_admin' => Pengaturan::getNilai('nomor_wa_admin', '6282328280963'),
            'radius_absensi' => Pengaturan::getNilai('radius_absensi', 50),
            'kantor_lat' => Pengaturan::getNilai('kantor_lat', '-6.9834745188590315'),
            'kantor_lon' => Pengaturan::getNilai('kantor_lon', '110.40770197890485'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konfigurasi Pendaftaran Magang')
                    ->description('Atur batas kuota pendaftar dan buka/tutup form pendaftaran.')
                    ->schema([
                        Toggle::make('status_pendaftaran')
                            ->label('Buka Form Pendaftaran')
                            ->helperText('Jika dimatikan, calon mahasiswa magang tidak akan bisa mendaftar.')
                            ->onColor('success')
                            ->offColor('danger'),
                        TextInput::make('batas_max_pendaftar')
                            ->label('Batas Maksimal Pendaftar')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->helperText('Form pendaftaran akan otomatis tertutup jika kuota tercapai (tidak termasuk yang ditolak).'),
                        TextInput::make('nomor_wa_admin')
                            ->label('Nomor WhatsApp Admin')
                            ->required()
                            ->helperText('Gunakan format 628xxx. Nomor ini digunakan untuk tombol bantuan lupa password di halaman login mahasiswa.'),
                    ])->columns(1),

                Section::make('Konfigurasi Absensi')
                    ->description('Atur batas waktu absensi mahasiswa.')
                    ->schema([
                        \Filament\Forms\Components\TimePicker::make('jam_masuk_standar')
                            ->label('Batas Jam Masuk (Terlambat)')
                            ->required()
                            ->seconds(false)
                            ->helperText('Mahasiswa yang absen masuk lebih dari jam ini akan tercatat terlambat.'),
                        \Filament\Forms\Components\TimePicker::make('jam_pulang_standar')
                            ->label('Batas Jam Pulang (Pulang Cepat & Alpha)')
                            ->required()
                            ->seconds(false)
                            ->helperText('Mahasiswa yang pulang sebelum jam ini tercatat pulang cepat. Cron job Alpha juga akan dieksekusi tepat pada jam ini.'),
                        TextInput::make('radius_absensi')
                            ->label('Radius Absensi (Meter)')
                            ->numeric()
                            ->required()
                            ->minValue(10)
                            ->helperText('Jarak maksimal dalam satuan meter yang diizinkan untuk melakukan absensi (Check-in/out) dari titik koordinat kantor.'),
                        TextInput::make('kantor_lat')
                            ->label('Latitude Kantor')
                            ->required()
                            ->helperText('Contoh: -6.9834745188590315. Dapatkan koordinat ini dari Google Maps dengan klik kanan pada lokasi.'),
                        TextInput::make('kantor_lon')
                            ->label('Longitude Kantor')
                            ->required()
                            ->helperText('Contoh: 110.40770197890485. Dapatkan koordinat ini dari Google Maps dengan klik kanan pada lokasi.'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Pengaturan::setNilai('batas_max_pendaftar', $state['batas_max_pendaftar']);
        Pengaturan::setNilai('status_pendaftaran', $state['status_pendaftaran'] ? 'buka' : 'tutup');
        Pengaturan::setNilai('jam_masuk_standar', $state['jam_masuk_standar']);
        Pengaturan::setNilai('jam_pulang_standar', $state['jam_pulang_standar']);
        Pengaturan::setNilai('nomor_wa_admin', $state['nomor_wa_admin']);
        Pengaturan::setNilai('radius_absensi', $state['radius_absensi']);
        Pengaturan::setNilai('kantor_lat', $state['kantor_lat']);
        Pengaturan::setNilai('kantor_lon', $state['kantor_lon']);

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}