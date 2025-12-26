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
    </style>
</head>

<body class="bg-white p-4">
    <div class="max-w-5xl mx-auto border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-8 border-b-4 border-slate-800 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black text-slate-800">ملخص الوزارة التنفيذي</h1>
                <h2 class="text-sm text-slate-500 uppercase tracking-widest">Ministry Executive Summary</h2>
            </div>
            <div class="text-left font-bold text-slate-400 text-xs">{{ $ministryAr }} / {{ $ministryEn }}</div>
        </div>

        <div class="p-8">
            <div class="mb-10 text-center bg-slate-900 text-white p-6 rounded-xl">
                <p class="text-blue-400 text-xs font-bold uppercase mb-2">إجمالي الشكاوى بالوزارة / Total Ministry Complaints</p>
                <h1 class="text-5xl font-black">{{ $total }}</h1>
            </div>

            <table class="w-full text-right">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase border-b-2">
                        <th class="pb-3">الفرع / Branch</th>
                        <th class="pb-3 text-center">جديد / New</th>
                        <th class="pb-3 text-center">قيد التنفيذ / Progress</th>
                        <th class="pb-3 text-center">مكتمل / Resolved</th>
                        <th class="pb-3 text-center">مرفوض / Rejected</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @foreach($branches as $b)
                    <tr>
                        <td class="py-4">
                            <p class="font-bold">{{ $b->name_ar }}</p>
                            <p class="text-[10px] text-slate-400 uppercase">{{ $b->name_en }}</p>
                        </td>
                        <td class="text-center font-bold text-blue-600 bg-blue-50">{{ $b->new }}</td>
                        <td class="text-center font-bold text-yellow-600">{{ $b->progress }}</td>
                        <td class="text-center font-bold text-green-600">{{ $b->resolved }}</td>
                        <td class="text-center font-bold text-red-600">{{ $b->rejected }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 text-center border-t">
            <p class="text-[10px] text-slate-400 font-bold uppercase">CONFIDENTIAL - للموظفين المصرح لهم فقط</p>
        </div>
    </div>
</body>

</html>