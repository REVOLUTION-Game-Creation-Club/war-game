<?php

namespace WarGame\Domain\Player;

use WarGame\Domain\Card\Deck;

final class Dealer
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var Player
     */
    private $player1;

    /**
     * @var Player
     */
    private $player2;

    public function __construct(Deck $deck, Player $player1, Player $player2)
    {
        $this->deck = $deck;
        $this->player1 = $player1;
        $this->player2 = $player2;
    }

    public function dealCardsOneByOne()
    {
        while (!$this->deck->isEmpty()) {
            $this->player1->receiveCard($this->deck->pickFromTheTop());
            $this->player2->receiveCard($this->deck->pickFromTheTop());
        }
    }
}
