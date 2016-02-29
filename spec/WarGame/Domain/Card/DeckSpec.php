<?php

namespace spec\WarGame\Domain\Card;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;

class DeckSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('WarGame\Domain\Card\Deck');
    }

    function it_should_create_a_french_deck()
    {
        $this->beConstructedThrough('frenchDeck');
        $this->getNbOfCards()->shouldBe(52);
    }


    function it_should_shuffle_cards()
    {
        $this->beConstructedThrough('frenchDeck');
        $this->shuffle();
    }

    function it_should_pick_one_card()
    {
        $this->beConstructedThrough('frenchDeck');
        $this->pick()->shouldHaveType(Card::class);
        $this->getNbOfCards()->shouldBe(51);
    }

    function it_should_add_a_card()
    {
        $this->beConstructedThrough('frenchDeck');
        $this->pick()->shouldHaveType(Card::class);
        $this->getNbOfCards()->shouldBe(51);
        $this->add(Card::random());
        $this->getNbOfCards()->shouldBe(52);
    }

    function it_should_verify_if_deck_is_empty()
    {
        $this->beConstructedWith();
        $this->getNbOfCards()->shouldBe(0);
        $this->isEmpty()->shouldBe(true);
    }
}
