<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class ImportXmlYear extends Command
{
    private const XML_URL = 'https://habr.com/ru/rss/best/yearly/';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('import:year')
             ->setDescription('Import posts from rss.')
             ->setHelp('This command allows you to import year posts...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data  = file_get_contents(self::XML_URL);
        $xmls  = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $items = $xmls->channel->item;

        if ($items !== null) {
            $repository = $this->entityManager->getRepository(Post::class);
            $posts      = $repository->findAll();
            $arrayPosts = $repository->findAllInArray();

            foreach ($items as $item) {
                $searchResult = array_search($item->guid, array_column($arrayPosts, 'link'));

                if ($searchResult !== false) {
                    $posts[$searchResult]->setIsYear(true);

                    $this->entityManager->persist($posts[$searchResult]);
                    $this->entityManager->flush();
                } else {
                    $pubDate = new DateTimeImmutable($item->pubDate);

                    $post = new Post();

                    $post->setTitle($item->title);
                    $post->setDescription($item->description);
                    $post->setLink($item->guid);
                    $post->setPubDate($pubDate);
                    $post->setCategory((array)$item->category);
                    $post->setIsYear(true);

                    $this->entityManager->persist($post);
                    $this->entityManager->flush();
                }
            }
        }
    }
}