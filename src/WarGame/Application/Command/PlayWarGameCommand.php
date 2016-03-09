<?php

namespace WarGame\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarGame\Domain\Card\Deck;
use WarGame\Domain\Game\WarGame;
use WarGame\Domain\Player\Dealer;
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
        if ($input->getOption('anonymous')) {
            $nameOfPlayer1 = 'Player 1';
            $nameOfPlayer2 = 'Player 2';
        } else {
            $questionHelper = $this->getHelper('question');
            $nameOfPlayer1 = $questionHelper->ask($input, $output, new Question('Who is the first player? > ', 'Player 1'));
            $nameOfPlayer2 = $questionHelper->ask($input, $output, new Question('Who is the second player? > ', 'Player 2'));
        }

        $table = new Table();

        $player1 = Player::named($nameOfPlayer1, PlayerId::generate());
        $table->welcome($player1);

        $player2 = Player::named($nameOfPlayer2, PlayerId::generate());
        $table->welcome($player2);

        $deck = Deck::frenchDeck();
        $deck->shuffle();

        $dealer = new Dealer($deck, $table);
        $dealer->dealCardsOneByOne();

        $warGame = new WarGame($table);

        foreach ($warGame->getBattles() as $battleNumber => $playedBattle) {
            $output->writeln(
                sprintf(
                    'Battle n°%d : %s won %d cards',
                    $battleNumber,
                    $playedBattle->getWinner()->getName(),
                    $playedBattle->numberOfCardsInTheBattle()
                )
            );
        }

        $output->writeln(
            sprintf(
                '%s won the game in %d battles',
                $warGame->getWinner()->getName(),
                count($warGame->getBattles())
            )
        );
    }
}
