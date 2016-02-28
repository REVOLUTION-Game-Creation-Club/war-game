<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Player\PlayerId;

class Round
{
    private $cardsFaceUp;
    private $cardsFaceDown;

    public function __construct()
    {
        $this->cardsFaceUp = [];
        $this->cardsFaceDown = [];
    }

    public function playerAddsCardFaceUp(PlayerId $playerId, Card $card)
    {
        Assertion::lessThan(count($this->cardsFaceUp), 2);

        $this->cardsFaceUp[$playerId->toString()] = $card;

        return $this;
    }

    public function resolveWinner()
    {
        Assertion::eq(count($this->cardsFaceUp), 2);

        list($player1, $player2) = array_keys($this->cardsFaceUp);

        if ($this->cardsFaceUp[$player1]->isEquals($this->cardsFaceUp[$player2])) {
            throw new War();
        }

        if ($this->cardsFaceUp[$player1]->isGreaterThan($this->cardsFaceUp[$player2])) {
            return PlayerId::fromString($player1);
        }

        return PlayerId::fromString($player2);
    }

    public function wonCards()
    {
        return array_values($this->cardsFaceUp) + array_values($this->cardsFaceDown);
    }
}
