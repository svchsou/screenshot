<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScreenshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'max:' . ((int) env('UPLOAD_MAX_MB', 12) * 1024), // KB
                'mimetypes:' . env('ALLOWED_MIMES', 'image/png,image/jpeg,image/webp'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please select an image to upload.',
            'image.file' => 'The upload must be a file.',
            'image.max' => 'The image is too large.',
            'image.mimetypes' => 'Only PNG, JPEG, and WebP images are allowed.',
        ];
    }
}
