<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'agent_id' => ['required', 'exists:users,id,role,agent'],
        ];
    }

    public function messages(): array
    {
        return [
            'agent_id.required' => 'The agent field is required.',
            'agent_id.exists' => 'The selected agent does not exist.',
        ];
    }
}
