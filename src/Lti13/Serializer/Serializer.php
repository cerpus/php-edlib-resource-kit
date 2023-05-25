<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Serializer;

use Cerpus\EdlibResourceKit\Lti13\Mapping\ReaderType;
use Cerpus\EdlibResourceKit\Lti13\Mapping\MappingInterface;
use Cerpus\EdlibResourceKit\Lti13\Mapping\ReflectionMapping;
use StringBackedEnum;
use function constant;
use function is_object;

final readonly class Serializer implements SerializerInterface
{
    public function __construct(
        private MappingInterface $mapping = new ReflectionMapping(),
    ) {
    }

    public function serialize(object $message): array
    {
        $serialized = [];
        foreach ($this->mapping->getFields($message) as $field) {
            $name = $field->getReader()->getName();
            $value = match ($field->getReader()->getType()) {
                ReaderType::Constant => constant($message::class . '::' . $name),
                ReaderType::Getter => $message->{$name}(),
                ReaderType::Property => $message->$name,
            };

            $value = $this->serializeValue($value);

            if ($value === null) {
                // don't include NULL values
                continue;
            }

            $serialized[$field->getClaim()] = $value;
        }

        return $serialized;
    }

    private function serializeValue(mixed $value): mixed
    {
        if ($value instanceof StringBackedEnum) {
            $value = $value->value;
        } elseif (is_object($value)) {
            $value = $this->serialize($value);
        } elseif (is_array($value)) {
            $value = array_map($this->serializeValue(...), $value);
        }

        return $value;
    }
}
