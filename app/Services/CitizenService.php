<?php

namespace App\Services;

use App\DAO\CitizenDAO;
use App\Http\Resources\MinistryResource;
use App\Models\Citizen;
use Illuminate\Support\Facades\Cache;

class CitizenService
{
    public function __construct(
        protected CitizenDAO $citizenDAO,
        protected FileManagerService $fileManagerService
    ) {}

    public function read()
    {
        $cacheKey = "citizens";
        return Cache::remember($cacheKey, 1800, function () {
            return $this->citizenDAO->read();
        });
    }

    public function readOne($id)
    {
        $cacheKey = "citizen {$id}";
        return Cache::remember($cacheKey, 1800, function () use ($id) {
            return $this->citizenDAO->findById($id);
        });
    }

    public function uploadProfileImage($img, $citizen)
    {
        if (isset($img)) {
            $this->fileManagerService->storeFile(
                $citizen,
                $img,
                "citizens",
                'image',
                fn() => 'img'
            );
        }
        return $citizen;
    }

    public function deleteProfileImage($citizen)
    {
        if (!$citizen->image) {
            return false;
        }

        return $this->fileManagerService->deleteFile($citizen, $citizen->image->id, 'image');
    }

    public function updateProfileImage($img, $citizen)
    {
        $status = $this->deleteProfileImage($citizen);
        if ($status) {
        }
        return $this->uploadProfileImage($img, $citizen) ? true : false;
    }
}
