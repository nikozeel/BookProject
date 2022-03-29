<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\AuthorTranslation;
use App\Entity\Book;
use App\Entity\BookTranslation;
use App\Service\NewAuthorCreate;
use App\Service\NewBookCreate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SandboxCommand extends Command
{
    protected static $defaultName = 'app:sandbox';
    protected static $defaultDescription = 'Add a short description for your command';
    protected EntityManagerInterface $_em;
    protected NewAuthorCreate $newAuthor;
    protected NewBookCreate $newBook;
    const TOTAL_ITEMS_QTY = 10000;

    public function __construct(EntityManagerInterface $_em, NewAuthorCreate $newAuthor, NewBookCreate $newBook)
    {
        $this->_em = $_em;
        $this->newAuthor = $newAuthor;
        $this->newBook = $newBook;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    private function authorAndBookValuesGenerator()
    {
        for ($i = 1; $i <= self::TOTAL_ITEMS_QTY; $i++) {
            $authorNameEn = "Author_" . $i;
            $authorNameRu = "Автор_" . $i;
            $authorNameDe = "Autor_" . $i;
            $result["authorNameEn"] = $authorNameEn;
            $result["authorNameRu"] = $authorNameRu;
            $result["bookTranslations"] = [
                "en" => "Book $i $authorNameEn",
                "ru" => "Книга $i $authorNameRu",
                "de" => "Buchen $i $authorNameDe"
            ];

            yield $result;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, self::TOTAL_ITEMS_QTY);
        $progressBar->start();
        $this->_em->getConnection()->getConfiguration()->setSQLLogger(null);
        $i = 0;
        foreach ($this->authorAndBookValuesGenerator() as $item) {
            $newAuthor = $this->newAuthor->create($item["authorNameEn"], $item["authorNameRu"]);
            $this->newBook->create($newAuthor, $item["bookTranslations"]);
            $i++;
            if ($i > self::TOTAL_ITEMS_QTY / 10) {
                $this->_em->flush();
                $this->_em->clear();
                $i = 0;
            }
            $progressBar->advance();
        }
        $this->_em->flush();
        $this->_em->clear();
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
