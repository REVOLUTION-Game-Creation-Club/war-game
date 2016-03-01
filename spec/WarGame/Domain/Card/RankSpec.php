<?php

namespace spec\WarGame\Domain\Card;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RankSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(7);
        $this->shouldHaveType('WarGame\Domain\Card\Rank');
    }

    function it_should_have_a_weight()
    {
        $this->beConstructedWith(7);
        $this->getWeight()->shouldBe(7);
    }

    function it_should_create_an_ace()
    {
        $this->beConstructedThrough('ace');
        $this->getWeight()->shouldBe(14);
    }

    function it_should_create_a_king()
    {
        $this->beConstructedThrough('king');
        $this->getWeight()->shouldBe(13);
    }

    function it_should_create_a_queen()
    {
        $this->beConstructedThrough('queen');
        $this->getWeight()->shouldBe(12);
    }

    function it_should_create_a_jack()
    {
        $this->beConstructedThrough('jack');
        $this->getWeight()->shouldBe(11);
    }
}
