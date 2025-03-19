<?php

namespace App\Filament\Resources;

use App\Models\Page;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\RichEditor;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PageResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Card::make()
                ->schema([
                    // حقل العنوان
                    Forms\Components\TextInput::make('title')
                        ->label('Page Title')
                        ->required()
                        ->maxLength(191),

                    // حقل الـ slug مع زر التوليد
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->unique('pages', 'slug', ignoreRecord: true)
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
                                        // توليد slug بناءً على العنوان
                                        $slug = \Str::slug($title);

                                        // التحقق من أن slug غير مكرر
                                        $originalSlug = $slug;
                                        $counter = 1;
                                        while (Page::where('slug', $slug)->exists()) {
                                            $slug = $originalSlug . '-' . $counter;
                                            $counter++;
                                        }

                                        // تعيين قيمة الـ slug
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
                    // حالة الصفحة (مسودة أم منشورة)
                    Forms\Components\Select::make('is_published')
                        ->label('Status')
                        ->options([
                            0 => 'Draft',
                            1 => 'Published',
                        ])
                        ->default(0)
                        ->required(),

                    // صاحب الصفحة
                    Forms\Components\Select::make('user_id')
                        ->label('Publisher')
                        ->relationship('user', 'name')
                        ->default(fn () => auth()->id())
                        ->required(),
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Publisher'),

                    Tables\Columns\BadgeColumn::make('is_published')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Published' : 'Draft') // ✅ استبدال enum()
                    ->colors([
                        'danger' => fn ($state) => !$state, // ❌ أحمر إذا كانت مسودة
                        'success' => fn ($state) => $state, // ✅ أخضر إذا كانت منشورة
                    ])
                    ->sortable(),
                    Tables\Columns\TextColumn::make('slug')->
                    label('Slug')
                    ->copyable()
                    ->searchable(),


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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
