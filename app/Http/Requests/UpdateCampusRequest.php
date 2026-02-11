<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampusRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'CampusCode' => 'required|string|max:50|unique:campus,CampusCode,'.$id.',CampusID',
            'CampusName' => 'required|string|max:255',
            'Location' => 'nullable|string|max:255',
            'CampusHead' => 'nullable|integer',
            'OfficeCode' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }
}
