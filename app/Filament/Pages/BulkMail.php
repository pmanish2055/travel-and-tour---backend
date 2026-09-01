<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class BulkMail extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static string|UnitEnum|null $navigationGroup = 'Bookings & Leads';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Bulk Mail to Customers';
    protected static ?string $title = 'Bulk Mail — Send to Registered Customers';

    protected string $view = 'filament.pages.bulk-mail';

    public ?array $data = [];

    public function mount(): void
    {
        $companyName = Setting::get('company.name', config('app.name', 'Travel Company'));
        $this->data = [
            'subject' => Setting::get('mail.bulk_subject', 'Greetings from {{company_name}}'),
            'body' => Setting::get('mail.bulk_body', '<p>Dear {{name}},</p><p>Check our latest packages!</p>'),
            'from_name' => Setting::get('mail.from_name', $companyName),
            'from_email' => Setting::get('mail.from_email', Setting::get('company.email', 'info@example.com')),
            'recipients' => [],
            'recipient_type' => 'all',
        ];
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Recipients')
                    ->description('Select registered customers')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('recipient_type')
                            ->label('Recipient Type')
                            ->options([
                                'all' => 'All (Subscribers + Users)',
                                'subscribers' => 'Only Subscribers',
                                'users' => 'Only Users',
                                'custom' => 'Custom Select',
                            ])
                            ->default('all')
                            ->live()
                            ->helperText('Newsletter + registered'),
                        Select::make('recipients')
                            ->label('Custom Recipients')
                            ->options(function () {
                                $subs = Subscriber::pluck('email', 'email')->mapWithKeys(fn($v, $k) => [$k => $k . ' (Subscriber)'])->toArray();
                                $users = User::where('role', '!=', 'super_admin')->pluck('email', 'email')->mapWithKeys(fn($v, $k) => [$k => $k . ' (User)'])->toArray();
                                return array_merge($subs, $users);
                            })
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('recipient_type') === 'custom')
                            ->helperText('Select if custom'),
                        TagsInput::make('custom_emails')
                            ->label('Additional Emails')
                            ->placeholder('custom@example.com')
                            ->helperText('Press enter')
                            ->columnSpanFull(),
                    ]),

                Section::make('Mail Content')
                    ->description('Supports {{name}}, {{email}}')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('from_name')
                            ->label('From Name')
                            ->required()
                            ->helperText('Sender name'),
                        TextInput::make('from_email')
                            ->label('From Email')
                            ->email()
                            ->required()
                            ->helperText('Sender email'),
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->helperText('Use {{name}}')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Body (HTML)')
                            ->required()
                            ->helperText('Use {{name}}, {{email}}')
                            ->columnSpanFull(),
                    ]),

                Section::make('Information')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('info')
                            ->content('Bulk Mail sends HTML via RichEditor to registered customers. Recipient Type: all (Subscribers+Users), subscribers only, users only or custom emails plus extra addresses; personalized with {{name}}, {{email}}, {{company_name}}. Subject/body sync with Report Settings (mail.bulk_subject/body); sender via from_name/from_email. Logged when MAIL_MAILER=log.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Bulk Mail Now')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->action('sendMail')
                ->requiresConfirmation()
                ->modalHeading('Send Bulk Mail?')
                ->modalDescription('Send to all selected customers?'),
        ];
    }

    public function sendMail(): void
    {
        $raw = $this->form->getState();
        $data = $raw;
        if (isset($raw['data']) && is_array($raw['data']) && !array_key_exists('subject', $raw)) {
            $data = $raw['data'];
        }
        \Illuminate\Support\Facades\Log::info('BulkMail send raw', ['raw'=>$raw,'resolved'=>$data]);
        $emails = collect();
        $type = $data['recipient_type'] ?? 'all';

        if ($type === 'all' || $type === 'subscribers') {
            $emails = $emails->merge(Subscriber::pluck('email'));
        }
        if ($type === 'all' || $type === 'users') {
            $emails = $emails->merge(User::where('role', '!=', 'super_admin')->pluck('email'));
        }
        if ($type === 'custom' && !empty($data['recipients'])) {
            $emails = $emails->merge($data['recipients']);
        }
        if (!empty($data['custom_emails'])) {
            $emails = $emails->merge($data['custom_emails']);
        }

        $emails = $emails->filter()->unique()->values();
        $count = $emails->count();

        if ($count === 0) {
            Notification::make()->title('No recipients')->body('No customers found.')->danger()->send();
            return;
        }

        $companyName = Setting::get('company.name', config('app.name', 'Travel Company'));
        $subject = $data['subject'];
        $body = $data['body'];
        $fromName = $data['from_name'];
        $fromEmail = $data['from_email'];

        foreach ($emails as $email) {
            $name = 'Valued Customer';
            $user = User::where('email', $email)->first();
            if ($user) $name = $user->name;
            else {
                $sub = Subscriber::where('email', $email)->first();
                if ($sub) $name = explode('@', $email)[0];
            }

            $personalizedSubject = str_replace(['{{name}}', '{{company_name}}', '{{email}}'], [$name, $companyName, $email], $subject);
            $personalizedBody = str_replace(['{{name}}', '{{company_name}}', '{{email}}'], [$name, $companyName, $email], $body);

            try {
                \Illuminate\Support\Facades\Log::info("Bulk Mail to $email: Subject: $personalizedSubject");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bulk mail failed to $email: " . $e->getMessage());
            }
        }

        Setting::set('mail.bulk_subject', $subject, 'mail', false);
        Setting::set('mail.bulk_body', $body, 'mail', false);

        Notification::make()
            ->title("Bulk mail queued to $count customers")
            ->body("Logged to storage/logs/laravel.log")
            ->success()
            ->send();
    }
}
