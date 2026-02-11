<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorityRequest extends FormRequest
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
        $authorityId = $this->route('authority'); // Assuming the route parameter is named 'authority'
        
        return [
            'application_id' => 'required|uuid|exists:applications,id',
            'code' => 'required|string|max:255|unique:authorities,code,' . $authorityId,
            'name' => 'required|string|max:255',
            'menus' => 'array',
            'menus.*.id' => 'required|uuid|exists:menus,id',
            'menus.*.actions' => 'array',
            'menus.*.actions.*.action_id' => 'required|uuid|exists:actions,id',
            'menus.*.actions.*.value' => 'required|integer|in:0,1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'application_id.required' => 'Aplikasi harus dipilih.',
            'application_id.exists' => 'Aplikasi yang dipilih tidak valid.',
            'code.required' => 'Kode otoritas harus diisi.',
            'code.unique' => 'Kode otoritas sudah digunakan.',
            'name.required' => 'Nama otoritas harus diisi.',
            'menus.*.id.required' => 'ID menu harus ada.',
            'menus.*.id.exists' => 'Menu yang dipilih tidak valid.',
            'menus.*.actions.*.action_id.required' => 'ID aksi harus ada.',
            'menus.*.actions.*.action_id.exists' => 'Aksi yang dipilih tidak valid.',
            'menus.*.actions.*.value.required' => 'Nilai aksi harus ada.',
            'menus.*.actions.*.value.in' => 'Nilai aksi harus 0 atau 1.',
        ];
    }
}