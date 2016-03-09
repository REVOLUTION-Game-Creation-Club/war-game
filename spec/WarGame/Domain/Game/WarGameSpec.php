<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Player\Dealer;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

class WarGameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new Table());
        $this->shouldHaveType('WarGame\Domain\Game\WarGame');
    }

    function it_does_not_init_if_table_is_not_full()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$table]);
    }

    function it_does_not_init_if_players_have_no_card()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));
        $table->welcome(Player::named('Jeremy', PlayerId::generate()));

        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$table]);
    }

    function it_gets_the_winner()
    {
        $lucas = Player::named('Lucas', PlayerId::generate());
        $lucas->receiveCard(new Card(Rank::ace(), Suit::clubs()));

        $jeremy = Player::named('Jeremy', PlayerId::generate());
        $jeremy->receiveCard(new Card(Rank::king(), Suit::clubs()));

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($table);
        $this->getWinner()->shouldReturnAnInstanceOf(Player::class);
    }

    function it_returns_played_battles()
    {
        $lucas = Player::named('Lucas', PlayerId::generate());
        $lucas->receiveCard(new Card(Rank::ace(), Suit::clubs()));

        $jeremy = Player::named('Jeremy', PlayerId::generate());
        $jeremy->receiveCard(new Card(Rank::king(), Suit::clubs()));

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($table);
        $this->getBattles()->shouldHaveCount(1);
    }

    function it_returns_a_winner_even_if_players_have_same_rank_cards()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::ace(), Suit::diamonds()),
            new Card(Rank::king(), Suit::clubs()),
            new Card(Rank::king(), Suit::diamonds())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $dealer = new Dealer($deck, $table);
        $dealer->dealCardsOneByOne();

        $this->beConstructedWith($table);
        $this->getWinner()->shouldBeLike($jeremy); // Lucas is the first player to be out of cards
    }
}
