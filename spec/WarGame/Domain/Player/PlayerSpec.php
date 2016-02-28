<?php

namespace spec\WarGame\Domain\Player;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;

class PlayerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->shouldHaveType('WarGame\Domain\Player\Player');
    }

    function it_should_name_players()
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->getName()->shouldBe('Lucas');
    }

    function it_should_receive_cards(Card $card)
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->receiveCard($card);
        $this->isStillHaveCards()->shouldBe(true);
    }

    function it_should_know_how_many_cards_he_has(Card $card)
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->receiveCard($card);
        $this->getNbOfCards()->shouldBe(1);
    }

    function it_should_signal_if_he_is_ready()
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->readyToStart();
        $this->isReady()->shouldBe(true);
    }

    function it_should_put_won_cards_on_the_bottom_of_the_stack(Card $card1, Card $card2, Card $card3)
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->receiveCard($card1);
        $this->readyToStart();

        $this->getNbOfCards()->shouldBe(1);
        $this->wins([$card2, $card3]);
        $this->getNbOfCards()->shouldBe(3);
    }

    function it_should_put_one_card_up(Card $card1)
    {
        $this->beConstructedThrough('named', ['Lucas']);
        $this->receiveCard($card1);
        $this->readyToStart();

        $this->getNbOfCards()->shouldBe(1);
        $this->putOneCardUp()->shouldReturnAnInstanceOf(Card::class);
        $this->getNbOfCards()->shouldBe(0);
    }
}
