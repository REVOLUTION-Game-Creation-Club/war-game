<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Table;

class WarGame
{
    const STATUS_PENDING = 'pending';
    const STATUS_STARTED = 'started';

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
        $this->deck = $deck;
        $this->table = $table;
        $this->status = self::STATUS_PENDING;
    }

    public function dealCards()
    {
        Assertion::false($this->deck->isEmpty(), 'Deck is empty.');

        while (!$this->deck->isEmpty()) {
            $this->table->getPlayer1()->receiveCard($this->deck->pick());
            $this->table->getPlayer2()->receiveCard($this->deck->pick());
        }
    }

    public function play()
    {
        $this->status = self::STATUS_STARTED;

        // TODO
    }

    public function isStarted()
    {
        return $this->status === self::STATUS_STARTED;
    }
}
