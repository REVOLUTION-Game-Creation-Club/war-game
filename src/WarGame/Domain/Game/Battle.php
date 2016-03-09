<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Player\NotEnoughCards;
use WarGame\Domain\Player\Player;

final class Battle
{
    const BATTLE_IS_IN_WAR = true;
    const BATTLE_IS_NOT_IN_WAR = false;
    const VARIANT_WAR_WITH_NB_CARDS = 3;

    /**
     * @var Player
     */
    private $player1;

    /**
     * @var Player
     */
    private $player2;

    /**
     * @var Card[]
     */
    private $cardsFaceUp;

    /**
     * @var Card[]
     */
    private $cardsFaceDown;

    /**
     * @var Player $winner Winner of the battle
     */
    private $winner;

    public function __construct(Player $player1, Player $player2)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->cardsFaceUp = [];
        $this->cardsFaceDown = [];
    }

    private function playerAddsCardFaceUp(Player $player)
    {
        if (!array_key_exists($player->getId()->toString(), $this->cardsFaceUp)) {
            $this->cardsFaceUp[$player->getId()->toString()] = $player->putOneCard();
        }
    }

    private function playerAddsCardFaceDown(Player $player, $nbOfCardsFaceDown)
    {
        Assertion::greaterOrEqualThan($nbOfCardsFaceDown, 1, 'Players must put at least one card each (depending on the variant).');

        while ($nbOfCardsFaceDown-- > 0) {
            $this->cardsFaceDown[] = $player->putOneCard();
        }
    }

    /**
     * @param bool $isInWar
     *
     * @return Player Winner
     */
    public function play($isInWar = self::BATTLE_IS_NOT_IN_WAR)
    {
        if ($this->player1->getNbOfCards() + $this->player2->getNbOfCards() + count($this->cardsFaceUp) < 2) {
            throw new BattleCannotTakePlace();
        }

        if (self::BATTLE_IS_IN_WAR === $isInWar) {
            try {
                $this->playerAddsCardFaceDown($this->player1, self::VARIANT_WAR_WITH_NB_CARDS);
            } catch (NotEnoughCards $e) {
                return $this->nominateAndAwardWinner($this->player2);
            }

            try {
                $this->playerAddsCardFaceDown($this->player2, self::VARIANT_WAR_WITH_NB_CARDS);
            } catch (NotEnoughCards $e) {
                return $this->nominateAndAwardWinner($this->player1);
            }
        }

        try {
            $this->playerAddsCardFaceUp($this->player1);
        } catch (NotEnoughCards $e) {
            return $this->nominateAndAwardWinner($this->player2);
        }

        try {
            $this->playerAddsCardFaceUp($this->player2);
        } catch (NotEnoughCards $e) {
            return $this->nominateAndAwardWinner($this->player1);
        }

        if (count($this->cardsFaceUp) === 2) {
            $winner = $this->resolveCardsUpBattle();

            return $this->nominateAndAwardWinner($winner);
        }

        throw new BattleCannotTakePlace();
    }

    /**
     * @return Player Winner of the battle
     */
    private function resolveCardsUpBattle()
    {
        list($player1, $player2) = array_keys($this->cardsFaceUp);

        if ($this->cardsFaceUp[$player1]->isEquals($this->cardsFaceUp[$player2])) {
            $this->putAllCardsFaceDown();

            throw new War();
        }

        if ($this->cardsFaceUp[$player1]->isGreaterThan($this->cardsFaceUp[$player2])) {
            return $this->player1;
        }

        return $this->player2;
    }

    /**
     * @return Card[]
     */
    public function getAllCards()
    {
        return array_merge(
            array_values($this->cardsFaceUp),
            $this->cardsFaceDown
        );
    }

    public function numberOfCardsInTheBattle()
    {
        return count($this->cardsFaceUp) + count($this->cardsFaceDown);
    }

    /**
     * @return Player
     */
    public function getWinner()
    {
        return $this->winner;
    }

    private function nominateAndAwardWinner(Player $winner)
    {
        $this->putAllCardsFaceDown();

        $winner->wins($this->getAllCards());

        $this->winner = $winner;

        return $winner;
    }

    private function putAllCardsFaceDown()
    {
        $this->cardsFaceDown = array_merge($this->cardsFaceDown, array_values($this->cardsFaceUp));
        $this->cardsFaceUp = [];
    }
}
