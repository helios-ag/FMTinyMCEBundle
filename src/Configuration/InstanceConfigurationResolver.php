<?php

declare(strict_types=1);

namespace FM\TinyMCEBundle\Configuration;

final class InstanceConfigurationResolver
{
    /** @param array<string, array<string, mixed>> $instances */
    public function __construct(private readonly array $instances)
    {
    }

    /** @return array<string, mixed> */
    public function resolve(string $name, ?bool $enabledOverride = null): array
    {
        if (!isset($this->instances[$name])) {
            $names = array_keys($this->instances);
            sort($names);

            throw new \InvalidArgumentException(sprintf(
                'Unknown TinyMCE instance "%s". Available instances: %s.',
                $name,
                implode(', ', $names),
            ));
        }

        $instance = $this->instances[$name];

        if (null !== $enabledOverride) {
            $instance['enabled'] = $enabledOverride;
        }

        return $instance;
    }
}
