<?php declare(strict_types=1);

namespace TestPlugin\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\DataAbstractionLayer\Command\ConsoleProgressTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TestPlugin\Elasticsearch\MyIndexer;

class MyEsIndexCommand extends Command implements EventSubscriberInterface
{
    use ConsoleProgressTrait;

    protected static $defaultName = 'my:es:index';

    /**
     * @var MyIndexer
     */
    private $indexer;

    public function __construct(MyIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Reindex all entities to elasticsearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new ShopwareStyle($input, $output);

        $this->indexer->index();

        return 0;
    }
}
