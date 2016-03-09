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

    public function __construct(Player $player1, Player $player2)
    {
        Assertion::false($player1->isOutOfCards(), 'Player 1 is out of cards.');
        Assertion::false($player2->isOutOfCards(), 'Player 2 is out of cards.');

        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->battles = [];
        $this->isCurrentlyInWar = false;
        $this->timesPlayersHaveBeenInWar = [
            $this->player1->getId()->toString() => 0,
            $this->player2->getId()->toString() => 0
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
                $this->battles[$battleNumber] = new Battle($this->player1, $this->player2);
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
            $this->winner = $this->player1->isOutOfCards()
                ? $this->player2
                : $this->player1;
        }
    }

    /**
     * @return bool
     */
    private function oneOfThePlayersRanOutOfCards()
    {
        return $this->player1->isOutOfCards() || $this->player2->isOutOfCards();
    }
}
