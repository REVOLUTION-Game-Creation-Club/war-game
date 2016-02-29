<?php

namespace WarGame\Domain\Player;

use Assert\Assertion;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Deck;

class Player
{
    /**
     * @var PlayerId
     */
    private $playerId;

    private $name;

    /**
     * @var Deck
     */
    private $deck;

    private $isReady;

    private function __construct(PlayerId $playerId, $name, Deck $deck)
    {
        $this->playerId = $playerId;
        $this->name = $name;
        $this->deck = $deck;
    }

    public static function named($name)
    {
        return new self(PlayerId::generate(), $name, new Deck());
    }

    public function getName()
    {
        return $this->name;
    }

    public function isStillHaveCards()
    {
        return !empty($this->deck);
    }

    public function receiveCard(Card $card)
    {
        $this->deck->add($card);
    }

    public function wins(array $cards)
    {
        foreach ($cards as $card) {
            $this->deck->add($card);
        }
    }

    public function getNbOfCards()
    {
        return $this->deck->getNbOfCards();
    }

    public function readyToStart()
    {
        $this->isReady = true;
    }

    public function isReady()
    {
        return true === $this->isReady;
    }

    /**
     * @return PlayerId
     */
    public function getId()
    {
        return $this->playerId;
    }

    public function putOneCardUp()
    {
        Assertion::false($this->deck->isEmpty());

        return $this->deck->pick();
    }

    public function putCardsFaceDown($nbOfCards)
    {
        $cardsFaceDown = [];

        if ($this->deck->getNbOfCards() < $nbOfCards) {
            throw new NotEnoughCards();
        }

        while ($nbOfCards-- > 0) {
            $cardsFaceDown[] = $this->deck->pick();
        }

        return $cardsFaceDown;
    }
}
