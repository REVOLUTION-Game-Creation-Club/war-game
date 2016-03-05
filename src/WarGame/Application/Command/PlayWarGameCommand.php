<?php

namespace WarGame\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Game\WarGame;
use WarGame\Domain\Player\Player;
use WarGame\Domain\Player\PlayerId;
use WarGame\Domain\Player\Table;

class PlayWarGameCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('play')
            ->setDescription('Play a war game')
            ->addOption(
                'anonymous',
                null,
                InputOption::VALUE_NONE,
                'If set, the game will not ask to name the players'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table();

        if ($input->getOption('anonymous')) {
            $nameOfPlayer1 = 'Player 1';
            $nameOfPlayer2 = 'Player 2';
        } else {
            $questionHelper = $this->getHelper('question');
            $nameOfPlayer1 = $questionHelper->ask($input, $output, new Question('Who is the first player? > ', 'Player 1'));
            $nameOfPlayer2 = $questionHelper->ask($input, $output, new Question('Who is the second player? > ', 'Player 2'));
        }

        $player1 = Player::named($nameOfPlayer1, PlayerId::generate());
        $table->welcome($player1);

        $player2 = Player::named($nameOfPlayer2, PlayerId::generate());
        $table->welcome($player2);

        $deck = Deck::frenchDeck();
        $deck->shuffle();

        $warGame = new WarGame($deck, $table);
        $warGame->dealCards();
        $warGame->play();

        foreach ($warGame->getRounds() as $roundNumber => $playedRound) {
            $output->writeln(
                sprintf(
                    'Round n°%d : %s won %d cards',
                    $roundNumber,
                    $playedRound->getWinner()->getName(),
                    $playedRound->numberOfCardsInTheRound()
                )
            );
        }

        $output->writeln(
            sprintf(
                '%s won the game in %d rounds',
                $warGame->getWinner()->getName(),
                count($warGame->getRounds())
            )
        );
    }
}
