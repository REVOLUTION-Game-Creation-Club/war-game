<?php

namespace WarGame\Domain\Player;

final class Table
{
    private $players = [];

    public function welcome(Player $player)
    {
        if ($this->isFull()) {
            throw new TableIsFull();
        }

        $this->players[$player->getId()->toString()] = $player;
    }

    /**
     * @return Player
     */
    public function get(PlayerId $playerId)
    {
        return $this->players[$playerId->toString()];
    }

    public function getBothPlayers()
    {
        if (!$this->isFull()) {
            throw new TableIsNotFull();
        }

        return $this->players;
    }

    /**
     * @return Player
     */
    public function getPlayer1()
    {
        if (empty($this->players)) {
            throw new TableIsEmpty();
        }

        return array_values($this->players)[0];
    }

    /**
     * @return Player
     */
    public function getPlayer2()
    {
        if (!$this->isFull()) {
            throw new TableIsNotFull();
        }

        return end($this->players);
    }

    public function isFull()
    {
        return 2 === count($this->players);
    }
}