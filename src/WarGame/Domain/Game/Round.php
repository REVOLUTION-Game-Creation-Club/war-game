<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Player\PlayerId;

class Round
{
    /**
     * @var Card[]
     */
    private $cardsFaceUp;

    /**
     * @var Card[]
     */
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

    public function playerAddsCardsFaceDown($cards)
    {
        foreach ($cards as $card) {
            $this->cardsFaceDown[] = $card;
        }

        return $this;
    }

    public function resolveWinner()
    {
        Assertion::eq(count($this->cardsFaceUp), 2);

        list($player1, $player2) = array_keys($this->cardsFaceUp);

        if ($this->cardsFaceUp[$player1]->isEquals($this->cardsFaceUp[$player2])) {
            $this->putAllCardsFaceDown();

            throw new War();
        }

        if ($this->cardsFaceUp[$player1]->isGreaterThan($this->cardsFaceUp[$player2])) {
            $this->putAllCardsFaceDown();

            return PlayerId::fromString($player1);
        }

        $this->putAllCardsFaceDown();

        return PlayerId::fromString($player2);
    }

    public function wonCards()
    {
        $wonCards = array_values($this->cardsFaceUp) + array_values($this->cardsFaceDown);

        $this->cardsFaceUp = [];
        $this->cardsFaceDown = [];

        return $wonCards;
    }

    public function numberOfCardsInTheRound()
    {
        return count($this->cardsFaceUp) + count($this->cardsFaceDown);
    }

    private function putAllCardsFaceDown()
    {
        $this->cardsFaceDown = array_merge($this->cardsFaceDown, array_values($this->cardsFaceUp));
        $this->cardsFaceUp = [];
    }
}
