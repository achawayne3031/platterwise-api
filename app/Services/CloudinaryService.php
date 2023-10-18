<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use App\Models\CloudinarySettings;
use App\Helpers\DBHelpers;
use App\Helpers\ResponseHelper;

class CloudinaryService
{
    public $name;
    public $api_key;
    public $secret_key;
    public $url;

    public function __construct()
    {
        $data = DBHelpers::first_data(CloudinarySettings::class);
        $this->name = $data->name;
        $this->api_key = $data->api_key;
        $this->secret_key = $data->secret_key;
        $this->url = $data->cloudinary_url;
        Configuration::instance($this->url);
    }

    public function image_upload($folder, $image)
    {
        $upload = new UploadApi();
        $result = $upload->upload($image->getRealPath(), [
            'folder' => 'test',
            'discard_original_filename' => true,
            'transformation' => [
                'fetch_format' => 'auto',
                'quality' => 'auto:eco',
            ],
        ]);

        if ($result && $result !== null) {
            return $result['secure_url'];
        } else {
            return false;
        }
    }
}

?>
