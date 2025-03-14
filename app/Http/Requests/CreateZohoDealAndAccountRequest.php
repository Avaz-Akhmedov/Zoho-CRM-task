<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateZohoDealAndAccountRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'deal_name' => ['required', 'string'],
            'deal_stage' => ['required', 'string',Rule::in(self::dealStages())],
            'account_name' => ['required', 'string'],
            'account_website' => ['nullable', 'url'],
            'account_phone' => ['nullable', 'string'],
        ];
    }

    public static function dealStages (): array
    {
        return [
            'Qualification',
            'Needs Analysis',
            'Value Proposition',
            'Identify Decision Maker',
            'Proposal/Price Quote',
            'Negotiation/Review',
        ];
    }
}
