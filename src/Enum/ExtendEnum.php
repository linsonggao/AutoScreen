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

    /**
     * @param null|int|string $key
     */
    public static function keyValues($key = null): array|string
    {
        $result = [];
        foreach (self::cases() as $item) {
            $result[$item->value] = $item->getDescription();
        }
        if (null !== $key) {
            return $result[$key] ?? '';
        } else {
            return $result;
        }
    }
}
