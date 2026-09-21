<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => preg_replace('/[\s()-]+/', '', $this->input('phone'))]);
        }
    }

    public function rules(): array
    {
        return [
            'checkout_token' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/\A(?:\+?880|0)1[3-9][0-9]{8}\z/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:250'],
            'area' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'zone' => ['required', Rule::in(array_keys(config('checkout.delivery')))],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment' => ['required', Rule::in(['cod'])],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a valid Bangladesh mobile number.', 'payment.in' => 'Only Cash on Delivery is currently supported.'];
    }
}
