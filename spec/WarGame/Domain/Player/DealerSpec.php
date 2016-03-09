<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;

class DealerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $this->beConstructedWith(new Deck(), $lucas, $jeremy);
        $this->shouldHaveType('WarGame\Domain\Player\Dealer');
    }

    function it_deals_cards_to_players_one_by_one()
    {
        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $this->beConstructedWith(Deck::frenchDeck(), $lucas, $jeremy);
        $this->dealCardsOneByOne();
    }
}
