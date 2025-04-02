<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Property;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PropertyResource\Pages;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Property Name')
                            ->required()
                            ->maxLength(191),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique('properties', 'slug', ignoreRecord: true)
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
                                            while (Property::where('slug', $slug)->exists()) {
                                                $slug = $originalSlug . '-' . $counter;
                                                $counter++;
                                            }
                                            $set('slug', $slug);
                                        }
                                    })
                            ),

                        TinyEditor::make('content')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('uploads')
                            ->rtl()
                            ->columnSpan('full')
                            ->required(),
                            Forms\Components\TextInput::make('location') 
                            ->label('Location')
                            ->required(),
                    ])
                    ->columnSpan('full'),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price ($)')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'villa' => 'Villa',
                                'house' => 'House',
                                'office' => 'Office',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('bedrooms')
                            ->label('Bedrooms')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('bathrooms')
                            ->label('Bathrooms')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('area')
                            ->label('Area (sqm)')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->directory('properties')
                            ->image()
                            ->nullable(),

                            Forms\Components\FileUpload::make('image_gallery') // ✅ تحسين عرض الصور
                            ->label('Image Gallery')
                            ->directory('properties/gallery')
                            ->image()
                            ->multiple()
                            ->columns(3) // ✅ عرض الصور بشكل متناسق
                            ->nullable(),
                            
                         Forms\Components\Select::make('status')
                        ->label('Property Status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),

                    ])
                    ->columns(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(191),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255),

                            Forms\Components\Select::make('user_id')
                            ->label('Posted By')
                            ->options(User::pluck('name', 'id'))
                            ->default(auth()->id())
                            ->disabled(!auth()->user()->hasRole('admin'))
                            ->hidden(!auth()->user()->hasRole('admin'))
                            ->required(),

                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Property Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name') // ✅ إظهار اسم المستخدم
                ->label('Posted By')
                ->sortable()
                ->searchable(),

                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
