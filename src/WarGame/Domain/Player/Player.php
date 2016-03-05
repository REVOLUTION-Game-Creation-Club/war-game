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

    private function __construct(PlayerId $playerId, $name, Deck $deck)
    {
        $this->playerId = $playerId;
        $this->name = $name;
        $this->deck = $deck;
    }

    public static function named($name, PlayerId $playerId)
    {
        return new self($playerId, $name, new Deck());
    }

    public function getName()
    {
        return $this->name;
    }

    public function isOutOfCards()
    {
        return $this->deck->isEmpty();
    }

    public function receiveCard(Card $card)
    {
        $this->deck->addToTheTop($card);
    }

    /**
     * @param Card[] $wonCards Won cards
     */
    public function wins(array $wonCards)
    {
        // Prevents some infinite loops
        shuffle($wonCards);

        foreach ($wonCards as $card) {
            $this->deck->addToTheBottom($card);
        }
    }

    public function getNbOfCards()
    {
        return $this->deck->getNbOfCards();
    }

    /**
     * @return PlayerId
     */
    public function getId()
    {
        return $this->playerId;
    }

    public function putOneCard()
    {
        if ($this->deck->isEmpty()) {
            throw new NotEnoughCards();
        }

        return $this->deck->pickFromTheTop();
    }

    /**
     * @return Deck
     */
    public function getDeck()
    {
        return $this->deck;
    }
}
