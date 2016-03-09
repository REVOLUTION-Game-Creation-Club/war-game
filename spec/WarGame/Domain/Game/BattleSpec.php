<?php

namespace spec\WarGame\Domain\Game;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Game\BattleCannotTakePlace;
use WarGame\Domain\Game\Battle;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Game\War;

class BattleSpec extends ObjectBehavior
{
    function it_resolves_the_winner_with_one_higher_card()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(5), Suit::clubs()));

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(3), Suit::clubs()));

        $this->beConstructedWith($player1, $player2);
        $this->play();
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_plays_with_only_2_cards_on_the_table()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player2 = Player::named('Jeremy', PlayerId::generate());

        $this->beConstructedWith($player1, $player2);
        $this->shouldThrow(BattleCannotTakePlace::class)->during('play');

        $player1->receiveCard(new Card(new Rank(5), Suit::clubs()));
        $player2->receiveCard(new Card(new Rank(3), Suit::clubs()));

        $this->play();
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_allows_players_to_add_cards_face_down_and_one_face_up()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(Rank::king(), Suit::diamonds()));
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(Rank::king(), Suit::hearts()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());

        $this->beConstructedWith($player1, $player2);
        $this->shouldThrow(War::class)->during('play', [Battle::BATTLE_IS_IN_WAR]);

        $this->numberOfCardsInTheBattle()->shouldBe(8);
    }

    function it_returns_played_cards_in_the_battle()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(Rank::king(), Suit::diamonds()));

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(Rank::queen(), Suit::hearts()));

        $this->beConstructedWith($player1, $player2);
        $this->getAllCards()->shouldHaveCount(0);
        $this->numberOfCardsInTheBattle()->shouldBe(0);
        $this->play();
        $this->getAllCards()->shouldHaveCount(2);
        $this->numberOfCardsInTheBattle()->shouldBe(2);
    }

    function it_should_detect_wars()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(2), Suit::hearts()));

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(2), Suit::clubs()));

        $this->beConstructedWith($player1, $player2);
        $this->shouldThrow(War::class)->during('play');
        $this->numberOfCardsInTheBattle()->shouldBe(2);
    }

    function it_should_detect_double_wars_and_return_won_cards()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(new Card(new Rank(2), Suit::hearts()));
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(new Card(new Rank(7), Suit::hearts()));

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(2), Suit::clubs()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(new Card(new Rank(7), Suit::clubs()));

        $this->beConstructedWith($player1, $player2);
        $this->shouldThrow(War::class)->during('play');
        $this->numberOfCardsInTheBattle()->shouldBe(2);
        $this->shouldThrow(War::class)->during('play', [Battle::BATTLE_IS_IN_WAR]);
        $this->numberOfCardsInTheBattle()->shouldBe(10);
    }

    function it_detects_if_player_ran_out_of_cards_during_normal_battle()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());

        $player2 = Player::named('Jeremy', PlayerId::generate());

        $this->beConstructedWith($player1, $player2);
        $this->play();
        $this->numberOfCardsInTheBattle()->shouldBe(1);
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_detects_if_player_1_ran_out_of_cards_during_war()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(new Card(new Rank(10), Suit::clubs()));
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());

        $this->beConstructedWith($player1, $player2);
        $this->play(Battle::BATTLE_IS_IN_WAR);
        $this->numberOfCardsInTheBattle()->shouldBe(1);
        $this->getWinner()->shouldBeLike($player2);
    }

    function it_detects_if_player_2_ran_out_of_cards_during_war()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());
        $player2->receiveCard(Card::random());

        $this->beConstructedWith($player1, $player2);
        $this->play(Battle::BATTLE_IS_IN_WAR);
        $this->numberOfCardsInTheBattle()->shouldBe(7);
        $this->getWinner()->shouldBeLike($player1);
    }

    function it_detects_if_both_players_ran_out_of_cards_during_war()
    {
        $player1 = Player::named('Lucas', PlayerId::generate());
        $player1->receiveCard(Card::random());
        $player1->receiveCard(Card::random());

        $player2 = Player::named('Jeremy', PlayerId::generate());
        $player2->receiveCard(Card::random());

        $this->beConstructedWith($player1, $player2);
        $this->play(Battle::BATTLE_IS_IN_WAR);
        $this->numberOfCardsInTheBattle()->shouldBe(2);
        $this->getWinner()->shouldBeLike($player2);
    }
}
