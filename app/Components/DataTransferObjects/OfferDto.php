<?php

namespace App\Components\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

final class OfferDto implements Arrayable
{
    public function __construct(
        #[DtoRule(max: 255)]
        public string  $title,
        #[DtoRule(min: 1000, max: 999999999)]
        public int     $price,
        #[DtoRule(max: 4096)]
        public ?string $description,
        public bool    $isActive,
        #[DtoRule(date_format: 'Y-m-d H:i:s')]
        public string  $publishAt,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'isActive' => $this->isActive,
            'publishAt' => $this->publishAt,
        ];
    }
}
