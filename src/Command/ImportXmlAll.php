<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Logger\ConsoleLogger;

class ImportXmlAll extends Command
{
    private const XML_URL = 'https://habr.com/ru/rss/all/all/';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('import:all')
             ->setDescription('Import posts from rss.')
             ->setHelp('This command allows you to import all posts from rss...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data       = file_get_contents(self::XML_URL);
        $xmls       = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $items      = $xmls->channel->item;
        $repository = $this->entityManager->getRepository(Post::class);

        if ($items !== null && $repository->resetPosts()) {
            $posts = $repository->findAllLinks();

            foreach ($items as $item) {
                if (array_search($item->guid, array_column($posts, 'link')) === false) {
                    $pubDate = new DateTimeImmutable($item->pubDate);

                    $post = new Post();

                    $post->setTitle($item->title);
                    $post->setDescription($item->description);
                    $post->setLink($item->guid);
                    $post->setPubDate($pubDate);
                    $post->setCategory((array)$item->category);

                    $this->entityManager->persist($post);
                    $this->entityManager->flush();
                }
            }

            $this->importDaily($output);
            $this->importWeek($output);
            $this->importMonth($output);
            $this->importYear($output);
        }
    }

    /**
     * Set daily checkbox
     *
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    private function importDaily(OutputInterface $output)
    {
        $command      = $this->getApplication()->find('import:daily');
        $arguments    = ['command' => 'import:daily'];
        $commandInput = new ArrayInput($arguments);

        return $command->run($commandInput, $output);
    }

    /**
     * Set week checkbox
     *
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    private function importWeek(OutputInterface $output)
    {
        $command      = $this->getApplication()->find('import:week');
        $arguments    = ['command' => 'import:week'];
        $commandInput = new ArrayInput($arguments);

        return $command->run($commandInput, $output);
    }

    /**
     * Set month checkbox
     *
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    private function importMonth(OutputInterface $output)
    {
        $command      = $this->getApplication()->find('import:month');
        $arguments    = ['command' => 'import:month'];
        $commandInput = new ArrayInput($arguments);

        return $command->run($commandInput, $output);
    }

    /**
     * Set year checkbox
     *
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    private function importYear(OutputInterface $output)
    {
        $command      = $this->getApplication()->find('import:year');
        $arguments    = ['command' => 'import:year'];
        $commandInput = new ArrayInput($arguments);

        return $command->run($commandInput, $output);
    }
}