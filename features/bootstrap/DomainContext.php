<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;

use WarGame\Domain\Card\Card;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Card\Rank;
use WarGame\Domain\Card\Suit;
use WarGame\Domain\Game\Round;
use WarGame\Domain\Game\WarGame;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

/**
 * Defines application features from the specific context.
 */
class DomainContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var WarGame
     */
    private $warGame;

    /**
     * @var Round
     */
    private $round;

    /**
     * @var Table
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
        $this->deck = Deck::frenchDeck();
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
        $this->table = new Table();
        $this->table->welcome(Player::named('Lucas', PlayerId::generate()));
        $this->table->welcome(Player::named('Jeremy', PlayerId::generate()));

        Assert::assertTrue($this->table->isFull());
    }

    /**
     * @When I deal all the cards face down, one at a time
     */
    public function iDealAllTheCardsFaceDownOneAtATime()
    {
        $this->warGame = new WarGame($this->deck, $this->table);
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
        $this->round = new Round();

        Assert::assertSame(0, $this->round->numberOfCardsInTheRound());
    }

    /**
     * @Given a new round has started
     */
    public function aNewRoundHasStarted()
    {
        $this->cardsAreDealt();
        $this->playersAreReady();

        $this->round = new Round();
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
                new Card(new Rank($rank), Suit::$suit())
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
     * @Then each player puts :numberOfCardsFaceDown cards face down and one card face up
     */
    public function eachPlayerPutsCardsFaceDownAndOneCardFaceUp($numberOfCardsFaceDown)
    {
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer1()->putCardsFaceDown($numberOfCardsFaceDown)
        );
        $this->round->playerAddsCardsFaceDown(
            $this->table->getPlayer2()->putCardsFaceDown($numberOfCardsFaceDown)
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
            new Card(new Rank(2), Suit::hearts())
        );
        $this->round->playerAddsCardFaceUp(
            $this->table->getPlayer2()->getId(),
            new Card(new Rank(2), Suit::clubs())
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
