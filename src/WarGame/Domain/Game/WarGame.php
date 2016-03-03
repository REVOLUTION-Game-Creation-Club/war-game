<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Deck;
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
     * @var Deck $deck A standard 52-card deck
     */
    private $deck;

    /**
     * @var Table $table A group of 2 players
     */
    private $table;

    /**
     * @var Round[] $rounds Rounds
     */
    private $rounds;

    private $currentStatus;

    /**
     * @var bool
     */
    private $isCurrentlyInWar;

    /**
     * @var array
     */
    private $timesPlayersHaveBeenInWar;

    public function __construct(Deck $deck, Table $table)
    {
        Assertion::false($deck->isEmpty(), 'Deck is empty.');
        Assertion::true($table->isFull(), 'Table is not full.');

        $this->deck = $deck;
        $this->table = $table;
        $this->rounds = [];
        $this->currentStatus = self::STATUS_STARTED;
        $this->isCurrentlyInWar = false;
        $this->timesPlayersHaveBeenInWar = [
            $this->table->getPlayer1()->getId()->toString() => 0,
            $this->table->getPlayer2()->getId()->toString() => 0
        ];
    }

    public function dealCards($doShuffle = true)
    {
        if ($doShuffle) {
            $this->deck->shuffle();
        }

        while (!$this->deck->isEmpty()) {
            $this->table->getPlayer1()->receiveCard($this->deck->pickFromTheTop());
            $this->table->getPlayer2()->receiveCard($this->deck->pickFromTheTop());
        }

        $this->currentStatus = self::STATUS_CARDS_DEALT;
    }

    public function play()
    {
        if ($this->currentStatus < self::STATUS_CARDS_DEALT) {
            throw new CardsAreNotDealt();
        }

        $roundNumber = 1;

        do {
            if (false === $this->isCurrentlyInWar) {
                $this->rounds[$roundNumber] = new Round($roundNumber, $this->table);
            }

//            var_dump($roundNumber);
//            var_dump($this->rounds[$roundNumber]);
//            if ($this->isCurrentlyInWar)
//            var_dump($this->isCurrentlyInWar);

            try {
//                var_dump('teeest');
                $this->rounds[$roundNumber]->play($this->isCurrentlyInWar);
                $this->isCurrentlyInWar = false;
                $roundNumber++;
//                var_dump($winner);
            } catch (War $e) {
                $this->isCurrentlyInWar = true;
//                var_dump($this->rounds[$roundNumber]);

//                var_dump('OVER?');
//                var_dump($this->gameIsOver());
                continue;
            }


        } while (!$this->gameIsOver());

        $this->currentStatus = self::STATUS_GAME_OVER;

        return $this;
    }

    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

//    public function getLoser()
//    {
//        if ($this->currentStatus != self::STATUS_GAME_OVER) {
//            throw new GameIsNotOver();
//        }
//
//        return $this->getWinner()->getId()->sameValueAs($this->table->getPlayer1()->getId())
//            ? $this->table->getPlayer2()
//            : $this->table->getPlayer1();
//    }

    public function getWinner()
    {
        if ($this->currentStatus != self::STATUS_GAME_OVER) {
            throw new GameIsNotOver();
        }

        return $this->table->getPlayer1()->getNbOfCards() === 0 ? $this->table->getPlayer2() : $this->table->getPlayer1();
    }

    /**
     * @return bool
     */
    private function oneOfThePlayersRanOutOfCards()
    {
        return $this->table->getPlayer1()->isOutOfCards() || $this->table->getPlayer2()->isOutOfCards();
    }

    private function gameIsOver()
    {
        return $this->oneOfThePlayersRanOutOfCards();
//        || $this->table->getPlayer1()->nbWonWars() === 5
//        || $this->table->getPlayer1()->nbWonWars() === 5;
    }

    /**
     * @return Round[]
     */
    public function getRounds()
    {
        return $this->rounds;
    }
}
