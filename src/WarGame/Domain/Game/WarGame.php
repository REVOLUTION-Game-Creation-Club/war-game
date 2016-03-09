<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\Table;

class WarGame
{
    /**
     * Variant of 1 or 3 cards
     */
    const NB_CARDS_FACE_DOWN = 3;

    /**
     * Number of wars one player has to win to win the game
     */
    const MAX_WARS = 5;

    /**
     * @var Table $table A group of 2 players
     */
    private $table;

    /**
     * @var Battle[] $battles Battles
     */
    private $battles;

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

    public function __construct(Table $table)
    {
        Assertion::true($table->isFull(), 'Table is not full.');
        Assertion::false($table->getPlayer1()->isOutOfCards(), 'Player 1 is out of cards.');
        Assertion::false($table->getPlayer2()->isOutOfCards(), 'Player 2 is out of cards.');

        $this->table = $table;
        $this->battles = [];
        $this->isCurrentlyInWar = false;
        $this->timesPlayersHaveBeenInWar = [
            $this->table->getPlayer1()->getId()->toString() => 0,
            $this->table->getPlayer2()->getId()->toString() => 0
        ];

        $this->play();
    }

    /**
     * @return Player
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @return Battle[]
     */
    public function getBattles()
    {
        return $this->battles;
    }

    private function play()
    {
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
    }

    /**
     * @return bool
     */
    private function oneOfThePlayersRanOutOfCards()
    {
        return $this->table->getPlayer1()->isOutOfCards() || $this->table->getPlayer2()->isOutOfCards();
    }
}
