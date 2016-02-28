<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Game\Round;
use WarGame\Domain\Game\War;

class RoundSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('WarGame\Domain\Game\Round');
    }

    function it_adds_one_card_face_up(Card $card, PlayerId $playerId)
    {
        $this->playerAddsCardFaceUp($playerId, $card);
    }

    function it_cannot_add_more_than_2_cards_face_up(
        Card $card1, PlayerId $playerId1, Card $card2, PlayerId $playerId2, Card $card3, PlayerId $playerId3
    ) {
        $playerId1->toString()->willReturn(Uuid::uuid4()->toString());
        $playerId2->toString()->willReturn(Uuid::uuid4()->toString());
        $playerId3->toString()->willReturn(Uuid::uuid4()->toString());

        $this->playerAddsCardFaceUp($playerId1, $card1);
        $this->playerAddsCardFaceUp($playerId2, $card2);
        $this->shouldThrow(\InvalidArgumentException::class)->during('playerAddsCardFaceUp', [$playerId3, $card3]);
    }

    function it_resolves_the_winner(Card $card1, PlayerId $playerId1, Card $card2, PlayerId $playerId2)
    {
        $playerId1->toString()->willReturn(Uuid::uuid4()->toString());
        $playerId2->toString()->willReturn(Uuid::uuid4()->toString());

        $this->playerAddsCardFaceUp($playerId1, $card1);
        $this->playerAddsCardFaceUp($playerId2, $card2);
        $this->resolveWinner()->shouldReturnAnInstanceOf(PlayerId::class);
    }

    function it_returns_won_cards(Card $card1, PlayerId $playerId1, Card $card2, PlayerId $playerId2)
    {
        $playerId1->toString()->willReturn(Uuid::uuid4()->toString());
        $playerId2->toString()->willReturn(Uuid::uuid4()->toString());

        $this->playerAddsCardFaceUp($playerId1, $card1)->shouldBeAnInstanceOf(Round::class);
        $this->playerAddsCardFaceUp($playerId2, $card2)->shouldBeAnInstanceOf(Round::class);
        $this->resolveWinner();
        $this->wonCards()->shouldHaveCount(2);
    }

    function it_should_detect_wars(Card $card1, PlayerId $playerId1, Card $card2, PlayerId $playerId2)
    {
        $playerId1->toString()->willReturn(Uuid::uuid4()->toString());
        $card1->isEquals($card2)->willReturn(true);
        $playerId2->toString()->willReturn(Uuid::uuid4()->toString());

        $this->playerAddsCardFaceUp($playerId1, $card1);
        $this->playerAddsCardFaceUp($playerId2, $card2);
        $this->shouldThrow(War::class)->during('resolveWinner');
    }
}
