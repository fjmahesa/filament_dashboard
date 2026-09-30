<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

// Import Exporter Bawaan Filament v3
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\ContactMessageExporter;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Inbox Management';
    protected static ?string $modelLabel = 'Pesan Masuk';
    protected static ?string $pluralModelLabel = 'Contact Messages';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pengirim & Pesan')
                    ->schema([
                        Forms\Components\TextInput::make('source_website')
                            ->label('Asal Website')
                            ->disabled(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status Pesan')
                            ->options([
                                'unread'   => 'Belum Dibaca',
                                'read'     => 'Sudah Dibaca',
                                'replied'  => 'Sudah Dibalas',
                                'archived' => 'Diarsipkan',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('No. WA/HP')
                            ->disabled(),
                        Forms\Components\TextInput::make('service')
                            ->label('Layanan yang Dibutuhkan')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->label('Isi Pesan')
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(Tables\Actions\ViewAction::class)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('source_website')
                    ->label('Website')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. WA'),
                Tables\Columns\TextColumn::make('service')
                    ->label('Layanan')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'warning',
                        'replied' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => 'Belum Dibaca',
                        'read' => 'Dibaca',
                        'replied' => 'Dibalas',
                        'archived' => 'Arsip',
                        default => $state,
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export Excel/CSV')
                    ->color('success')
                    ->exporter(ContactMessageExporter::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                        'archived' => 'Diarsipkan',
                    ]),
                SelectFilter::make('source_website')
                    ->label('Filter Website')
                    ->options(fn () => ContactMessage::pluck('source_website', 'source_website')->unique()->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->mountUsing(function (Forms\ComponentContainer $form, ContactMessage $record) {
                        if ($record->status === 'unread') {
                            $record->update(['status' => 'read']);
                            $record->refresh();
                        }
                        
                        $form->fill($record->toArray());
                    }),

                Action::make('whatsapp')
                    ->label('Balas WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->action(function (ContactMessage $record, Tables\Actions\Action $action) {
                        $record->update(['status' => 'replied']);

                        $phone = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        $waUrl = "https://wa.me/{$phone}?text=" . urlencode("Halo {$record->name}, terima kasih telah menghubungi kami melalui {$record->source_website}.");

                        $action->getLivewire()->js("window.open('{$waUrl}', '_blank')");
                    })
                    ->visible(fn (ContactMessage $record) => !empty($record->phone)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Export Baris Terpilih')
                        ->exporter(ContactMessageExporter::class),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
        ];
    }
}