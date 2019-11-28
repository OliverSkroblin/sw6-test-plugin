<?php

namespace TestPlugin\Elasticsearch;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;
use Shopware\Elasticsearch\Framework\FullText;
use Shopware\Elasticsearch\Product\ElasticsearchProductDefinition;

class ProductDefinition extends ElasticsearchProductDefinition
{
    /**
     * @var AbstractElasticsearchDefinition
     */
    private $decorated;

    public function __construct(AbstractElasticsearchDefinition $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getEntityDefinition(): EntityDefinition
    {
        return $this->decorated->getEntityDefinition();
    }

    public function getMapping(Context $context): array
    {
        return $this->decorated->getMapping($context);
    }

    public function extendCriteria(Criteria $criteria): void
    {
        $this->decorated->extendCriteria($criteria);
    }

    public function buildFullText(Entity $entity): FullText
    {
        $fullText = $this->decorated->buildFullText($entity);

        dd($fullText);

        return $fullText;
    }

}
