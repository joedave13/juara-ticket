<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Livewire\after;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('ticket.city:id,name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'name')
                    ->required(),
                Forms\Components\TextInput::make('total_participant')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('booking_date')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255)
                    ->default('transfer'),
                Forms\Components\TextInput::make('payment_proof')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Booking $record) => $record->phone)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.name')
                    ->description(fn(Booking $record) => $record->ticket->city->name)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->description(fn(Booking $record) => $record->ticket->opened_at->format('H:i') . ' - ' . $record->ticket->closed_at->format('H:i'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state->getColor())
                    ->icon(fn($state) => $state->getIcon()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BookingStatus::class)
            ])
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Action::make('approve')
                            ->requiresConfirmation()
                            ->icon('heroicon-o-check')
                            ->color('success')
                            ->visible(fn(Booking $record) => $record->status === BookingStatus::PENDING)
                            ->action(fn(Booking $record) => $record->approve())
                            ->after(function () {
                                Notification::make()
                                    ->success()
                                    ->title('Approved!')
                                    ->body('Booking ticket has been approved.')
                                    ->send();
                            }),
                        Action::make('reject')
                            ->requiresConfirmation()
                            ->icon('heroicon-o-x-mark')
                            ->color('warning')
                            ->visible(fn(Booking $record) => $record->status === BookingStatus::PENDING)
                            ->action(fn(Booking $record) => $record->reject())
                            ->after(function () {
                                Notification::make()
                                    ->success()
                                    ->title('Rejected!')
                                    ->body('Booking ticket has been rejected.')
                                    ->send();
                            }),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make()
                ])
                    ->icon('heroicon-m-bars-3'),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            // 'create' => Pages\CreateBooking::route('/create'),
            // 'view' => Pages\ViewBooking::route('/{record}'),
            // 'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', BookingStatus::PENDING)->count();
    }
}
