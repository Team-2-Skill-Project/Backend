<?php
namespace App\Http\Requests\cv;

use Illuminate\Foundation\Http\FormRequest;

class UploadCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cv' => [
                'required',
                'file',
                'mimes:pdf,docx',
                'max:5120',
                function ($attribute, $value, $fail) {
                    if ($value->getSize() === 0) {
                        $fail('The uploaded file is empty and contains no data.');
                    }
                },
            ],
        ];
    }
}
