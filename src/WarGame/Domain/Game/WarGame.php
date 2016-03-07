<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\Table;

class WarGame
{
    const STATUS_STARTED = 10;
    const STATUS_CARDS_DEALT = 20;
    const STATUS_GAME_OVER = 30;

    /**
     * Variant of 1 or 3 cards
     */
    const NB_CARDS_FACE_DOWN = 3;

    /**
     * Number of wars one player has to win to win the game
     */
    const MAX_WARS = 5;

    /**
     * @var Deck $deck A deck of cards
     */
    private $deck;

    /**
     * @var Table $table A group of 2 players
     */
    private $table;

    /**
     * @var Battle[] $battles Battles
     */
    private $battles;

    private $currentStatus;

    /**
     * @var bool
     */
    private $isCurrentlyInWar;

    /**
     * @var array
     */
    private $timesPlayersHaveBeenInWar;

    /**
     * @var Player
     */
    private $winner;

    public function __construct(Deck $deck, Table $table)
    {
        Assertion::false($deck->isEmpty(), 'Deck is empty.');
        Assertion::greaterOrEqualThan($deck->getNbOfCards(), 2, 'Cannot play with less than 2 cards.');
        Assertion::true($table->isFull(), 'Table is not full.');

        $this->deck = $deck;
        $this->table = $table;
        $this->battles = [];
        $this->isCurrentlyInWar = false;
        $this->timesPlayersHaveBeenInWar = [
            $this->table->getPlayer1()->getId()->toString() => 0,
            $this->table->getPlayer2()->getId()->toString() => 0
        ];
        $this->currentStatus = self::STATUS_STARTED;
    }

    public function dealCards()
    {
        if ($this->currentStatus >= self::STATUS_CARDS_DEALT) {
            throw new CardsAlreadyDealt();
        }

        while (!$this->deck->isEmpty()) {
            $this->table->getPlayer1()->receiveCard($this->deck->pickFromTheTop());
            $this->table->getPlayer2()->receiveCard($this->deck->pickFromTheTop());
        }

        $this->currentStatus = self::STATUS_CARDS_DEALT;

        return $this;
    }

    public function play()
    {
        if ($this->currentStatus < self::STATUS_CARDS_DEALT) {
            throw new CardsAreNotDealt();
        }

        if ($this->currentStatus >= self::STATUS_GAME_OVER) {
            throw new CannotPlayTwice();
        }

        $battleNumber = 1;

        do {

            if (false === $this->isCurrentlyInWar) {
                $this->battles[$battleNumber] = new Battle($battleNumber, $this->table);
            }

            try {
                $this->battles[$battleNumber]->play($this->isCurrentlyInWar);

                // Player wins a war
                if (true === $this->isCurrentlyInWar) {
                    $battleWinner = $this->battles[$battleNumber]->getWinner();

                    if (self::MAX_WARS === ++$this->timesPlayersHaveBeenInWar[$battleWinner->getId()->toString()]) {
                        $this->winner = $battleWinner;

                        break;
                    }
                }

                $this->isCurrentlyInWar = false;
                $battleNumber++;
            } catch (War $e) {
                $this->isCurrentlyInWar = true;

                continue;
            }
        } while ($this->isCurrentlyInWar || !$this->oneOfThePlayersRanOutOfCards());

        if (null === $this->winner) {
            $this->winner = $this->table->getPlayer1()->isOutOfCards()
                ? $this->table->getPlayer2()
                : $this->table->getPlayer1();
        }

        $this->currentStatus = self::STATUS_GAME_OVER;

        return $this;
    }

    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    public function getWinner()
    {
        if ($this->currentStatus < self::STATUS_GAME_OVER) {
            throw new GameIsNotOver();
        }

        return $this->winner;
    }

    /**
     * @return Battle[]
     */
    public function getBattles()
    {
        return $this->battles;
    }

    /**
     * @return bool
     */
    private function oneOfThePlayersRanOutOfCards()
    {
        return $this->table->getPlayer1()->isOutOfCards() || $this->table->getPlayer2()->isOutOfCards();
    }
}
