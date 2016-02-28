<?php

namespace WarGame\Domain\Card;

final class Suit
{
    const CLOVERS = 'clovers';
    const TILES = 'tiles';
    const HEARTS = 'hearts';
    const PIKES = 'pikes';

    private $name;

    private function __construct($name)
    {
        $this->name = $name;
    }

    public static function clovers()
    {
        return new self(self::CLOVERS);
    }

    public static function tiles()
    {
        return new self(self::TILES);
    }

    public static function hearts()
    {
        return new self(self::HEARTS);
    }

    public static function pikes()
    {
        return new self(self::PIKES);
    }

    public function getName()
    {
        return $this->name;
    }

    public static function getSuits()
    {
        return [self::CLOVERS, self::TILES, self::HEARTS, self::PIKES];
    }
}
