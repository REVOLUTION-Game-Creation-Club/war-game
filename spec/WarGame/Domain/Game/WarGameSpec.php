<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Game\CardsAreNotDealt;
use WarGame\Domain\Game\WarGame;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

class WarGameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Deck::frenchDeck(), new Table());
        $this->shouldHaveType('WarGame\Domain\Game\WarGame');
    }

    function it_does_not_init_if_deck_is_empty()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));
        $table->welcome(Player::named('Jeremy', PlayerId::generate()));

        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [new Deck(), $table]);
    }

    function it_does_not_init_if_table_is_empty()
    {
        $this->beConstructedWith();
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [Deck::frenchDeck(), new Table()]);
    }

    function it_plays_a_round_when_cards_are_dealt()
    {
        $deck = Deck::frenchDeck();
        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->shouldThrow(CardsAreNotDealt::class)->during('play');
        $this->dealCards();
        $this->play()->shouldReturnAnInstanceOf(WarGame::class);
        $this->getWinner()->shouldReturnAnInstanceOf(Player::class);
    }
}
