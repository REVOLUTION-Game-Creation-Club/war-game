Feature: Score the players
  In order to score players
  As a judge
  I need to nominate the winner

  Scenario: One of the players has higher cards
    Given there are two players around the table
    And there are following cards in the deck:
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
    When cards are dealt but not shuffled
    And players play the game
    Then player 2 loses the game
    And player 1 wins the game
    And they played 5 rounds

  Scenario: Players play one single war
    Given there are two players around the table
    And there are following cards in the deck:
      | rank  | suit     | comments             | round |
      | ace   | clubs    | 1st card of player 2 | 1     |
      | ace   | diamonds | 1st card of player 1 | 1     |
      | 3     | clubs    | 2nd card of player 2 | 1     |
      | king  | diamonds | 2nd card of player 1 | 1     |
      | 4     | clubs    | 3rd card of player 2 | 1     |
      | queen | diamonds | 3rd card of player 1 | 1     |
      | 4     | clubs    | 4th card of player 2 | 1     |
      | queen | diamonds | 4th card of player 1 | 1     |
      | 4     | clubs    | 5th card of player 2 | 1     |
      | queen | diamonds | 5th card of player 1 | 1     |
      | 4     | clubs    | 6th card of player 2 | 2     |
      | queen | diamonds | 6th card of player 1 | 2     |
    When cards are dealt but not shuffled
    And players play the game
    Then player 2 loses the game
    And player 1 wins the game
    And they played 2 rounds










#  Scenario: A player wins 5 wars
#    Given there are two players around the table
#    And player 1 has following cards:
#      | rank | suit   |
#      | 2    | spades |
#      | 3    | spades |
#      | 4    | spades |
#      | 5    | spades |
#      | 6    | spades |
#    And player 2 has following cards:
#      | rank  | suit   |
#      | ace   | hearts |
#      | king  | hearts |
#      | queen | hearts |
#      | jack  | hearts |
#      | 10    | hearts |
#    When the first round starts
#    When I lose 5 wars in the game
#    Then I lose the game
