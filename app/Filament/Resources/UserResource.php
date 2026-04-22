<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\Section; // <-- PENTING: Namespace Layout Baru
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions; // <-- PENTING: Namespace Actions Baru untuk v5
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Akun Mahasiswa';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Akun';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('role', 'mahasiswa');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Akun')
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
                ])->columns(2),

            Section::make('Data Mahasiswa')
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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                // SOLUSI: Menggunakan Actions\ bukan Tables\Actions\
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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