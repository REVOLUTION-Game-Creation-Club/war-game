<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Game\BattleCannotTakePlace;
use WarGame\Domain\Game\Round;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Game\War;
use WarGame\Domain\Player\Table;

class RoundSpec extends ObjectBehavior
{
    function it_doesnt_start_if_table_in_not_full()
    {
        $table = new Table();
        $table->welcome(Player::named('Lucas', PlayerId::generate()));

        $this->beConstructedWith(1, $table);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [1, $table]);

        $table->welcome(Player::named('Jeremy', PlayerId::generate()));

        $this->beConstructedWith(1, $table);
        $this->shouldHaveType('WarGame\Domain\Game\Round');
    }

    function it_resolves_the_winner_with_one_higher_card()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(5), Suit::clubs()));
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(3), Suit::clubs()));
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->play();
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_plays_with_only_2_cards_on_the_table()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->shouldThrow(BattleCannotTakePlace::class)->during('play');

        $player1->receiveCard(new Card(new Rank(5), Suit::clubs()));
        $player2->receiveCard(new Card(new Rank(3), Suit::clubs()));

        $this->play();
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_allows_players_to_add_cards_face_down_and_one_face_up()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(Rank::king(), Suit::diamonds()));
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(Rank::king(), Suit::hearts()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->shouldThrow(War::class)->during('play', [Round::ROUND_IS_IN_WAR]);

        $this->numberOfCardsInTheRound()->shouldBe(8);
    }

    function it_returns_played_cards_in_the_round()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(Rank::king(), Suit::diamonds()));
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(Rank::queen(), Suit::hearts()));
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->getAllCards()->shouldHaveCount(0);
        $this->numberOfCardsInTheRound()->shouldBe(0);
        $this->play();
        $this->getAllCards()->shouldHaveCount(2);
        $this->numberOfCardsInTheRound()->shouldBe(2);
    }

    function it_should_detect_wars()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(2), Suit::hearts()));
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(2), Suit::clubs()));
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->shouldThrow(War::class)->during('play');
        $this->numberOfCardsInTheRound()->shouldBe(2);
    }

    function it_should_detect_double_wars_and_return_won_cards()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(2), Suit::hearts()));
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(new Card(new Rank(7), Suit::hearts()));
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(2), Suit::clubs()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(new Card(new Rank(7), Suit::clubs()));
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->shouldThrow(War::class)->during('play');
        $this->numberOfCardsInTheRound()->shouldBe(2);
        $this->shouldThrow(War::class)->during('play', [Round::ROUND_IS_IN_WAR]);
        $this->numberOfCardsInTheRound()->shouldBe(10);
    }

    function it_detects_if_player_ran_out_of_cards_during_normal_battle()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->play();
        $this->numberOfCardsInTheRound()->shouldBe(1);
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_detects_if_player_1_ran_out_of_cards_during_war()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(10), Suit::clubs()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->play(Round::ROUND_IS_IN_WAR);
        $this->numberOfCardsInTheRound()->shouldBe(1);
        $this->getWinner()->shouldBeLike($player2);
    }

    function it_detects_if_player_2_ran_out_of_cards_during_war()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->play(Round::ROUND_IS_IN_WAR);
        $this->numberOfCardsInTheRound()->shouldBe(7);
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_detects_if_both_players_ran_out_of_cards_during_war()
    {
        $table = new Table();
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $table->welcome($player1);

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(Card::random());
        $table->welcome($player2);

        $this->beConstructedWith(1, $table);
        $this->play(Round::ROUND_IS_IN_WAR);
        $this->numberOfCardsInTheRound()->shouldBe(2);
        $this->getWinner()->shouldBeLike($player2);
    }
}
