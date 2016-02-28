<?php

namespace spec\WarGame\Domain\Card;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('hearts');
        $this->shouldHaveType('WarGame\Domain\Card\Suit');
    }

    function it_should_create_a_hearts_family()
    {
        $this->beConstructedThrough('hearts');
        $this->getName()->shouldBe('hearts');
    }

    function it_should_get_available_families()
    {
        $this->beConstructedThrough('hearts');
        $this->getSuits()->shouldHaveCount(4);
    }
}
