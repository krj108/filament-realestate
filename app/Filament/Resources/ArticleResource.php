<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Article;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\ArticleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ArticleResource\RelationManagers;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Card::make()
                ->schema([
                    // حقل العنوان
                    Forms\Components\TextInput::make('title')
                        ->label('Article Title')
                        ->required()
                        ->maxLength(191),

                    // حقل الـ slug مع زر توليد
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->unique('articles', 'slug', ignoreRecord: true)
                        ->maxLength(191)
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('generateSlug')
                                ->label('Generate Slug')
                                ->tooltip('Click to generate a slug from the title')
                                ->color('success')
                                ->icon('heroicon-o-cog')
                                ->action(function (callable $get, callable $set) {
                                    $title = $get('title');

                                    if (!empty($title)) {
                                
                                        $slug = \Str::slug($title);

                                  
                                        $originalSlug = $slug;
                                        $counter = 1;
                                        while (Article::where('slug', $slug)->exists()) {
                                            $slug = $originalSlug . '-' . $counter;
                                            $counter++;
                                        }

                                   
                                        $set('slug', $slug);
                                    } else {
                                        Filament::notify('warning', 'Please enter a title before generating a slug.');
                                    }
                                })
                        ),

                    // محرر النصوص للمحتوى
                    TinyEditor::make('content')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsVisibility('public')
                        ->fileAttachmentsDirectory('uploads')
                        ->profile('default')
                        ->rtl()
                        ->columnSpan('full')
                        ->required(),
                ])
                ->columnSpan('full'),

            Forms\Components\Card::make()
                ->schema([
                    // التصنيفات
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(Category::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    // الوسوم
                    Forms\Components\MultiSelect::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->required(),
                

                    // صورة المقال
                    Forms\Components\FileUpload::make('image')
                        ->label('Article Image')
                        ->directory('articles')
                        ->nullable(),
                ])
                ->columns(2),

            Forms\Components\Card::make()
                ->schema([
                    // حالة المقال
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),

                    // صاحب المقال
                    Forms\Components\Select::make('user_id')
                        ->label('Publisher')
                        ->relationship('user', 'name')
                        ->default(fn () => auth()->id()),
                ])
                ->columns(2)
                ->columnSpan('full'),

                  // قسم الـ SEO
            Forms\Components\Card::make()
            ->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(191)
                    ->placeholder('Leave empty to use article title'),

                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->maxLength(255),

                Forms\Components\TextInput::make('meta_keywords')
                    ->label('Meta Keywords')
                    ->placeholder('e.g. Laravel, Filament, SEO')
                    ->maxLength(255),
            ])
            ->columns(2)
            ->columnSpan('full'),
        ])

        
        ->columns(1);
}




    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Publisher'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    
}
