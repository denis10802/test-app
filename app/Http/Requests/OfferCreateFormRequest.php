<?php

namespace App\Http\Requests;

use App\Components\DataTransferObjects\OfferDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OfferCreateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:1000', 'max:999999999'],
            'description' => ['nullable', 'string', 'max:4096'],
            'isActive' => ['required', 'boolean'],
            'publishAt' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    public function toDto(
        string $title,
        int $price,
        string $description,
        bool $isActive,
        string $publishAt,
    ): OfferDto
    {
       return new OfferDto($title, $price, $description, $isActive, $publishAt);
    }
}
