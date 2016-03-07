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
    Then player 1 wins the game
    And they played 5 battles

  Scenario: Players play one single war
    Given there are two players around the table
    And there are following cards in the deck:
      | rank  | suit     | comments             |
      | ace   | clubs    | 1st card of player 2 |
      | ace   | diamonds | 1st card of player 1 |
      | x     | x        | 2nd card of player 2 |
      | x     | x        | 2nd card of player 1 |
      | x     | x        | 3rd card of player 2 |
      | x     | x        | 3rd card of player 1 |
      | x     | x        | 4th card of player 2 |
      | x     | x        | 4th card of player 1 |
      | 4     | clubs    | 5th card of player 2 |
      | queen | diamonds | 5th card of player 1 |
    When cards are dealt but not shuffled
    And players play the game
    Then player 1 wins the game
    And they played 1 battles

  Scenario: Players play one double war
    Given there are two players around the table
    And there are following cards in the deck:
      | rank | suit     | comments             |
      | ace  | clubs    | 1st card of player 2 |
      | ace  | diamonds | 1st card of player 1 |
      | x    | x        | 2nd card of player 2 |
      | x    | x        | 2nd card of player 1 |
      | x    | x        | 3rd card of player 2 |
      | x    | x        | 3rd card of player 1 |
      | x    | x        | 4th card of player 2 |
      | x    | x        | 4th card of player 1 |
      | 4    | clubs    | 5th card of player 2 |
      | 4    | diamonds | 5th card of player 1 |
      | x    | x        | 6th card of player 2 |
      | x    | x        | 6th card of player 1 |
      | x    | x        | 7th card of player 2 |
      | x    | x        | 7th card of player 1 |
      | x    | x        | 8th card of player 2 |
      | x    | x        | 8th card of player 1 |
      | 5    | clubs    | 9th card of player 2 |
      | 8    | diamonds | 9th card of player 1 |
    When cards are dealt but not shuffled
    And players play the game
    Then player 1 wins the game
    And they played 1 battles

  Scenario: A player wins 5 wars
    Given there are two players around the table
    And there are following cards in the deck:
      | rank  | suit     | comments                                      |
      | ace   | clubs    | 1st card of player 2 => 1st war               |
      | ace   | diamonds | 1st card of player 1                          |
      | x     | x        | 2nd card of player 2                          |
      | x     | x        | 2nd card of player 1                          |
      | x     | x        | 3rd card of player 2                          |
      | x     | x        | 3rd card of player 1                          |
      | x     | x        | 4th card of player 2                          |
      | x     | x        | 4th card of player 1                          |
      | 8     | clubs    | 5th card of player 2 => player 2 wins the war |
      | 4     | diamonds | 5th card of player 1                          |
      | king  | clubs    | 6th card of player 2 => 2nd war               |
      | king  | diamonds | 6th card of player 1                          |
      | x     | x        | 7th card of player 2                          |
      | x     | x        | 7th card of player 1                          |
      | x     | x        | 8th card of player 2                          |
      | x     | x        | 8th card of player 1                          |
      | x     | x        | 9th card of player 2                          |
      | x     | x        | 9th card of player 1                          |
      | 8     | clubs    | etc => player 2 wins the war                  |
      | 4     | diamonds | etc                                           |
      | queen | clubs    | etc => 3rd war                                |
      | queen | diamonds | etc                                           |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | 8     | clubs    | => player 2 wins the war                      |
      | 4     | diamonds |                                               |
      | jack  | clubs    | 4th war                                       |
      | jack  | diamonds |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | 8     | clubs    | => player 2 wins the war                      |
      | 4     | diamonds |                                               |
      | jack  | clubs    | 5th war                                       |
      | jack  | diamonds |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | x     | x        |                                               |
      | 8     | clubs    | => player 2 wins the war                      |
      | 4     | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
      | 2     | clubs    |                                               |
      | ace   | diamonds |                                               |
    When cards are dealt but not shuffled
    And players play the game
    Then player 2 wins the game
    And they played 5 battles
