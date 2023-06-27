<?php

namespace Lsg\AutoScreen\Enum;

trait ExtendEnum
{
    public function getDescription(): string
    {
        $ref = new \ReflectionEnumUnitCase(self::class, $this->name);
        $attributes = $ref->getAttributes();
        foreach ($attributes as $attribute) {
            $args = $attribute->getArguments();

            return $args[0];
        }

        return '';
    }

    public static function keyValues(): array
    {
        $result = [];
        foreach (self::cases() as $item) {
            $result[$item->value] = $item->getDescription();
        }

        return $result;
    }
}
