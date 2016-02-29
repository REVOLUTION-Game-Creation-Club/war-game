<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Player\NotEnoughCards;
use WarGame\Domain\Player\PlayerId;

class PlayerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->shouldHaveType('WarGame\Domain\Player\Player');
    }

    function it_should_name_players()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->getName()->shouldBe('Lucas');
    }

    function it_should_receive_cards()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(Card::random());
        $this->isStillHaveCards()->shouldBe(true);
    }

    function it_should_know_how_many_cards_he_has()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(Card::random());
        $this->getNbOfCards()->shouldBe(1);
    }

    function it_should_signal_if_he_is_ready()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->readyToStart();
        $this->isReady()->shouldBe(true);
    }

    function it_should_put_won_cards_on_the_bottom_of_the_stack()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(Card::random());
        $this->readyToStart();

        $this->getNbOfCards()->shouldBe(1);
        $this->wins([Card::random(), Card::random()]);
        $this->getNbOfCards()->shouldBe(3);
    }

    function it_should_put_one_card_up()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(Card::random());
        $this->readyToStart();

        $this->getNbOfCards()->shouldBe(1);
        $this->putOneCardUp()->shouldReturnAnInstanceOf(Card::class);
        $this->getNbOfCards()->shouldBe(0);
    }

    function it_should_put_cards_down()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(new Card(new Rank(3), Suit::clovers()));
        $this->receiveCard(new Card(new Rank(4), Suit::clovers()));
        $this->receiveCard(new Card(new Rank(5), Suit::clovers()));

        $this->readyToStart();

        $this->getNbOfCards()->shouldBe(3);
        $this->putCardsFaceDown(3)->shouldHaveCount(3);
        $this->getNbOfCards()->shouldBe(0);
    }

    function it_cannot_return_more_cards_than_available()
    {
        $this->beConstructedThrough('named', ['Lucas', PlayerId::generate()]);
        $this->receiveCard(new Card(new Rank(3), Suit::clovers()));
        $this->receiveCard(new Card(new Rank(4), Suit::clovers()));
        $this->receiveCard(new Card(new Rank(5), Suit::clovers()));

        $this->readyToStart();

        $this->shouldThrow(NotEnoughCards::class)->during('putCardsFaceDown', [4]);
    }
}
