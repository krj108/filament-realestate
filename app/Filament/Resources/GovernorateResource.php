<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;
use App\Filament\Resources\GovernorateResource\RelationManagers;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Governorate Name')
                ->required()
                ->maxLength(191),

                Forms\Components\TextInput::make('slug')
                ->label('Slug (URL)')
                ->required()
                ->unique('governorates', 'slug', ignoreRecord: true)
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
                                while (Governorate::where('slug', $slug)->exists()) {
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
                Tables\Columns\TextColumn::make('name')->label('المحافظة'),
                Tables\Columns\TextColumn::make('created_at')->label('إنشئ في')->dateTime(),
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
            'index' => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit' => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}
