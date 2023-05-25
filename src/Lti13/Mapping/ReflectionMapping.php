<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

use Cerpus\EdlibResourceKit\Lti13\Attribute\Claim;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionProperty;
use function preg_replace;
use function preg_replace_callback;
use function str_starts_with;
use function strtolower;

final class ReflectionMapping implements MappingInterface
{
    /**
     * @var array<string, Field>
     */
    private array $cachedFields = [];

    /**
     * Get a list of mapped fields on LTI objects from their classes' constants,
     * properties, and getter methods that have been adorned with the
     * {@link Claim} attribute.
     *
     * If no name was set on the claim, the name will be inferred from the name
     * of the symbol. For this to work, the following rules have to be met:
     *
     * - Constant names are CAPITAL_SNAKE_CASE.
     * - Properties are pascalCase or snake_case. The properties can be
     *   non-public, in which case a public getter method matching the property
     *   name (optionally with `is`/`get` prefix) must exist.
     * - Method names are pascalCase or snake_case, optionally with `is` or
     *   `get` prefixes. Methods beginning with underscores are ignored.
     *
     * The inferred names will then be their lower_case_snake_case equivalents.
     *
     * @return Field[]
     */
    public function getFields(object $object): array
    {
        if (!isset($this->cachedFields[$object::class])) {
            $r = new ReflectionClass($object);
            $propertyMappingInfo = self::readMappingInfoFromProperties($r);

            $this->cachedFields[$object::class] = array_filter([
                ...self::readMappingInfoFromConstants($r),
                ...$propertyMappingInfo,
                ...self::readMappingInfoFromMethods($r, $propertyMappingInfo),
            ], fn(Field $field) => !$field->getReader()->isPrivate());
        }

        return $this->cachedFields[$object::class];
    }

    /**
     * @return array<string, Field>
     */
    private static function readMappingInfoFromConstants(ReflectionClass $r): array
    {
        $mappingInfo = [];
        do {
            foreach ($r->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC) as $rConstant) {
                $key = strtolower($rConstant->getName());
                $claim = self::readClaim($rConstant);

                if ($claim) {
                    $mappingInfo[$key] = new Field(
                        claim: $claim->name ?? $key,
                        reader: new Reader(
                            name: $rConstant->getName(),
                            type: ReaderType::Constant,
                        ),
                    );
                }
            }

            $r = $r->getParentClass();
        } while ($r);

        return $mappingInfo;
    }

    /**
     * @return array<string, Field>
     */
    private static function readMappingInfoFromProperties(ReflectionClass $r): array
    {
        $mappingInfo = [];
        do {
            foreach ($r->getProperties(~ReflectionProperty::IS_STATIC) as $rProperty) {
                $key = self::pascalCaseToKey($rProperty->getName());

                if (isset($mappingInfo[$key])) {
                    // property is overridden
                    continue;
                }

                $claim = self::readClaim($rProperty);
                if ($claim) {
                    $mappingInfo[$key] = new Field(
                        claim: $claim->name ?? $key,
                        reader: new Reader(
                            name: $rProperty->getName(),
                            type: ReaderType::Property,
                            private: !$rProperty->isPublic(),
                        ),
                    );
                }
            }

            $r = $r->getParentClass();
        } while ($r);

        return $mappingInfo;
    }

    /**
     * @param array<string, Field> $propertyMappingInfo
     * @return array<string, Field>
     */
    private static function readMappingInfoFromMethods(
        ReflectionClass $r,
        array $propertyMappingInfo,
    ): array {
        $mappingInfo = [];
        do {
            foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $rMethod) {
                if (str_starts_with($rMethod->getName(), '_')) {
                    // skip magic & faux-private methods
                    continue;
                }

                $key = self::methodNameToKey($rMethod->getName());
                $claim = self::readClaim($rMethod);

                if ($claim) {
                    $mappingInfo[$key] = new Field(
                        claim: $claim->name ?? $key,
                        reader: new Reader(
                            name: $rMethod->getName(),
                            type: ReaderType::Getter,
                        ),
                    );
                } elseif (
                    isset($propertyMappingInfo[$key]) &&
                    $propertyMappingInfo[$key]->getReader()->getType() === ReaderType::Property
                ) {
                    // this is a getter for a private property
                    $mappingInfo[$key] = $propertyMappingInfo[$key]
                        ->withReader(new Reader(
                            name: $rMethod->getName(),
                            type: ReaderType::Getter,
                        ));
                }
            }

            $r = $r->getParentClass();
        } while ($r);

        return $mappingInfo;
    }

    private static function readClaim(
        ReflectionClassConstant|ReflectionMethod|ReflectionProperty $r,
    ): Claim|null {
        return ($r->getAttributes(Claim::class)[0] ?? null)?->newInstance();
    }

    /**
     * @todo This naming sucks
     */
    private static function methodNameToKey(string $name): string
    {
        return preg_replace('/^(get|is)_/', '', self::pascalCaseToKey($name));
    }

    /**
     * @todo This naming sucks
     */
    private static function pascalCaseToKey(string $name): string
    {
        return strtolower(preg_replace_callback(
            '/(?<!^)([A-Z]+)/',
            fn(array $matches) => '_' . $matches[1],
            $name,
        ));
    }
}
