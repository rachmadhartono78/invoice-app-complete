<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organizationId = $this->route('organization'); // Assuming the route parameter is named 'organization'
        
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:organizations,code,' . $organizationId,
            'type' => 'required|string|in:headquarters,branch,division,department,unit',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'organization_id' => 'nullable|uuid|exists:organizations,id|not_in:' . $organizationId,
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama organisasi harus diisi.',
            'code.required' => 'Kode organisasi harus diisi.',
            'code.unique' => 'Kode organisasi sudah digunakan.',
            'type.required' => 'Tipe organisasi harus dipilih.',
            'type.in' => 'Tipe organisasi tidak valid.',
            'city.required' => 'Kota harus diisi.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'organization_id.exists' => 'Organisasi induk tidak valid.',
            'organization_id.not_in' => 'Organisasi tidak boleh menjadi induk dari dirinya sendiri.',
        ];
    }
}