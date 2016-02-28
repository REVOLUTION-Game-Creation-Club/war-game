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
}
