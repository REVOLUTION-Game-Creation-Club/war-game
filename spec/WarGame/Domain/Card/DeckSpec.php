<?php

namespace spec\WarGame\Domain\Card;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;

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

    function it_should_add_one_card_to_the_top()
    {
        $this->addToTheTop(new Card(Rank::king(), Suit::clubs()));
        $this->addToTheTop(new Card(Rank::jack(), Suit::hearts()));
        $this->getNbOfCards()->shouldBe(2);
        $this->getCards()->shouldBeLike([
            new Card(Rank::king(), Suit::clubs()),
            new Card(Rank::jack(), Suit::hearts())
        ]);
    }

    function it_should_add_one_card_to_the_bottom()
    {
        $this->addToTheTop(new Card(Rank::king(), Suit::clubs()));
        $this->addToTheTop(new Card(Rank::jack(), Suit::hearts()));
        $this->addToTheBottom(new Card(new Rank(7), Suit::diamonds()));

        $this->getNbOfCards()->shouldBe(3);
        $this->getCards()->shouldBeLike([
            new Card(new Rank(7), Suit::diamonds()),
            new Card(Rank::king(), Suit::clubs()),
            new Card(Rank::jack(), Suit::hearts())
        ]);
    }

    function it_should_pick_one_card_from_the_top_of_the_deck()
    {
        $this->addToTheTop(new Card(new Rank(7), Suit::diamonds()));
        $this->addToTheTop(new Card(Rank::king(), Suit::clubs()));
        $this->addToTheTop(new Card(Rank::jack(), Suit::hearts()));

        $this->getNbOfCards()->shouldBe(3);
        $this->getCards()->shouldBeLike([
            new Card(new Rank(7), Suit::diamonds()),
            new Card(Rank::king(), Suit::clubs()),
            new Card(Rank::jack(), Suit::hearts())
        ]);

        $this->pickFromTheTop()->shouldBeLike(new Card(Rank::jack(), Suit::hearts()));

        $this->getNbOfCards()->shouldBe(2);
        $this->getCards()->shouldBeLike([
            new Card(new Rank(7), Suit::diamonds()),
            new Card(Rank::king(), Suit::clubs())
        ]);
    }

    function it_should_verify_if_deck_is_empty()
    {
        $this->beConstructedWith();
        $this->getNbOfCards()->shouldBe(0);
        $this->isEmpty()->shouldBe(true);
    }
}
