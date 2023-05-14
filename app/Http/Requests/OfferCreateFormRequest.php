<?php

namespace App\Http\Requests;

use App\Components\DataTransferObjects\OfferDto;
use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use ReflectionProperty;

class OfferCreateFormRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [];
        $dto = new OfferDto('', 0, null, false, '');

        $reflection = new ReflectionClass($dto);
        foreach ($reflection->getProperties() as $property) {
            $rules[$property->getName()] = $this->getRulesForProperty($property);
        }

        return $rules;
    }

    private function getRulesForProperty(ReflectionProperty $property): array
    {
        $rules = [];
        $rules[] = $property->getName() == 'description' ? 'nullable' : 'required';
        $rules[] = $property->hasType() ? $property->getType()->getName() : 'string';
        $rules[] = $this->getValidationRule($property);

        return $rules;
    }

    private function getValidationRule(ReflectionProperty $property): string
    {
        $strRule = '';
        foreach ($property->getAttributes() as $attribute) {
            $getStrFromArray = fn(string $k, int|string $v): string => "$k:$v";
            $strRule = implode(',', array: array_map($getStrFromArray, array_keys($attribute->getArguments()), array_values($attribute->getArguments())));
        }

        return $strRule;
    }

    public function toDto(): OfferDto
    {
        return new OfferDto(
            $this->input('title'),
            $this->input('price'),
            $this->input('description'),
            $this->input('isActive'),
            $this->input('publishAt')
        );
    }
}
