Feature: Play the battles
  In order to win the war game
  As a player
  I need to win battles one by one

  Scenario: One of the two cards is higher than the other
    Given there are two players around the table
    And player 1 receives following cards:
      | rank | suit  |
      | 7    | clubs |
    And player 2 receives following cards:
      | rank | suit  |
      | 5    | clubs |
    When the first round starts
    And players finish to play the round
    Then player 1 wins all 2 cards of the round and puts them, face down, on the bottom of his stack

  Scenario: Single war
    Given there are two players around the table
    And player 1 receives following cards:
      | rank | suit   |
      | 3    | spades |
      | x    |        |
      | x    |        |
      | x    |        |
      | 8    | clubs  |
    And player 2 receives following cards:
      | rank | suit   |
      | ace  | hearts |
      | x    |        |
      | x    |        |
      | x    |        |
      | 8    | hearts |
    When the first round starts
    And it's war
    And players finish to play the war round
    Then player 2 wins all 10 cards of the round and puts them, face down, on the bottom of his stack

  Scenario: Double war
    Given there are two players around the table
    And player 1 receives following cards:
      | rank | suit   |
      | ace  | hearts |
      | x    |        |
      | x    |        |
      | x    |        |
      | 3    | spades |
      | x    |        |
      | x    |        |
      | x    |        |
      | 8    | clubs  |
    And player 2 receives following cards:
      | rank | suit   |
      | king | spades |
      | x    |        |
      | x    |        |
      | x    |        |
      | 3    | hearts |
      | x    |        |
      | x    |        |
      | x    |        |
      | 8    | hearts |
    When the first round starts
    And it's war
    And it's double war
    And players finish to play the war round
    Then player 1 wins all 18 cards of the round and puts them, face down, on the bottom of his stack
