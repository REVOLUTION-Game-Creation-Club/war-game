<?php

namespace WarGame\Domain\Player;

use Assert\Assertion;
use WarGame\Domain\Card\Deck;

final class Dealer
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var Table
     */
    private $table;

    public function __construct(Deck $deck, Table $table)
    {
        Assertion::true($table->isFull(), 'Table is not full.');

        $this->deck = $deck;
        $this->table = $table;
    }

    public function dealCardsOneByOne()
    {
        while (!$this->deck->isEmpty()) {
            $this->table->getPlayer1()->receiveCard($this->deck->pickFromTheTop());
            $this->table->getPlayer2()->receiveCard($this->deck->pickFromTheTop());
        }
    }
}
