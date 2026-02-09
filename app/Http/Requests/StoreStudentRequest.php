<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'StudentYear' => 'nullable|integer',
            'FirstName' => 'required|string|max:255',
            'MiddleName' => 'nullable|string|max:255',
            'LastName' => 'required|string|max:255',
            'Email' => 'nullable|email',
            'PhoneNumber' => 'nullable|string|max:50',
            'Gender' => 'nullable|string|in:Male,Female,Other',
            'BirthDate' => 'nullable|date',
            'Address' => 'nullable|string',
            'CurriculumID' => 'nullable|integer',
            'YearLevel' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->YearLevel === null) {
            $this->merge([
                'YearLevel' => 0,
            ]);
        }
    }
}
