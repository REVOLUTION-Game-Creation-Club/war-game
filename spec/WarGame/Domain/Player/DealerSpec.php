<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

class DealerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new Deck(), new Table());
        $this->shouldHaveType('WarGame\Domain\Player\Dealer');
    }

    function it_deals_cards_to_players_one_by_one()
    {
        $deck = Deck::frenchDeck();

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCardsOneByOne();
    }

    function it_only_deals_cards_to_two_players()
    {
        $deck = Deck::frenchDeck();

        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));

        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$deck, $table]);
    }
}
