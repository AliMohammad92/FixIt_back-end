<?php

namespace App\Services;

use App\Http\Requests\BaseUserRequest;
use App\Models\Citizen;
use App\Models\Complaint;
use App\Models\User;
use App\Models\UserOTP;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManagerService
{
    public function storeFile($model, array|UploadedFile $files, $folderPath, $relationName = 'media', ?callable $typeResolver = null)
    {
        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $file->storeAs($folderPath, $fileName, 'public');
            $model->{$relationName}()->create([
                'path' => "$folderPath/$fileName",
                'type' => $typeResolver ? $typeResolver($file) : 'file'
            ]);
        }
    }

    public function deleteFile($model, $fileId, $relationName = 'media')
    {
        $fileRecord = $model->{$relationName}()->find($fileId);
        if (!$fileRecord) {
            return false;
        }

        if (Storage::disk('public')->exists($fileRecord->path)) {
            Storage::disk('public')->delete($fileRecord->path);
        }

        $fileRecord->delete();

        return true;
    }

    public function detectFileType($file)
    {
        return in_array(strtolower($file->getClientOriginalExtension()), ['pdf', 'doc', 'docx'])
            ? 'file'
            : 'img';
    }
}
