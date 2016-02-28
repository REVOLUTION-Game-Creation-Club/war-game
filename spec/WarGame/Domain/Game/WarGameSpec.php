<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Table;

class WarGameSpec extends ObjectBehavior
{
    function it_is_initializable(Deck $deck, Table $table)
    {
        $this->beConstructedWith($deck, $table);
        $this->shouldHaveType('WarGame\Domain\Game\WarGame');
    }

    function it_should_not_deal_cards_to_players_if_deck_is_empty(Deck $deck, Table $table)
    {
        $deck->isEmpty()->willReturn(true);
        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $deck->pick()->shouldNotHaveBeenCalled();
    }

    function it_should_play_a_round(Deck $deck, Table $table)
    {
        $this->beConstructedWith($deck, $table);
        $this->play();
        $this->isStarted()->shouldBe(true);
    }
}
