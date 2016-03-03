<?php

namespace WarGame\Domain\Card;

use Assert\Assertion;

final class Deck
{
    /**
     * @var Card[]
     */
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

    /**
     * @return Card
     */
    public function pickFromTheTop()
    {
        Assertion::notEmpty($this->cards, 'You cannot pick a card from an empty deck.');

        return array_pop($this->cards);
    }

    public function addToTheTop(Card $card)
    {
        $this->cards[] = $card;
    }

    public function addToTheBottom(Card $card)
    {
        array_unshift($this->cards, $card);
    }

    public function isEmpty()
    {
        return empty($this->cards);
    }

    /**
     * @return Card[]
     */
    public function getCards()
    {
        return $this->cards;
    }
}
