<?php
declare(strict_types=1);

namespace MarsRoverKata\Domain\MarsRover;

use Webmozart\Assert\Assert;

class ComplexCommand
{
    private const ALLOWED_VALUES = ['F', 'B', 'L', 'R'];

    private function __construct(private array $primitiveCommands)
    {
    }

    public static function fromString(string $value): self
    {
        $primitiveCommands = [];

        $commands = str_split($value);
        foreach ($commands as $command) {
            Assert::inArray($command, self::ALLOWED_VALUES, "Value $command is not valid for complex command");
            $primitiveCommands[] = PrimitiveCommand::fromString($command);
        }
        return new self($primitiveCommands);
    }

    public function toString(): string
    {
        return array_reduce(
            $this->primitiveCommands,
            fn($value, PrimitiveCommand $primitiveCommand) => $value . $primitiveCommand->toString(),
            ''
        );
    }

    public function getPrimitiveCommands(): array
    {
        return $this->primitiveCommands;
    }
}
