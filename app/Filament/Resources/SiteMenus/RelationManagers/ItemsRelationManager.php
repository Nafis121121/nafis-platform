<?php

namespace App\Filament\Resources\SiteMenus\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'آیتم‌های منو';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label_fa')->label('عنوان فارسی')->required()->maxLength(120),
            TextInput::make('label_en')->label('عنوان انگلیسی')->maxLength(120),
            Select::make('page_id')
                ->label('صفحه داخلی')
                ->relationship('page', 'title_fa')
                ->searchable()->preload(),
            TextInput::make('href')
                ->label('لینک دستی')
                ->helperText('اگر صفحه داخلی انتخاب شده باشد، لینک صفحه در اولویت است.'),
            Select::make('parent_id')
                ->label('آیتم والد')
                ->options(fn () => $this->getOwnerRecord()->items()->whereNull('parent_id')->pluck('label_fa', 'id')->all())
                ->searchable(),
            TextInput::make('icon')->label('آیکن'),
            TextInput::make('position')->label('ترتیب')->numeric()->default(0)->required(),
            Toggle::make('visible')->label('نمایش')->default(true),
            Toggle::make('open_in_new_tab')->label('باز شدن در تب جدید')->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('position')->label('ترتیب')->sortable(),
                TextColumn::make('label_fa')->label('عنوان')->searchable(),
                TextColumn::make('parent.label_fa')->label('والد')->placeholder('—'),
                TextColumn::make('page.title_fa')->label('صفحه')->placeholder('لینک دستی'),
                IconColumn::make('visible')->label('نمایش')->boolean(),
                IconColumn::make('open_in_new_tab')->label('تب جدید')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('افزودن آیتم')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
