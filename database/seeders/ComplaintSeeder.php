<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Complaint;
use App\Models\Citizen;
use App\Models\Governorate;
use App\Models\MinistryBranch;
use Faker\Factory as Faker;

class ComplaintSeeder extends Seeder
{
    public function run()
    {
        $fakerEn = Faker::create('en_US');
        $fakerAr = Faker::create('ar_SA');

        $citizens = Citizen::all();
        $branches = MinistryBranch::with('ministry', 'translations')->get();

        if ($citizens->isEmpty() || $branches->isEmpty()) {
            $this->command->error("Ensure Citizens and Branches are seeded first!");
            return;
        }

        foreach ($branches as $branch) {
            // Get the branch name in Arabic to determine the type of complaints
            $branchNameAr = $branch->translations->where('locale', 'ar')->first()?->name ?? '';

            // Generate at least 5 complaints per branch
            for ($i = 0; $i < 5; $i++) {
                $citizen = $citizens->random();
                $governorateId = $branch->governorate_id;

                // Determine realistic content based on branch name
                $content = $this->getRealisticContent($branchNameAr);

                $reference_number = strtoupper(
                    $branch->ministry->abbreviation . '_' . Str::random(8)
                );

                $complaint = Complaint::create([
                    'reference_number'   => $reference_number,
                    'type'               => $fakerEn->randomElement(['service', 'infrastructure', 'administrative']),
                    'description'        => $content['ar'],
                    'status'             => $fakerEn->randomElement(['new', 'in_progress', 'resolved', 'rejected']),
                    'governorate_id'     => $governorateId,
                    'city_name'          => $fakerAr->city,
                    'street_name'        => $fakerAr->streetAddress,
                    'citizen_id'         => $citizen->id,
                    'ministry_branch_id' => $branch->id,
                    'ministry_id'        => $branch->ministry_id,
                ]);

                // Occasionally add an English version for variety (20% chance)
                if (rand(1, 100) <= 20) {
                    $complaint->update(['description' => $content['en']]);
                }

                $this->addDummyMedia($complaint);
            }
        }
    }

    private function getRealisticContent($branchName): array
    {
        return match (true) {
            str_contains($branchName, 'صحة') || str_contains($branchName, 'مشفى') => [
                'ar' => "هناك نقص حاد في الأدوية الأساسية في الصيدلية التابعة للمركز، كما أن المواعيد بعيدة جداً.",
                'en' => "There is a severe shortage of essential medicines in the center's pharmacy, and appointments are too far away."
            ],
            str_contains($branchName, 'تربية') || str_contains($branchName, 'تعليم') => [
                'ar' => "المدرسة تعاني من نقص في الكتب المدرسية لطلاب المرحلة الإعدادية، ونرجو صيانة دورات المياه.",
                'en' => "The school suffers from a shortage of textbooks for middle school students, and we request maintenance for the restrooms."
            ],
            str_contains($branchName, 'هجرة') || str_contains($branchName, 'جوازات') => [
                'ar' => "تأخر إصدار جواز السفر الخاص بي لأكثر من شهرين رغم دفع الرسوم المستعجلة.",
                'en' => "My passport issuance has been delayed for over two months despite paying the urgent fees."
            ],
            str_contains($branchName, 'كهرباء') || str_contains($branchName, 'طاقة') => [
                'ar' => "انقطاع التيار الكهربائي المتكرر خارج أوقات التقنين يؤدي لتلف الأجهزة الكهربائية في الحي.",
                'en' => "Frequent power outages outside of rationing hours are causing damage to electrical appliances in the neighborhood."
            ],
            str_contains($branchName, 'اتصالات') || str_contains($branchName, 'إنترنت') => [
                'ar' => "سرعة الإنترنت بطيئة جداً ولا تصل للسرعة المتعاقد عليها، وهناك أعطال دائمة في الهاتف الأرضي.",
                'en' => "The internet speed is very slow and does not reach the contracted speed, and there are permanent faults in the landline."
            ],
            str_contains($branchName, 'مالية') || str_contains($branchName, 'ضرائب') => [
                'ar' => "هناك تعقيد كبير في إجراءات براءة الذمة المالية واستغلال من قبل بعض الموظفين.",
                'en' => "There is a great complexity in the financial clearance procedures and exploitation by some employees."
            ],
            str_contains($branchName, 'نفط') || str_contains($branchName, 'محروقات') => [
                'ar' => "لم تصل رسالة الغاز المنزلي منذ أكثر من 90 يوماً، نرجو معالجة آلية التوزيع.",
                'en' => "The domestic gas message hasn't arrived for over 90 days, please address the distribution mechanism."
            ],
            default => [
                'ar' => "تأخر في إنجاز المعاملة الإدارية وسوء معاملة من قبل الموظف المسؤول في قسم الاستقبال.",
                'en' => "Delay in completing the administrative transaction and mistreatment by the responsible employee in the reception department."
            ],
        };
    }

    protected function addDummyMedia(Complaint $complaint)
    {
        $folder = "complaints/{$complaint->id}";
        Storage::disk('public')->makeDirectory($folder);
        $yourImagePath = database_path('seeders/files/photo_2025-09-18_20-52-43.jpg');

        if (file_exists($yourImagePath)) {
            $imageName = "image_{$complaint->id}.jpg";
            Storage::disk('public')->putFileAs($folder, new \Illuminate\Http\File($yourImagePath), $imageName);
            $complaint->media()->create(['path' => "$folder/$imageName", 'type' => 'img']);
        }
    }
}
