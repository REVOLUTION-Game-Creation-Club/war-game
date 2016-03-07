# War Game

A PHP project to demonstrate how to do Domain Driven Development using BDD (Behat for stories and PHPSpec for specs).

## How to play war game

The objective of the game is to win all cards.

### The Deal
The deck is divided evenly, with each player receiving 26 cards, dealt one at a time, face down. Each player places his deck of cards face down, in front of him.

### The Play
Each player turns up a card at the same time (this is a battle) and the player with the higher card takes both cards and puts them, face down, on the bottom of his stack.

If the cards are the same rank, it is War. Each player turns up three cards face down and one card face up. The player with the higher cards takes both piles (ten cards). If the turned-up cards are again the same rank, each player places other three cards face down and turns another card face up. The player with the higher card takes all 18 cards, and so on.

### How to Keep Score
The game ends when one player has won all the cards or one player won 5 wars.

*Since there are no choices in the game, and all outcomes are random, it cannot really be considered a game by some definitions.*

## Installation

Get [composer](https://getcomposer.org/)

    composer install

## Usage

    php wargame.php play

## Tests

    ./vendor/bin/phpspec run
    ./vendor/bin/behat
