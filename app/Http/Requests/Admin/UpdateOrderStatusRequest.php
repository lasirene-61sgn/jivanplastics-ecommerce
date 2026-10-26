<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust according to your admin auth check (e.g., auth()->guard('admin')->check())
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,processing,completed,cancelled,accepted,rejected',
            'rejection_reason' => 'nullable|string|max:500', // optional: in case you want to capture reason
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please specify an order status.',
            'status.in'       => 'The selected status is invalid.',
        ];
    }
}