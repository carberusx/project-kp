<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Akun Mahasiswa';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel      = 'Akun';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Hanya tampilkan mahasiswa, bukan admin
        return parent::getEloquentQuery()->where('role', 'mahasiswa');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Akun')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->minLength(8)
                        ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                        ->dehydrated(fn($state) => !empty($state))
                        ->required(fn(string $operation) => $operation === 'create')
                        ->helperText('Kosongkan jika tidak ingin mengubah password'),
                    Forms\Components\Placeholder::make('password_sementara')
                        ->label('Password Sementara')
                        ->content(function (?User $record) {
                            if (!$record) return '—';
                            $catatan = $record->pendaftaran?->catatan_admin ?? '';
                            if (str_contains($catatan, 'Password sementara: ')) {
                                return explode('Password sementara: ', $catatan)[1] ?? '—';
                            }
                            return '—';
                        })
                        ->visible(fn (?User $record) => $record !== null),
                ])->columns(2),

            Forms\Components\Section::make('Data Mahasiswa')
                ->schema([
                    Forms\Components\TextInput::make('nim')
                        ->label('NIM')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('universitas')
                        ->label('Universitas')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('jurusan')
                        ->label('Jurusan')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telepon')
                        ->label('No. Telepon')
                        ->maxLength(20),
                ])->columns(2),

            Forms\Components\Hidden::make('role')
                ->default('mahasiswa'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->default('—'),
                Tables\Columns\TextColumn::make('universitas')
                    ->label('Universitas')
                    ->default('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->default('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telepon')
                    ->label('No. Telepon')
                    ->default('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('password_sementara')
                    ->label('Password Sementara')
                    ->getStateUsing(function (User $record) {
                        $catatan = $record->pendaftaran?->catatan_admin ?? '';
                        if (str_contains($catatan, 'Password sementara: ')) {
                            return explode('Password sementara: ', $catatan)[1] ?? '—';
                        }
                        return '—';
                    })
                    ->copyable()
                    ->copyMessage('Password tersalin')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
