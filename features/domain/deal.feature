Feature: Deal cards
  In order to play the war game
  As a player
  I need to have cards

  Background:
    Given there are two players around the table

  Scenario: Deal cards equally among players
    Given there is a french deck of 52 cards
    And cards are shuffled
    When I deal all the cards face down, one at a time
    Then each player has 26 cards

  Scenario: The deck is divided evenly
    Given there are following cards in the deck:
      | rank  | suit     |
      | 2     | clubs    |
      | ace   | diamonds |
      | 3     | clubs    |
      | king  | diamonds |
      | 4     | clubs    |
      | queen | diamonds |
      | 5     | clubs    |
      | jack  | diamonds |
      | 6     | clubs    |
      | 10    | diamonds |
    When I deal all the cards face down, one at a time
    Then player 1 should have following cards:
      | rank  | suit     |
      | 10    | diamonds |
      | jack  | diamonds |
      | queen | diamonds |
      | king  | diamonds |
      | ace   | diamonds |
    And player 2 should have following cards:
      | rank | suit  |
      | 6    | clubs |
      | 5    | clubs |
      | 4    | clubs |
      | 3    | clubs |
      | 2    | clubs |

  Scenario: Cards are dealt and picked up in the right order
    Given player 1 receives following cards:
      | rank | suit   |
      | 7    | clubs  |
      | king | hearts |
    And player 2 receives following cards:
      | rank  | suit   |
      | 5     | clubs  |
      | queen | spades |
    When players play a battle
    Then following cards should be on the table:
      | rank  | suit   |
      | king  | hearts |
      | queen | spades |
