<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class ManageCompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;
    protected static string|UnitEnum|null $navigationGroup = 'Company';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Company Settings';
    protected static ?string $title = 'Company Settings';

    protected string $view = 'filament.pages.manage-company-settings';

    public ?array $data = [];

    /**
     * Restrict Tokens & Keys and all company settings to super_admin only.
     * Filament Shield generates per-resource permissions but custom Pages need explicit gate.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        // Allow super_admin always; fallback to Shield permission if roles not seeded
        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) return true;
        if (method_exists($user, 'can') && $user->can('page_ManageCompanySettings')) return true;
        // Default deny if not super_admin (prevents editors reading tokens.esewa_secret etc.)
        return $user->hasRole('super_admin');
    }

    public function mount(): void
    {
        $this->data = [
            'company_name' => Setting::get('company.name', config('app.name', 'Travel Company')),
            'company_tagline' => Setting::get('company.tagline', 'Discover Your Next Journey'),
            'company_description' => Setting::get('company.description', 'Leading tour operator.'),
            'company_email' => Setting::get('company.email', 'info@example.com'),
            'company_phone' => Setting::get('company.phone', '+977-1-4000000'),
            'company_whatsapp' => Setting::get('company.whatsapp', '+977-9800000000'),
            'company_address' => Setting::get('company.address', 'Kathmandu, Nepal'),
            'company_city' => Setting::get('company.city', 'Kathmandu'),
            'company_province' => Setting::get('company.province', 'Bagmati'),
            'company_map_embed' => Setting::get('company.map_embed', ''),
            'company_business_hours' => Setting::get('company.business_hours', '9AM - 6PM, Sunday - Friday'),
            'company_logo' => Setting::get('company.logo', ''),
            'company_favicon' => Setting::get('company.favicon', ''),
            'company_cover' => Setting::get('company.cover', ''),
            'company_primary_color' => Setting::get('company.primary_color', '#f59e0b'),
            'company_pan' => Setting::get('company.pan', '123456789'),
            'company_reg_no' => Setting::get('company.reg_no', '12345/070/071'),
            'company_taan_license' => Setting::get('company.taan_license', 'TAAN 1234'),
            'company_ntb_license' => Setting::get('company.ntb_license', 'NTB 5678'),
            'company_facebook' => Setting::get('company.facebook', ''),
            'company_instagram' => Setting::get('company.instagram', ''),
            'company_youtube' => Setting::get('company.youtube', ''),
            'company_linkedin' => Setting::get('company.linkedin', ''),
            'company_tiktok' => Setting::get('company.tiktok', ''),
            'seo_site_title' => Setting::get('seo.site_title', config('app.name', 'Travel Company') . ' - Best Trekking & Tour Operator'),
            'seo_meta_description' => Setting::get('seo.meta_description', 'Book your dream trek.'),
            'seo_keywords' => Setting::get('seo.keywords', 'trek, tour, travel'),
            'tokens_google_map_api_key' => Setting::get('tokens.google_map_api_key', ''),
            'tokens_google_analytics_id' => Setting::get('tokens.google_analytics_id', ''),
            'tokens_facebook_pixel_id' => Setting::get('tokens.facebook_pixel_id', ''),
            'tokens_smtp_host' => Setting::get('tokens.smtp_host', ''),
            'tokens_smtp_port' => Setting::get('tokens.smtp_port', ''),
            'tokens_smtp_user' => Setting::get('tokens.smtp_user', ''),
            'tokens_smtp_pass' => Setting::get('tokens.smtp_pass', ''),
            'tokens_esewa_merchant_code' => Setting::get('tokens.esewa_merchant_code', ''),
            'tokens_esewa_secret' => Setting::get('tokens.esewa_secret', ''),
            'tokens_khalti_public_key' => Setting::get('tokens.khalti_public_key', ''),
            'tokens_khalti_secret' => Setting::get('tokens.khalti_secret', ''),
            'tokens_stripe_publishable' => Setting::get('tokens.stripe_publishable', ''),
            'tokens_stripe_secret' => Setting::get('tokens.stripe_secret', ''),
            'tokens_recaptcha_site_key' => Setting::get('tokens.recaptcha_site_key', ''),
            'tokens_recaptcha_secret' => Setting::get('tokens.recaptcha_secret', ''),
            'tokens_whatsapp_token' => Setting::get('tokens.whatsapp_token', ''),
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Company Settings Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('general_tab_info')
                                            ->content('This tab manages basic package details.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Company Identity')
                                    ->description('Master company identity')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('company_name')
                                            ->label('Company Legal Name')
                                            ->placeholder('Your Company Pvt. Ltd.')
                                            ->helperText('Shown in footer, invoices')
                                            ->nullable()
                                            ->maxLength(255),
                                        TextInput::make('company_tagline')
                                            ->label('Tagline')
                                            ->placeholder('Discover Your Next Journey')
                                            ->helperText('Short tagline for SEO')
                                            ->nullable()
                                            ->maxLength(255),
                                        Textarea::make('company_description')
                                            ->label('Company Description')
                                            ->rows(3)
                                            ->helperText('About text for SEO')
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Business Hours')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('company_business_hours')
                                            ->label('Business Hours')
                                            ->placeholder('9AM - 6PM, Sunday - Friday')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('contact_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Contact Details')
                                    ->description('Shown in header, footer')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('company_email')
                                            ->label('Company Email')
                                            ->email()
                                            ->rules(['nullable', 'email'])
                                            ->helperText('Inquiry notifications (optional)'),
                                        TextInput::make('company_phone')
                                            ->label('Phone')
                                            ->tel()
                                            ->helperText('+977-1-4440000'),
                                        TextInput::make('company_whatsapp')
                                            ->label('WhatsApp')
                                            ->tel()
                                            ->helperText('For WhatsApp button'),
                                        TextInput::make('company_address')
                                            ->label('Address')
                                            ->columnSpanFull()
                                            ->helperText('Full address'),
                                        TextInput::make('company_city')
                                            ->label('City')
                                            ->placeholder('Kathmandu'),
                                        TextInput::make('company_province')
                                            ->label('Province')
                                            ->placeholder('Bagmati'),
                                        Textarea::make('company_map_embed')
                                            ->label('Google Map Embed')
                                            ->rows(3)
                                            ->helperText('Embed iframe or API key')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Branding')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('branding_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Branding')
                                    ->description('Rebrand for any site')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('company_logo')
                                            ->label('Company Logo')
                                            ->image()
                                            ->directory('company')
                                            ->helperText('200x60 PNG, header')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        FileUpload::make('company_favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('company')
                                            ->helperText('16x16 or 32x32')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        FileUpload::make('company_cover')
                                            ->label('Cover Image')
                                            ->image()
                                            ->directory('company')
                                            ->helperText('About hero image')
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        TextInput::make('company_primary_color')
                                            ->label('Primary Color')
                                            ->placeholder('#f59e0b')
                                            ->helperText('Brand color')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Legal')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('legal_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Legal & Licenses')
                                    ->description('Footer and invoices')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('company_pan')
                                            ->label('PAN / VAT No.')
                                            ->placeholder('123456789')
                                            ->helperText('Tax number'),
                                        TextInput::make('company_reg_no')
                                            ->label('Company Reg No.')
                                            ->placeholder('12345/070/071'),
                                        TextInput::make('company_taan_license')
                                            ->label('TAAN License')
                                            ->placeholder('TAAN 1234')
                                            ->helperText('Tourism license'),
                                        TextInput::make('company_ntb_license')
                                            ->label('Tourism Board License')
                                            ->placeholder('NTB 5678')
                                            ->helperText('Board license'),
                                    ]),
                            ]),

                        Tab::make('Social')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('social_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Social Links')
                                    ->description('Footer and header')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('company_facebook')
                                            ->label('Facebook URL')
                                            ->placeholder('https://facebook.com/yourcompany')
                                            ->rules(['nullable', 'url'])
                                            ->maxLength(500),
                                        TextInput::make('company_instagram')
                                            ->label('Instagram URL')
                                            ->placeholder('https://instagram.com/yourcompany')
                                            ->rules(['nullable', 'url'])
                                            ->maxLength(500),
                                        TextInput::make('company_youtube')
                                            ->label('YouTube URL')
                                            ->rules(['nullable', 'url'])
                                            ->maxLength(500),
                                        TextInput::make('company_linkedin')
                                            ->label('LinkedIn URL')
                                            ->rules(['nullable', 'url'])
                                            ->maxLength(500),
                                        TextInput::make('company_tiktok')
                                            ->label('TikTok URL')
                                            ->rules(['nullable', 'url'])
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('seo_tab_info')
                                            ->content('This tab handles SEO and publishing.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('SEO')
                                    ->description('Reusable for all pages')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('seo_site_title')
                                            ->label('Site Title')
                                            ->maxLength(60)
                                            ->helperText('60 chars, Google title')
                                            ->columnSpanFull(),
                                        Textarea::make('seo_meta_description')
                                            ->label('Meta Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('160 chars, snippet')
                                            ->columnSpanFull(),
                                        TagsInput::make('seo_keywords')
                                            ->label('SEO Keywords')
                                            ->placeholder('nepal trek')
                                            ->helperText('Press enter')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Tokens & Keys')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('tokens_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Google & Analytics')
                                    ->description('Encrypted at rest')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('tokens_google_map_api_key')
                                            ->label('Google Maps Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Destination maps'),
                                        TextInput::make('tokens_google_analytics_id')
                                            ->label('Google Analytics ID')
                                            ->placeholder('G-XXXXXXXX')
                                            ->helperText('e.g., G-XXXXXXXX'),
                                        TextInput::make('tokens_facebook_pixel_id')
                                            ->label('Facebook Pixel ID')
                                            ->placeholder('1234567890'),
                                    ]),
                                Section::make('SMTP Email')
                                    ->description('Inquiry/booking emails')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('tokens_smtp_host')
                                            ->label('SMTP Host')
                                            ->password()->revealable()
                                            ->placeholder('smtp.mailtrap.io'),
                                        TextInput::make('tokens_smtp_port')
                                            ->label('SMTP Port')
                                            ->placeholder('2525'),
                                        TextInput::make('tokens_smtp_user')
                                            ->label('SMTP User')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_smtp_pass')
                                            ->label('SMTP Pass')
                                            ->password()->revealable(),
                                    ]),
                                Section::make('Payment Gateways')
                                    ->description('eSewa, Khalti, Stripe')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('tokens_esewa_merchant_code')
                                            ->label('eSewa Code')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_esewa_secret')
                                            ->label('eSewa Secret')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_khalti_public_key')
                                            ->label('Khalti Public Key')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_khalti_secret')
                                            ->label('Khalti Secret')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_stripe_publishable')
                                            ->label('Stripe Publishable')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_stripe_secret')
                                            ->label('Stripe Secret')
                                            ->password()->revealable(),
                                    ]),
                                Section::make('Other Tokens')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('tokens_recaptcha_site_key')
                                            ->label('reCAPTCHA Site Key'),
                                        TextInput::make('tokens_recaptcha_secret')
                                            ->label('reCAPTCHA Secret')
                                            ->password()->revealable(),
                                        TextInput::make('tokens_whatsapp_token')
                                            ->label('WhatsApp Token')
                                            ->password()->revealable()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                    ])            ])
            ->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Company Settings')
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $map = [
            'company_name' => ['key' => 'company.name', 'encrypted' => false, 'group' => 'company'],
            'company_tagline' => ['key' => 'company.tagline', 'encrypted' => false, 'group' => 'company'],
            'company_description' => ['key' => 'company.description', 'encrypted' => false, 'group' => 'company'],
            'company_business_hours' => ['key' => 'company.business_hours', 'encrypted' => false, 'group' => 'company'],
            'company_email' => ['key' => 'company.email', 'encrypted' => false, 'group' => 'company'],
            'company_phone' => ['key' => 'company.phone', 'encrypted' => false, 'group' => 'company'],
            'company_whatsapp' => ['key' => 'company.whatsapp', 'encrypted' => false, 'group' => 'company'],
            'company_address' => ['key' => 'company.address', 'encrypted' => false, 'group' => 'company'],
            'company_city' => ['key' => 'company.city', 'encrypted' => false, 'group' => 'company'],
            'company_province' => ['key' => 'company.province', 'encrypted' => false, 'group' => 'company'],
            'company_map_embed' => ['key' => 'company.map_embed', 'encrypted' => false, 'group' => 'company'],
            'company_logo' => ['key' => 'company.logo', 'encrypted' => false, 'group' => 'company'],
            'company_favicon' => ['key' => 'company.favicon', 'encrypted' => false, 'group' => 'company'],
            'company_cover' => ['key' => 'company.cover', 'encrypted' => false, 'group' => 'company'],
            'company_primary_color' => ['key' => 'company.primary_color', 'encrypted' => false, 'group' => 'company'],
            'company_pan' => ['key' => 'company.pan', 'encrypted' => false, 'group' => 'company'],
            'company_reg_no' => ['key' => 'company.reg_no', 'encrypted' => false, 'group' => 'company'],
            'company_taan_license' => ['key' => 'company.taan_license', 'encrypted' => false, 'group' => 'company'],
            'company_ntb_license' => ['key' => 'company.ntb_license', 'encrypted' => false, 'group' => 'company'],
            'company_facebook' => ['key' => 'company.facebook', 'encrypted' => false, 'group' => 'company'],
            'company_instagram' => ['key' => 'company.instagram', 'encrypted' => false, 'group' => 'company'],
            'company_youtube' => ['key' => 'company.youtube', 'encrypted' => false, 'group' => 'company'],
            'company_linkedin' => ['key' => 'company.linkedin', 'encrypted' => false, 'group' => 'company'],
            'company_tiktok' => ['key' => 'company.tiktok', 'encrypted' => false, 'group' => 'company'],
            'seo_site_title' => ['key' => 'seo.site_title', 'encrypted' => false, 'group' => 'seo'],
            'seo_meta_description' => ['key' => 'seo.meta_description', 'encrypted' => false, 'group' => 'seo'],
            'seo_keywords' => ['key' => 'seo.keywords', 'encrypted' => false, 'group' => 'seo'],
            'tokens_google_map_api_key' => ['key' => 'tokens.google_map_api_key', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_google_analytics_id' => ['key' => 'tokens.google_analytics_id', 'encrypted' => false, 'group' => 'tokens'],
            'tokens_facebook_pixel_id' => ['key' => 'tokens.facebook_pixel_id', 'encrypted' => false, 'group' => 'tokens'],
            'tokens_smtp_host' => ['key' => 'tokens.smtp_host', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_smtp_port' => ['key' => 'tokens.smtp_port', 'encrypted' => false, 'group' => 'tokens'],
            'tokens_smtp_user' => ['key' => 'tokens.smtp_user', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_smtp_pass' => ['key' => 'tokens.smtp_pass', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_esewa_merchant_code' => ['key' => 'tokens.esewa_merchant_code', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_esewa_secret' => ['key' => 'tokens.esewa_secret', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_khalti_public_key' => ['key' => 'tokens.khalti_public_key', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_khalti_secret' => ['key' => 'tokens.khalti_secret', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_stripe_publishable' => ['key' => 'tokens.stripe_publishable', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_stripe_secret' => ['key' => 'tokens.stripe_secret', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_recaptcha_site_key' => ['key' => 'tokens.recaptcha_site_key', 'encrypted' => false, 'group' => 'tokens'],
            'tokens_recaptcha_secret' => ['key' => 'tokens.recaptcha_secret', 'encrypted' => true, 'group' => 'tokens'],
            'tokens_whatsapp_token' => ['key' => 'tokens.whatsapp_token', 'encrypted' => true, 'group' => 'tokens'],
        ];

        foreach ($map as $field => $meta) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                \App\Models\Setting::set($meta['key'], $value, $meta['group'], $meta['encrypted'], "Managed via Company Settings page (master)");
            }
        }

        foreach (['all','company','seo','general','tokens','reports','mail'] as $g) {
            \Illuminate\Support\Facades\Cache::forget('settings:'.$g);
            \Illuminate\Support\Facades\Cache::forget('settings:'.$g.'+seo');
        }
        \Illuminate\Support\Facades\Cache::forget('settings:all');
        \Illuminate\Support\Facades\Cache::forget('settings:company');
        \Illuminate\Support\Facades\Cache::forget('settings:seo');
        \Illuminate\Support\Facades\Cache::forget('settings:company+seo');
        \Illuminate\Support\Facades\Cache::forget('navigation');
        \Illuminate\Support\Facades\Cache::forget('homepage:aggregate');
        \Illuminate\Support\Facades\Cache::forget('site:stats');

        Notification::make()
            ->title('Company settings saved')
            ->body('Settings saved successfully.')
            ->success()
            ->send();
    }
}
