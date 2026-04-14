<?php

namespace App\Filament\Pages;

use App\Models\Pengaturan;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class PengaturanWeb extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $title = 'Pengaturan Sistem';
    protected static ?string $navigationLabel = 'Pengaturan Web';
    protected static ?string $slug = 'pengaturan-web';

    protected static string $view = 'filament.pages.pengaturan-web';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'batas_max_pendaftar' => Pengaturan::getNilai('batas_max_pendaftar', 10),
            'status_pendaftaran' => Pengaturan::getNilai('status_pendaftaran', 'buka') === 'buka',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Pendaftaran Magang')
                    ->description('Atur batas kuota pendaftar dan buka/tutup form pendaftaran.')
                    ->schema([
                        Toggle::make('status_pendaftaran')
                            ->label('Buka Form Pendaftaran')
                            ->helperText('Jika dimatikan, calon mahasiswa magang tidak akan bisa mendaftar.')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                        TextInput::make('batas_max_pendaftar')
                            ->label('Batas Maksimal Pendaftar')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->helperText('Form pendaftaran akan otomatis tertutup jika kuota tercapai (tidak termasuk yang ditolak).'),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Pengaturan::setNilai('batas_max_pendaftar', $state['batas_max_pendaftar']);
        Pengaturan::setNilai('status_pendaftaran', $state['status_pendaftaran'] ? 'buka' : 'tutup');

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
