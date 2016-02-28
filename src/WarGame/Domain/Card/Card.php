<?php

namespace WarGame\Domain\Card;

class Card
{
    private $rank;
    private $suit;

    public function __construct(Rank $rank, Suit $suit)
    {
        $this->rank = $rank;
        $this->suit = $suit;
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
}
