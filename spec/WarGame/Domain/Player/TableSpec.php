<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
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

    function it_should_welcome_a_player_as_long_as_table_is_not_full(Player $player, PlayerId $playerId)
    {
        $playerId->toString()->willReturn(Uuid::uuid4()->toString());
        $player->getId()->willReturn($playerId);

        $this->welcome($player);
        $this->getPlayer1()->shouldBe($player);
        $this->isFull()->shouldBe(false);
        $this->shouldThrow(TableIsNotFull::class)->during('getPlayer2');
    }

    function it_should_reject_a_player_when_table_is_full(
        Player $player1,
        PlayerId $playerId1,
        Player $player2,
        PlayerId $playerId2,
        Player $player3,
        PlayerId $playerId3
    ) {
        $playerId1->toString()->willReturn(Uuid::uuid4()->toString());
        $player1->getId()->willReturn($playerId1);

        $playerId2->toString()->willReturn(Uuid::uuid4()->toString());
        $player2->getId()->willReturn($playerId2);

        $playerId3->toString()->willReturn(Uuid::uuid4()->toString());
        $player1->getId()->willReturn($playerId3);

        $this->welcome($player1);
        $this->welcome($player2);
        $this->getPlayer1()->shouldBe($player1);
        $this->getPlayer2()->shouldBe($player2);
        $this->isFull()->shouldBe(true);
        $this->shouldThrow(TableIsFull::class)->during('welcome', [$player3]);

    }
}
