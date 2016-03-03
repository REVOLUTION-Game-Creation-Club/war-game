<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\TableIsFull;
use WarGame\Domain\Player\TableIsNotFull;

class TableSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('WarGame\Domain\Player\Table');
    }

    function it_should_welcome_a_player_as_long_as_table_is_not_full()
    {
        $player = Player::named('Lucas', PlayerId::generate());

        $this->welcome($player);
        $this->getPlayer1()->shouldBe($player);
        $this->isFull()->shouldBe(false);
        $this->shouldThrow(TableIsNotFull::class)->during('getPlayer2');
    }

    function it_should_reject_a_player_when_table_is_full(
    ) {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player3 = Player::named('John', PlayerId::generate());

        $this->welcome($player1);
        $this->welcome($player2);
        $this->getPlayer1()->shouldBe($player1);
        $this->getPlayer2()->shouldBe($player2);
        $this->isFull()->shouldBe(true);
        $this->shouldThrow(TableIsFull::class)->during('welcome', [$player3]);
    }
}
