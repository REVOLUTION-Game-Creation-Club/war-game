Feature: Play War Game
  In order to win the war game
  As a player
  I need to win all 52 cards

  Scenario: Deal cards
    Given there is a french deck of 52 cards
    And cards are shuffled
    And there are two players
    When I deal all the cards face down, one at a time
    Then each player has 26 cards

  Scenario: Start playing
    Given cards are dealt
    When players are ready
    Then the game starts

  Scenario: One of the two cards is higher than the other
    Given a new round has started
    When player 1 turns up clovers 7
    And player 2 turns up hearts 5
    Then player 1 wins both cards and puts them, face down, on the bottom of his stack

  Scenario: Cards are the same rank (War)
    Given a new round has started
    When player 1 turns up clovers 8
    And player 2 turns up hearts 8
    Then it's war
    And each player puts 3 cards face down and one card face up

  Scenario: Double war
    Given a new round has started
    And players are in a war
    When each player puts 3 cards face down
    And player 1 turns up pikes 3
    And player 2 turns up hearts 3
    Then it's war
    And each player puts 3 cards face down and one card face up

  Scenario: Single war
    Given a new round has started
    And players are in a war
    When each player puts 3 cards face down
    And player 1 turns up pikes 3
    And player 2 turns up hearts 6
    Then player 2 wins all 10 cards of the round and puts them, face down, on the bottom of his stack

  Scenario: Game is over
    Given I won all 52 cards or I win 5 Wars
    Then I won the game
