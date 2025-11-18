<?php

namespace Database\Seeders;

use App\Models\Governorate;
use App\Models\GovernorateTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            [
                'code' => 'DM',
                'translations' => [
                    'ar' => ['name' => 'دمشق'],
                    'en' => ['name' => 'Damascus']
                ]
            ],
            [
                'code' => 'RD',
                'translations' => [
                    'ar' => ['name' => 'ريف دمشق'],
                    'en' => ['name' => 'Rif Dimashq']
                ]
            ],
            [
                'code' => 'QU',
                'translations' => [
                    'ar' => ['name' => 'القنيطرة'],
                    'en' => ['name' => 'Quneitra']
                ]
            ],
            [
                'code' => 'HA',
                'translations' => [
                    'ar' => ['name' => 'الحسكة'],
                    'en' => ['name' => 'Al-Hasakah']
                ]
            ],
            [
                'code' => 'RA',
                'translations' => [
                    'ar' => ['name' => 'الرقة'],
                    'en' => ['name' => 'Ar-Raqqah']
                ]
            ],
            [
                'code' => 'TA',
                'translations' => [
                    'ar' => ['name' => 'طرطوس'],
                    'en' => ['name' => 'Tartus']
                ]
            ],
            [
                'code' => 'SU',
                'translations' => [
                    'ar' => ['name' => 'السويداء'],
                    'en' => ['name' => 'As-Suwayda']
                ]
            ],
            [
                'code' => 'DI',
                'translations' => [
                    'ar' => ['name' => 'دير الزور'],
                    'en' => ['name' => 'Deir ez-Zor']
                ]
            ],
            [
                'code' => 'ID',
                'translations' => [
                    'ar' => ['name' => 'إدلب'],
                    'en' => ['name' => 'Idlib']
                ]
            ],
            [
                'code' => 'HL',
                'translations' => [
                    'ar' => ['name' => 'حلب'],
                    'en' => ['name' => 'Aleppo']
                ]
            ],
            [
                'code' => 'HM',
                'translations' => [
                    'ar' => ['name' => 'حماة'],
                    'en' => ['name' => 'Hama']
                ]
            ],
            [
                'code' => 'HI',
                'translations' => [
                    'ar' => ['name' => 'حمص'],
                    'en' => ['name' => 'Homs']
                ]
            ],
            [
                'code' => 'LA',
                'translations' => [
                    'ar' => ['name' => 'اللاذقية'],
                    'en' => ['name' => 'Latakia']
                ]
            ],
            [
                'code' => 'DR',
                'translations' => [
                    'ar' => ['name' => 'درعا'],
                    'en' => ['name' => 'Daraa']
                ]
            ]
        ];

        foreach ($governorates as $governorate) {
            $gov = Governorate::create([
                'code' => $governorate['code'],
            ]);

            foreach ($governorate['translations'] as $locale => $trans) {
                GovernorateTranslation::create([
                    'governorate_id' => $gov->id,
                    'locale'         => $locale,
                    'name'           => $trans['name'],
                ]);
            }
        }
    }
}
