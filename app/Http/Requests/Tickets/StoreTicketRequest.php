<?php

declare(strict_types=1);

namespace App\Http\Requests\Tickets;

use App\Enums\TicketPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in([
                TicketPriority::LOW,
                TicketPriority::MEDIUM,
                TicketPriority::HIGH,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Please enter a subject.',
            'subject.max' => 'Subject cannot be longer than 255 characters.',
            'description.required' => 'Please enter a description.',
            'priority.required' => 'Please select a priority.',
        ];
    }
}
