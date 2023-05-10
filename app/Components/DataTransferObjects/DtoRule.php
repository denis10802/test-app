<?php

namespace App\Components\DataTransferObjects;

#[\Attribute] class DtoRule
{
    public function __construct(
        public ?int   $min = null,
        public ?int   $max = null,
        public ?string $date_format = null,
    )
    {
    }

}
