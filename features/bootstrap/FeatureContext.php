<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \WarGame\Domain\Card\Deck
     */
    private $deck;

    /**
     * @var \WarGame\Domain\Game\WarGame
     */
    private $warGame;

    /**
     * @var \WarGame\Domain\Game\Round
     */
    private $round;

    /**
     * @var \WarGame\Domain\Player\Table
     */
    private $table;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given there is a french deck of 52 cards
     */
    public function thereIsAFrenchDeck()
    {
        $this->deck = \WarGame\Domain\Card\Deck::frenchDeck();
    }

    /**
     * @Given cards are shuffled
     */
    public function cardsAreShuffled()
    {
        $this->deck->shuffle();
    }

    /**
     * @Given there are two players
     */
    public function thereAreTwoPlayers()
    {
        $this->table = new \WarGame\Domain\Player\Table();
        $this->table->welcome(\WarGame\Domain\Player\Player::named('Lucas'));
        $this->table->welcome(\WarGame\Domain\Player\Player::named('Jeremy'));

        Assert::assertTrue($this->table->isFull());
    }

    /**
     * @When I deal all the cards face down, one at a time
     */
    public function iDealAllTheCardsFaceDownOneAtATime()
    {
        $this->warGame = new \WarGame\Domain\Game\WarGame($this->deck, $this->table);
        $this->warGame->dealCards();
    }

    /**
     * @Then each player has :nbOfCards cards
     */
    public function eachPlayerHasCards($nbOfCards)
    {
        Assert::assertSame($this->table->getPlayer1()->getNbOfCards(), intval($nbOfCards));
        Assert::assertSame($this->table->getPlayer2()->getNbOfCards(), intval($nbOfCards));
    }

    /**
     * @Given cards are dealt
     */
    public function cardsAreDealt()
    {
        $this->thereIsAFrenchDeck();
        $this->cardsAreShuffled();
        $this->thereAreTwoPlayers();
        $this->iDealAllTheCardsFaceDownOneAtATime();

        Assert::assertTrue($this->deck->isEmpty());
    }

    /**
     * @When players are ready
     */
    public function playersAreReady()
    {
        $this->table->getPlayer1()->readyToStart();
        $this->table->getPlayer2()->readyToStart();
    }

    /**
     * @Then the game starts
     */
    public function theGameStarts()
    {
        $this->round = new \WarGame\Domain\Game\Round();

        Assert::assertSame(0, $this->round->numberOfCardsInTheRound());
    }

    /**
     * @Given a new round has started
     */
    public function aNewRoundHasStarted()
    {
        $this->cardsAreDealt();
        $this->playersAreReady();

        $this->round = new \WarGame\Domain\Game\Round();
    }

    /**
     * @When player :playerNumber turns up :suit :rank
     */
    public function playerTurnsUpCard($playerNumber, $suit, $rank)
    {
        $player = intval($playerNumber) === 1 ? $this->table->getPlayer1() : $this->table->getPlayer2();

        $this->round
            ->playerAddsCardFaceUp(
                $player->getId(),
                new \WarGame\Domain\Card\Card(new \WarGame\Domain\Card\Rank($rank), \WarGame\Domain\Card\Suit::$suit())
            );
    }

    /**
     * @Then player :playerNumber wins both cards and puts them, face down, on the bottom of his stack
     */
    public function playerWinsBothCardsAndPutsThemFaceDownOnTheBottomOfHisStack($playerNumber)
    {
        $playerId = $this->round->resolveWinner();

        $winner = $this->table->get($playerId);

        $nbOfCardsBeforeRoundIsFinished = $winner->getNbOfCards();

        $winner->wins($this->round->wonCards());

        Assert::assertSame($winner, intval($playerNumber) === 1 ? $this->table->getPlayer1() : $this->table->getPlayer2());
        Assert::assertSame($winner->getNbOfCards(), $nbOfCardsBeforeRoundIsFinished + 2);
    }

    /**
     * @Then it's war
     */
    public function itSWar()
    {
        try {
            $this->round->resolveWinner();

            Assert::fail('Players are not in war.');
        } catch (\WarGame\Domain\Game\War $e) {}
    }

    /**
     * @Then each player puts :numberOfCards cards face down and one card face up
     */
    public function eachPlayerPutsCardsFaceDownAndOneCardFaceUp($numberOfCards)
    {
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer1()->putCardsFaceDown($numberOfCards)
        );
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer2()->putCardsFaceDown($numberOfCards)
        );

        $this->round->playerAddsCardFaceUp(
            $this->table->getPlayer1()->getId(),
            $this->table->getPlayer1()->putOneCardUp()
        );

        $this->round->playerAddsCardFaceUp(
            $this->table->getPlayer2()->getId(),
            $this->table->getPlayer2()->putOneCardUp()
        );
    }

    /**
     * @Given players are in a war
     */
    public function playersAreInAWar()
    {
        $this->round->playerAddsCardFaceUp(
            $this->table->getPlayer1()->getId(),
            new WarGame\Domain\Card\Card(new \WarGame\Domain\Card\Rank(2), \WarGame\Domain\Card\Suit::hearts())
        );
        $this->round->playerAddsCardFaceUp(
            $this->table->getPlayer2()->getId(),
            new WarGame\Domain\Card\Card(new \WarGame\Domain\Card\Rank(2), \WarGame\Domain\Card\Suit::clovers())
        );

        try {
            $this->round->resolveWinner();
        } catch (\WarGame\Domain\Game\War $e) {}
    }

    /**
     * @When each player puts :numberOfCards cards face down
     */
    public function eachPlayerPutsCardsFaceDown($numberOfCards)
    {
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer1()->putCardsFaceDown($numberOfCards)
        );
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer2()->putCardsFaceDown($numberOfCards)
        );
    }

    /**
     * @Then player :playerNumber wins all :numberOfCards cards of the round and puts them, face down, on the bottom of his stack
     */
    public function playerWinsAllCardsOfTheRoundAndPutsThemFaceDownOnTheBottomOfHisStack2($playerNumber, $numberOfCards)
    {
        try {
            $winnerId = $this->round->resolveWinner();

            $winner = $this->table->get($winnerId);
            $nbOfCardsBeforeRoundIsFinished = $winner->getNbOfCards();

            Assert::assertSame(intval($numberOfCards), $this->round->numberOfCardsInTheRound());
            Assert::assertSame($winner, intval($playerNumber) === 1 ? $this->table->getPlayer1() : $this->table->getPlayer2());

            $winner->wins($this->round->wonCards());

            Assert::assertSame($winner->getNbOfCards(), $nbOfCardsBeforeRoundIsFinished + intval($numberOfCards));
        } catch (\WarGame\Domain\Game\War $e) {
            Assert::fail('Players should not be in war again.');
        }
    }

    /**
     * @Given I won all :arg1 cards or I win :arg2 Wars
     */
    public function iWonAllCardsOrIWinWars($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then I won the game
     */
    public function iWonTheGame()
    {
        throw new PendingException();
    }
}