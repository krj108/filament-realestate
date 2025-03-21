<?php

namespace Database\Seeders;
use Filament\Commands\MakeUserCommand as FilamentMakeUserCommand;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $filamentMakeUserCommand = new FilamentMakeUserCommand();
        $reflector = new \ReflectionObject($filamentMakeUserCommand);

        $getUserModel = $reflector->getMethod('getUserModel');
        $getUserModel->setAccessible(true);
        $getUserModel->invoke($filamentMakeUserCommand)::create([
            'name' => 'admin123',
            'email' => 'admin123@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        $this->call([
            UserSeeder::class, 
        ]);
    }
}
