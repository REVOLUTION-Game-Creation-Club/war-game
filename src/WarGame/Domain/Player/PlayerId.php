<?php

namespace WarGame\Domain\Player;

use Ramsey\Uuid\Uuid;

class PlayerId
{
    /**
     * @var Uuid
     */
    private $uuid;

    public static function generate()
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString($userId)
    {
        return new self(Uuid::fromString($userId));
    }

    private function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString()
    {
        return $this->uuid->toString();
    }

    public function sameValueAs(PlayerId $other)
    {
        return $this->toString() === $other->toString();
    }
}