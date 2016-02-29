<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Table;

class WarGameSpec extends ObjectBehavior
{
    function it_is_initializable(Table $table)
    {
        $this->beConstructedWith(Deck::frenchDeck(), $table);
        $this->shouldHaveType('WarGame\Domain\Game\WarGame');
    }

    function it_should_not_deal_cards_to_players_if_deck_is_empty(Table $table)
    {
        $deck = new Deck();

        $this->beConstructedWith($deck, $table);
        $this->shouldThrow(\InvalidArgumentException::class)->during('dealCards');
    }

    function it_should_play_a_round(Table $table)
    {
        $this->beConstructedWith(Deck::frenchDeck(), $table);
        $this->play();
        $this->isStarted()->shouldBe(true);
    }
}
