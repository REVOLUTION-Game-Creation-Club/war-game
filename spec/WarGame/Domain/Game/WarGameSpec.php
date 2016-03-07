<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Game\CannotPlayTwice;
use WarGame\Domain\Game\CardsAlreadyDealt;
use WarGame\Domain\Game\CardsAreNotDealt;
use WarGame\Domain\Game\GameIsNotOver;
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

    function it_does_not_init_if_table_is_not_full()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [Deck::frenchDeck(), $table]);
    }

    function it_does_not_init_if_less_than_2_cards()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));
        $table->welcome(Player::named('Jeremy', PlayerId::generate()));

        $deck = new Deck([
            new Card(Rank::king(), Suit::clubs())
        ]);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$deck, $table]);
    }

    function it_deals_cards()
    {
        $deck = Deck::frenchDeck();
        $deck->shuffle();

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $this->getCurrentStatus()->shouldBe(WarGame::STATUS_CARDS_DEALT);
    }

    function it_cannot_deal_cards_after_they_have_already_been_dealt()
    {
        $deck = Deck::frenchDeck();
        $deck->shuffle();

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $this->shouldThrow(CardsAlreadyDealt::class)->during('dealCards');
        $this->getCurrentStatus()->shouldBe(WarGame::STATUS_CARDS_DEALT);
    }

    function it_plays_a_battle_when_cards_are_dealt_and_returns_the_winner()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::king(), Suit::clubs())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->shouldThrow(CardsAreNotDealt::class)->during('play');
        $this->dealCards();
        $this->play();
        $this->getWinner()->shouldReturnAnInstanceOf(Player::class);
    }

    function it_has_a_fluent_interface()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::king(), Suit::clubs())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCards()->shouldReturnAnInstanceOf(WarGame::class);
        $this->play()->shouldReturnAnInstanceOf(WarGame::class);
    }

    function it_cannot_play_same_game_twice()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::king(), Suit::clubs())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $this->play();
        $this->shouldThrow(CannotPlayTwice::class)->during('play');
    }

    function it_cannot_return_a_winner_until_game_is_not_over()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::king(), Suit::clubs())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $table = new Table();
        $table->welcome($lucas);
        $table->welcome($jeremy);

        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $this->shouldThrow(GameIsNotOver::class)->during('getWinner');
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

        $this->beConstructedWith($deck, $table);
        $this->dealCards();
        $this->play();
        $this->getWinner()->shouldBeLike($jeremy); // Lucas is the first player to be out of cards
    }
}
