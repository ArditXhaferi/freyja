<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdvisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advisors = [
            [
                'name' => 'Matias Holmqvist',
                'email' => 'matias.holmqvist@espoo.fi',
                'password' => Hash::make('password'), // Change this to a secure password
                'role' => 'advisor',
                'title' => 'Business Advisor',
                'bio' => 'I am an analytical business developer, marketing specialist and strategist. I also have practical experience in start-up entrepreneurship. My job is to support promising entrepreneurs and help develop their businesses.',
                'languages' => ['Finnish', 'Swedish', 'English'],
                'specialization' => 'marketing',
            ],
            [
                'name' => 'Irene Matinpalo',
                'email' => 'irene.matinpalo@espoo.fi',
                'password' => Hash::make('password'), // Change this to a secure password
                'role' => 'advisor',
                'title' => 'Business Advisor',
                'bio' => 'I work with clients wanting to start a business, but I also help existing companies develop their operations. I specialise in sales and marketing as well as risk management and permit issues.',
                'languages' => ['Finnish', 'English', 'Russian'],
                'specialization' => 'marketing',
            ],
            [
                'name' => 'Ville Tolvanen',
                'email' => 'ville.tolvanen@espoo.fi',
                'password' => Hash::make('password'), // Change this to a secure password
                'role' => 'advisor',
                'title' => 'Business Advisor',
                'bio' => 'I help current and future entrepreneurs start and develop viable business operations. Many of my clients work in the creative industries, but I am happy to help anyone considering entrepreneurship or wanting to develop their business.',
                'languages' => ['Finnish', 'English'],
                'specialization' => 'business_registration',
            ],
            [
                'name' => 'Päivi Lahtelin-Laine',
                'email' => 'paivi.lahtelin-laine@uusyrityskeskus.fi',
                'password' => Hash::make('password'), // Change this to a secure password
                'role' => 'advisor',
                'title' => 'Executive Director, Espoo Regional Enterprise Agency',
                'bio' => 'In my work, I want to support new entrepreneurs during the early stages of their business activities. I want to help create sustainable and profitable business operations and employment opportunities, one company at a time.',
                'languages' => ['Finnish', 'English'],
                'specialization' => 'funding',
            ],
            [
                'name' => 'Nikke Vainikka',
                'email' => 'nikke.vainikka@uusyrityskeskus.fi',
                'password' => Hash::make('password'), // Change this to a secure password
                'role' => 'advisor',
                'title' => 'Project Coordinator, Espoo Regional Enterprise Agency',
                'bio' => 'I advise especially entrepreneurs with an immigrant background and those intending to become entrepreneurs in the twists and turns of Finnish business life, especially by accompanying them on the growth path. I am the coordinator of the Entrepreneurship Path for Immigrants project.',
                'languages' => ['Finnish', 'English', 'Chinese'],
                'specialization' => 'residence_permit',
            ],
        ];

        foreach ($advisors as $advisor) {
            User::updateOrCreate(
                ['email' => $advisor['email']],
                $advisor
            );
        }
    }
}
