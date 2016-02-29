<?php

namespace WarGame\Domain\Card;

use Assert\Assertion;

class Deck
{
    private $cards;

    public function __construct(array $cards = [])
    {
        $this->cards = $cards;
    }

    public static function frenchDeck()
    {
        $cards = [];

        foreach (Suit::getSuits() as $suit) {
            foreach (range(Rank::MIN_WEIGHT, Rank::MAX_WEIGHT) as $rank) {
                $card = new Card(new Rank($rank), Suit::$suit());

                $cards[] = $card;
            }
        }

        return new self($cards);
    }

    public function shuffle()
    {
        shuffle($this->cards);
    }

    public function getNbOfCards()
    {
        return count($this->cards);
    }

    public function pick()
    {
        Assertion::notEmpty($this->cards);

        return array_shift($this->cards);
    }

    public function add(Card $card)
    {
        $this->cards[] = $card;
    }

    public function isEmpty()
    {
        return empty($this->cards);
    }
}
