<?php

namespace WarGame\Domain\Game;

use Assert\Assertion;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Player\NotEnoughCards;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

class Round
{
    const ROUND_IS_IN_WAR = true;
    const ROUND_IS_NOT_IN_WAR = false;
    const VARIANT_WAR_WITH_3_CARDS = 3;

    /**
     * @var Card[]
     */
    private $cardsFaceUp;

    /**
     * @var Card[]
     */
    private $cardsFaceDown;

    private $roundNumber;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var Player $winner Winner of the round
     */
    private $winner;

    public function __construct($roundNumber, Table $table)
    {
        Assertion::integer($roundNumber, 'Round number should be a number.');
        Assertion::true($table->isFull(), 'Table has to be full to start a new round.');

        $this->cardsFaceUp = [];
        $this->cardsFaceDown = [];
        $this->roundNumber = $roundNumber;
        $this->table = $table;
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
    public function play($isInWar = self::ROUND_IS_NOT_IN_WAR)
    {
        if ($this->table->getPlayer1()->getNbOfCards() + $this->table->getPlayer2()->getNbOfCards() + count($this->cardsFaceUp) < 2) {
            throw new BattleCannotTakePlace();
        }

        if (self::ROUND_IS_IN_WAR === $isInWar) {
            try {
                $this->playerAddsCardFaceDown($this->table->getPlayer1(), self::VARIANT_WAR_WITH_3_CARDS);
            } catch (NotEnoughCards $e) {
                return $this->nominateAndAwardWinner($this->table->getPlayer2());
            }

            try {
                $this->playerAddsCardFaceDown($this->table->getPlayer2(), self::VARIANT_WAR_WITH_3_CARDS);
            } catch (NotEnoughCards $e) {
                return $this->nominateAndAwardWinner($this->table->getPlayer1());
            }
        }

        try {
            $this->playerAddsCardFaceUp($this->table->getPlayer1());
        } catch (NotEnoughCards $e) {
            return $this->nominateAndAwardWinner($this->table->getPlayer2());
        }

        try {
            $this->playerAddsCardFaceUp($this->table->getPlayer2());
        } catch (NotEnoughCards $e) {
            return $this->nominateAndAwardWinner($this->table->getPlayer1());
        }

        if (count($this->cardsFaceUp) === 2) {
            $winnerId = $this->resolveCardsUpBattle();

            return $this->nominateAndAwardWinner($this->table->get($winnerId));
        }

        throw new BattleCannotTakePlace();
    }

    /**
     * @return PlayerId Winner id
     */
    private function resolveCardsUpBattle()
    {
        list($player1, $player2) = array_keys($this->cardsFaceUp);

        if ($this->cardsFaceUp[$player1]->isEquals($this->cardsFaceUp[$player2])) {
            $this->putAllCardsFaceDown();

            throw new War();
        }

        if ($this->cardsFaceUp[$player1]->isGreaterThan($this->cardsFaceUp[$player2])) {
            return PlayerId::fromString($player1);
        }

        return PlayerId::fromString($player2);
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

    public function numberOfCardsInTheRound()
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
