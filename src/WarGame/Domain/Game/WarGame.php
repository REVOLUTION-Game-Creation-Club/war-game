<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Player\Player;

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
     * @var Player
     */
    private $player1;

    /**
     * @var Player
     */
    private $player2;

    /**
     * @var Player
     */
    private $winner;

    /**
     * @var array
     */
    private $timesPlayersHaveBeenInWar;

    public function __construct(Player $player1, Player $player2)
    {
        Assertion::false($player1->isOutOfCards(), 'Player 1 is out of cards.');
        Assertion::false($player2->isOutOfCards(), 'Player 2 is out of cards.');

        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->timesPlayersHaveBeenInWar = [
            $this->player1->getId()->toString() => 0,
            $this->player2->getId()->toString() => 0
        ];
    }

    /**
     * @return bool
     */
    public function hasWinner()
    {
        return null !== $this->winner;
    }

    /**
     * @return Player
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @return Battle
     */
    public function playBattle()
    {
        if ($this->hasWinner()) {
            throw new GameIsOver();
        }

        $currentBattle = new Battle($this->player1, $this->player2);
        $isCurrentlyInWar = Battle::IS_NOT_IN_WAR;

        do {
            try {
                // May throw War
                $currentBattle->play($isCurrentlyInWar);

                // Player wins a war
                if (Battle::IS_IN_WAR === $isCurrentlyInWar) {
                    $isCurrentlyInWar = Battle::IS_NOT_IN_WAR;

                    $battleWinner = $currentBattle->getWinner();

                    if (self::MAX_WARS === ++$this->timesPlayersHaveBeenInWar[$battleWinner->getId()->toString()]) {
                        $this->winner = $battleWinner;

                        return $currentBattle;
                    }
                }
            } catch (War $e) {
                $isCurrentlyInWar = Battle::IS_IN_WAR;
            }
        } while (Battle::IS_IN_WAR === $isCurrentlyInWar);

        if ($this->player1->isOutOfCards() || $this->player2->isOutOfCards()) {
            $this->winner = $this->player1->isOutOfCards() ? $this->player2 : $this->player1;
        }

        return $currentBattle;
    }
}
