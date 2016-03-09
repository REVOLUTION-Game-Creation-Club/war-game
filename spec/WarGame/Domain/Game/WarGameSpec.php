<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Game\Battle;
use WarGame\Domain\Game\GameIsOver;
use WarGame\Domain\Player\Dealer;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;

class WarGameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player2 = Player::named('Jeremy', PlayerId::generate());

        $this->beConstructedWith($player1, $player2);
        $this->shouldHaveType('WarGame\Domain\Game\WarGame');
    }

    function it_does_not_init_if_players_have_no_card()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player2 = Player::named('Jeremy', PlayerId::generate());

        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$player1, $player2]);
    }

    function it_gets_the_winner()
    {
        $lucas = Player::named('Lucas', PlayerId::generate());
        $lucas->receiveCard(new Card(Rank::ace(), Suit::clubs()));

        $jeremy = Player::named('Jeremy', PlayerId::generate());
        $jeremy->receiveCard(new Card(Rank::king(), Suit::clubs()));

        $this->beConstructedWith($lucas, $jeremy);
        $this->playBattle()->shouldReturnAnInstanceOf(Battle::class);
        $this->getWinner()->shouldReturnAnInstanceOf(Player::class);
        $this->hasWinner()->shouldBe(true);
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

        $dealer = new Dealer($deck, $lucas, $jeremy);
        $dealer->dealCardsOneByOne();

        $this->beConstructedWith($lucas, $jeremy);
        $this->playBattle();
        $this->getWinner()->shouldBeLike($jeremy); // Lucas is the first player to be out of cards
    }

    function it_throws_exception_if_trying_to_play_after_game_is_over()
    {
        $deck = new Deck([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::king(), Suit::diamonds())
        ]);

        $lucas = Player::named('Lucas', PlayerId::generate());
        $jeremy = Player::named('Jeremy', PlayerId::generate());

        $dealer = new Dealer($deck, $lucas, $jeremy);
        $dealer->dealCardsOneByOne();

        $this->beConstructedWith($lucas, $jeremy);
        $this->playBattle();
        $this->getWinner()->shouldBeLike($jeremy);
        $this->shouldThrow(GameIsOver::class)->during('playBattle');
    }
}
