<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;
use Filament\Forms\Concerns\InteractsWithForms;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.edit-profile';
    protected static ?string $title = 'Edit Profile';

    public $data = [];
    public $passwordData = [];

    public function mount()
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getProfileFormSchema())
                ->model(Auth::user()->getMorphClass()) // تعديل هنا
                ->statePath('data'),
            
            'passwordForm' => $this->makeForm()
                ->schema($this->getPasswordFormSchema())
                ->statePath('passwordData'),
        ];
    }

    protected function getProfileFormSchema(): array
    {
        $user = Auth::user(); 
        
        return [
            Forms\Components\Section::make('Personal Information')
                ->description('Update your account profile information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->rules([
                            Rule::unique('users', 'email')->ignore(Auth::id()),
                        ]),
                        
                        Forms\Components\FileUpload::make('avatar_url')
                        ->label('Profile Photo')
                        ->avatar()
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('avatars')
                        ->visibility('public')
                        ->preserveFilenames() 
                        ->helperText('Maximum file size: 2MB. Allowed formats: PNG, JPG')
                ])
                ->columns(2)
        ];
    }

    protected function getPasswordFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Update Password')
                ->description('Ensure your account is using a strong password')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('New Password')
                        ->password()
                        ->required()
                        ->minLength(8)
                        ->confirmed()
                        ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/')
                        ->helperText('Must contain at least one uppercase, lowercase, number and special character'),
                    
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required()
                ])
                ->columns(2)
        ];
    }

    public function saveProfile()
    {
        $user = Auth::user();
        $user->update($this->form->getState());

        Notification::make()
            ->title('Profile Updated Successfully')
            ->success()
            ->send();
    }

    public function savePassword()
    {
        $this->passwordForm->validate();

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->passwordData['password'])
        ]);

        $this->passwordForm->fill();

        Notification::make()
            ->title('Password Updated Successfully')
            ->success()
            ->send();
    }
}