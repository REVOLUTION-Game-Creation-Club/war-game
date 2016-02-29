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

    function it_should_compare_with_a_greater_card()
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $greaterCard = new Card(new Rank(7), Suit::hearts());

        $this->isGreaterThan($greaterCard)->shouldBe(false);
        $this->isSmallerThan($greaterCard)->shouldBe(true);
        $this->isEquals($greaterCard)->shouldBe(false);
    }

    function it_should_compare_with_a_smaller_card()
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $smallerCard = new Card(new Rank(3), Suit::hearts());

        $this->isGreaterThan($smallerCard)->shouldBe(true);
        $this->isSmallerThan($smallerCard)->shouldBe(false);
        $this->isEquals($smallerCard)->shouldBe(false);
    }

    function it_should_compare_with_an_equal_card()
    {
        $this->beConstructedWith(new Rank(5), Suit::hearts());

        $sameRankCard = new Card(new Rank(5), Suit::clovers());

        $this->isGreaterThan($sameRankCard)->shouldBe(false);
        $this->isSmallerThan($sameRankCard)->shouldBe(false);
        $this->isEquals($sameRankCard)->shouldBe(true);
    }
}
