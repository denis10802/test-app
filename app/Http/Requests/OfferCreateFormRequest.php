<?php

namespace App\Http\Requests;

use App\Components\DataTransferObjects\DtoRule;
use App\Components\DataTransferObjects\OfferDto;
use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use ReflectionProperty;

class OfferCreateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];
        $dto = new OfferDto('', 0, null, false, '');

        // create instance to use annotations
        $reflection = new ReflectionClass($dto);
        foreach ($reflection->getProperties() as $property) {
            $rules[$property->getName()] = $this->getRulesForProperty($property);
        }
        return $rules;
    }

    private function getRulesForProperty(ReflectionProperty $property): array
    {
        $rules = [];
        $rules[] = $property->hasType() ? $property->getType()->getName() : 'string';
        $rules[] = $this->getValidationRule('max', $property);
        $rules[] = $this->getValidationRule('min', $property);
        $rules[] = $this->getValidationRule('date_format', $property);
        $rules[] = $property->getDocComment() ? 'required' : 'nullable';
        return array_filter($rules);
    }

    private function getValidationRule($rule, $property): string
    {
        $dtoRule = $this->getDtoRule($property);
        return $dtoRule && property_exists($dtoRule, $rule)
            ? "{$rule}:{$dtoRule->{$rule}}"
            : '';
    }

    private function getDtoRule($property): ?DtoRule
    {
        $docComment = $property->getDocComment();
        if ($docComment) {
            $matches = [];
            preg_match('/@DtoRule\((.*?)\)/', $docComment, $matches);
            if (!empty($matches)) {
                $arguments = explode(',', $matches[1]);
                $args = [];
                foreach ($arguments as $argument) {
                    [$name, $value] = array_map('trim', explode(':', $argument));
                    $args[$name] = $value;
                }
                return new DtoRule(...array_values($args));
            }
        }
        return null;
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
