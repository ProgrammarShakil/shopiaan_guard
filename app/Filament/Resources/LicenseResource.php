<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'License Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('license_key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => License::generateLicenseKey()),
                Forms\Components\TextInput::make('domain')
                    ->nullable(),
                Forms\Components\TextInput::make('ip_address')
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                    ])
                    ->required()
                    ->default('active'),
                Forms\Components\DateTimePicker::make('activated_at')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->nullable(),
                Forms\Components\TextInput::make('max_domains')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\TextInput::make('used_domains')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\KeyValue::make('features')
                    ->nullable(),
                Forms\Components\Select::make('license_type')
                    ->options([
                        'regular' => 'Regular',
                        'extended' => 'Extended',
                        'lifetime' => 'Lifetime',
                    ])
                    ->required()
                    ->default('regular'),
                Forms\Components\Toggle::make('is_trial')
                    ->default(false),
                Forms\Components\Textarea::make('notes')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_type')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_trial')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                    ]),
                Tables\Filters\SelectFilter::make('license_type')
                    ->options([
                        'regular' => 'Regular',
                        'extended' => 'Extended',
                        'lifetime' => 'Lifetime',
                    ]),
                Tables\Filters\Filter::make('is_trial')
                    ->query(fn (Builder $query): Builder => $query->where('is_trial', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
} 