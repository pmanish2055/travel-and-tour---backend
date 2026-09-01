<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * File: app/Filament/Pages/Auth/EditProfile.php
 * Purpose: Custom profile page for Filament - shown via user icon dropdown -> My Profile
 *          Allows user to update own profile (name, email, phone, avatar, password) without needing admin UserResource permission.
 *          This fixes requirement: "user le aafno profile aafai update garcha so user ko icon ma profile update garne feature rakh"
 *          Accessible at: /admin/profile
 */
class EditProfile extends BaseEditProfile
{
    /**
     * Add avatar, phone to profile form while keeping email/password logic from parent.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Update your personal information and avatar. Visible in topbar user menu.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('avatars')->disk('public')->visibility('public')
                            ->helperText('Upload your profile picture (200x200 PNG/JPG, max 5MB)')
                            ->columnSpanFull()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg','image/jpg','image/png','image/webp'])
                            ->imagePreviewHeight('250')
                            ->openable()
                            ->downloadable(),

                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Displayed in admin topbar'),

                        TextInput::make('phone')
                            ->label('Phone / WhatsApp')
                            ->tel()
                            ->placeholder('+977-9800000000')
                            ->maxLength(20)
                            ->helperText('Optional'),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used for login. Changing email may require verification.'),
                    ]),

                Section::make('Change Password')
                    ->description('Leave blank to keep current password. Requires current password when changing.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation')
                            ->helperText('Min 8 chars, blank = no change')
                            ->columnSpanFull(),

                        TextInput::make('passwordConfirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => filled($get('password')))
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('currentPassword')
                            ->label('Current Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
                            ->dehydrated(false)
                            ->currentPassword(guard: filament()->getAuthGuard())
                            ->helperText('Required to confirm email or password change')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Ensure phone/avatar are saved correctly.
     * Parent handles password hashing and email verification; we just allow phone/avatar via mass update.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Keep parent logic (if any) but ensure avatar null handling
        // Filament FileUpload already handles storage path
        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return null; // stay on profile page after save
    }
}
