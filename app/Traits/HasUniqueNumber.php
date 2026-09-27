<?php

namespace App\Traits;

use Illuminate\Database\UniqueConstraintViolationException;

trait HasUniqueNumber
{
    /**
     * Create a model whose number column (order_number, quotation_number, ...) is generated
     * from a count of existing rows. Two requests can compute the same "next" number at the
     * same instant, so the insert can fail on the column's unique constraint — catch that and
     * regenerate rather than letting it surface as a 500 in production.
     *
     * $generator receives the attempt index (0, 1, 2, ...) so each retry asks for a number
     * further past the collision instead of recomputing the identical value.
     *
     * @param  callable(int $attempt): string  $generator
     */
    public static function createWithUniqueNumber(string $numberField, callable $generator, array $attributes, int $maxAttempts = 5): static
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                return static::create([$numberField => $generator($attempt)] + $attributes);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === $maxAttempts - 1) {
                    throw $e;
                }
            }
        }

        throw new \LogicException('Unreachable: ' . static::class . '::createWithUniqueNumber() loop exited without returning or throwing.');
    }
}
