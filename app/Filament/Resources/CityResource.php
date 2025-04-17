<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                               // ربط بمحافظة
                               Forms\Components\Select::make('governorate_id')
                               ->label('المحافظة')
                               ->relationship('governorate', 'name')
                               ->searchable()
                               ->required(),

                               Forms\Components\TextInput::make('name')
                               ->label('اسم المدينة')
                               ->required()
                               ->maxLength(191),


                               Forms\Components\TextInput::make('slug')
                               ->label('Slug (URL)')
                               ->required()
                               ->unique('cities', 'slug', ignoreRecord: true)
                               ->maxLength(191)
                               ->suffixAction(
                                   Forms\Components\Actions\Action::make('generateSlug')
                                       ->label('Generate Slug')
                                       ->tooltip('Generate a slug from the name')
                                       ->color('success')
                                       ->icon('heroicon-o-cog')
                                       ->action(function (callable $get, callable $set) {
                                           $name = $get('name');
                                           if (!empty($name)) {
                                               $slug = \Str::slug($name);
                                               $originalSlug = $slug;
                                               $counter = 1;
                                               while (City::where('slug', $slug)->exists()) {
                                                   $slug = $originalSlug . '-' . $counter;
                                                   $counter++;
                                               }
                                               $set('slug', $slug);
                                           }
                                       })
                               ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('governorate.name')->label('المحافظة'),
                Tables\Columns\TextColumn::make('name')->label('المدينة'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
