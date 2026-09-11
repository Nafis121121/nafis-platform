<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[0-9+()\-\s]{7,32}$/'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'max:0'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'شماره تماس واردشده معتبر نیست.',
            'message.min' => 'لطفاً توضیح کوتاه‌تری از ۱۰ کاراکتر وارد نکنید.',
            'website.max' => 'ارسال پیام نامعتبر بود.',
        ];
    }
}
