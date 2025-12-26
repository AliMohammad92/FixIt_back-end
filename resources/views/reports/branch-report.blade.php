<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body class="bg-white p-4">
    <div class="max-w-5xl mx-auto border border-gray-200 shadow-sm rounded-lg overflow-hidden">
        <div class="bg-slate-800 p-6 text-white flex justify-between items-start">
            <div class="text-right">
                <h1 class="text-xl font-bold uppercase">تقرير نشاط الفرع</h1>
                <p class="text-slate-400 text-sm">Branch Activity Report</p>
            </div>
            <div class="text-left">
                <p class="text-blue-400 font-bold text-lg">{{ $ministryAr }}</p>
                <p class="text-slate-500 text-xs uppercase">{{ $ministryEn }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-2 gap-8 mb-8 bg-slate-50 p-4 rounded border-b-2 border-slate-200">
                <div class="text-right">
                    <span class="text-[10px] font-black text-slate-400 uppercase">الفرع والموقع / Branch & Location</span>
                    <p class="text-lg font-bold text-slate-700">{{ $branchAr }} - {{ $branch->governorate->translation('ar')->name }}</p>
                    <p class="text-xs text-slate-500">{{ $branchEn }} - {{ $branch->governorate->translation('en')->name }}</p>
                </div>
                <div class="text-left border-l border-slate-300 pl-6">
                    <span class="text-[10px] font-black text-slate-400 uppercase">المسؤول / Responsible</span>
                    <p class="text-lg font-bold text-slate-700">{{ $manager }}</p>
                </div>
            </div>

            <div class="grid grid-cols-5 gap-2 mb-10 text-center">
                <div class="bg-slate-100 p-2 rounded border-t-4 border-slate-400">
                    <span class="block text-[10px] font-bold text-slate-500">الإجمالي / Total</span>
                    <p class="text-xl font-black">{{ $total }}</p>
                </div>
                <div class="bg-blue-50 p-2 rounded border-t-4 border-blue-400">
                    <span class="block text-[10px] font-bold text-blue-600">جديد / New</span>
                    <p class="text-xl font-black text-blue-700">{{ $statuses['new_count'] }}</p>
                </div>
                <div class="bg-yellow-50 p-2 rounded border-t-4 border-yellow-400">
                    <span class="block text-[10px] font-bold text-yellow-600">قيد التنفيذ / In-Progress</span>
                    <p class="text-xl font-black text-yellow-700">{{ $statuses['progress_count'] }}</p>
                </div>
                <div class="bg-green-50 p-2 rounded border-t-4 border-green-400">
                    <span class="block text-[10px] font-bold text-green-600">مكتمل / Resolved</span>
                    <p class="text-xl font-black text-green-700">{{ $statuses['resolved_count'] }}</p>
                </div>
                <div class="bg-red-50 p-2 rounded border-t-4 border-red-400">
                    <span class="block text-[10px] font-bold text-red-600">مرفوض / Rejected</span>
                    <p class="text-xl font-black text-red-700">{{ $statuses['rejected_count'] }}</p>
                </div>
            </div>

            <div class="mb-8 border-r-4 border-blue-600 pr-4">
                <p class="text-sm font-bold text-slate-500">عدد الموظفين / Employees Count</p>
                <p class="text-2xl font-black">{{ $branch->employees->count() }}</p>
            </div>

            <h3 class="bg-slate-100 p-2 text-sm font-bold mb-4 flex justify-between">
                <span>النشاط الأخير بالفرع</span>
                <span class="text-xs font-normal">Recent Branch Activity</span>
            </h3>
            <table class="w-full text-right border">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase border-b">
                        <th class="p-2 border-l">التاريخ / Date</th>
                        <th class="p-2 border-l">النشاط / Activity</th>
                        <th class="p-2">الحالة / Status</th>
                    </tr>
                </thead>

                <tbody class="text-sm">
                    @foreach($activities as $activity)
                    @php
                    $statusType = $activity->event;

                    if (str_contains($activity->description, 'resolved')) $statusType = 'resolved';
                    if (str_contains($activity->description, 'rejected')) $statusType = 'rejected';

                    $config = match($statusType) {
                    'created' => ['color' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'ar' => 'جديد', 'en' => 'Created'],
                    'resolved' => ['color' => 'text-blue-700', 'bg' => 'bg-blue-50', 'ar' => 'تم الحل', 'en' => 'Resolved'],
                    'rejected' => ['color' => 'text-red-700', 'bg' => 'bg-red-50', 'ar' => 'مرفوض', 'en' => 'Rejected'],
                    'updated' => ['color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'ar' => 'تحديث', 'en' => 'Updated'],
                    'deleted' => ['color' => 'text-gray-700', 'bg' => 'bg-gray-100', 'ar' => 'حذف', 'en' => 'Deleted'],
                    default => ['color' => 'text-slate-600', 'bg' => 'bg-slate-50', 'ar' => 'نشاط', 'en' => 'Activity'],
                    };
                    @endphp
                    <tr>
                        <td class="p-2 border-l border-b font-mono text-[10px] text-slate-500">
                            {{ $activity->created_at->format('Y-m-d H:i A') }}
                        </td>

                        <td class="p-2 border-l border-b text-slate-700">
                            <div class="font-bold text-xs">{{ $activity->description }}</div>
                            @if(isset($activity->properties['attributes']['notes']))
                            <div class="text-[10px] text-slate-500 mt-1 italic">
                                Note: {{ $activity->properties['attributes']['notes'] }}
                            </div>
                            @endif
                        </td>

                        <td class="p-2 border-b text-center">
                            <div class="inline-block px-2 py-1 rounded {{ $config['bg'] }} {{ $config['color'] }}">
                                <div class="text-[10px] font-black leading-tight">{{ $config['ar'] }}</div>
                                <div class="text-[8px] font-bold uppercase leading-tight opacity-70">{{ $config['en'] }}</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>