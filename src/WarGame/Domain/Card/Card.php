<?php

namespace WarGame\Domain\Card;

final class Card
{
    private $rank;
    private $suit;

    public function __construct(Rank $rank, Suit $suit)
    {
        $this->rank = $rank;
        $this->suit = $suit;
    }

    public static function random()
    {
        $suits = Suit::getSuits();
        $randomSuit = $suits[array_rand($suits, 1)];

        return new self(
            new Rank(rand(Rank::MIN_WEIGHT, Rank::MAX_WEIGHT)),
            Suit::$randomSuit()
        );
    }

    public function isGreaterThan(Card $card)
    {
        return $this->getWeight() > $card->getWeight();
    }

    public function isSmallerThan(Card $card)
    {
        return $this->getWeight() < $card->getWeight();
    }

    public function isEquals(Card $card)
    {
        return $this->getWeight() === $card->getWeight();
    }

    public function getWeight()
    {
        return $this->rank->getWeight();
    }

    public function getSuit()
    {
        return $this->suit;
    }

    public function toString()
    {
        return sprintf('%s %s', $this->rank->toString(), $this->suit->toString());
    }
}
