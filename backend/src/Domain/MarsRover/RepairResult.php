<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class RepairResult
{
    private function __construct(private string $result, private ?string $failure)
    {
    }

    public static function ok(): self
    {
        return new self('ok', null);
    }

    public static function ko(string $failure): self
    {
        return new self('ko', $failure);
    }

    public function isOk(): bool
    {
        return $this->result === 'ok';
    }

    public function failure(): ?string
    {
        return $this->failure;
    }
}
