<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TugasResource\Pages;
use App\Models\Tugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TugasResource extends Resource
{
    protected static ?string $model = Tugas::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Tugas';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel      = 'Tugas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('judul')
                ->label('Judul Tugas')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Select::make('tipe')
                ->label('Tipe Tugas')
                ->options([
                    'laporan'  => 'Laporan',
                    'proyek'   => 'Proyek',
                    'evaluasi' => 'Evaluasi',
                    'lainnya'  => 'Lainnya',
                ])
                ->required(),
            Forms\Components\DateTimePicker::make('deadline')
                ->label('Deadline')
                ->required(),
            Forms\Components\Toggle::make('is_aktif')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipe')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'laporan',
                        'success' => 'proyek',
                        'warning' => 'evaluasi',
                        'gray'    => 'lainnya',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengumpulans_count')
                    ->label('Dikumpulkan')
                    ->counts('pengumpulans')
                    ->suffix(' mahasiswa'),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('deadline', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTugas::route('/'),
            'create' => Pages\CreateTugas::route('/create'),
            'edit'   => Pages\EditTugas::route('/{record}/edit'),
        ];
    }
}
