<?php

namespace spec\WarGame\Domain\Card;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;

class CardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());
        $this->shouldHaveType('WarGame\Domain\Card\Card');
    }

    function it_should_compare_with_a_greater_card(Card $secondCard)
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $secondCard->getWeight()->willReturn(7);

        $this->isGreaterThan($secondCard)->shouldBe(false);
        $this->isSmallerThan($secondCard)->shouldBe(true);
        $this->isEquals($secondCard)->shouldBe(false);
    }

    function it_should_compare_with_a_smaller_card(Card $secondCard)
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $secondCard->getWeight()->willReturn(3);

        $this->isGreaterThan($secondCard)->shouldBe(true);
        $this->isSmallerThan($secondCard)->shouldBe(false);
        $this->isEquals($secondCard)->shouldBe(false);
    }

    function it_should_compare_with_an_equal_card(Card $secondCard)
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $secondCard->getWeight()->willReturn(5);

        $this->isGreaterThan($secondCard)->shouldBe(false);
        $this->isSmallerThan($secondCard)->shouldBe(false);
        $this->isEquals($secondCard)->shouldBe(true);
    }
}
