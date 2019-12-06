<?php

namespace TestPlugin\Elasticsearch;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;
use Shopware\Elasticsearch\Product\ElasticsearchProductDefinition;

class MyIndexer
{
    /**
     * @var ElasticsearchProductDefinition
     */
    private $productDefinition;

    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        ElasticsearchProductDefinition $productDefinition,
        EntityRepositoryInterface $productRepository
    ) {
        $this->productDefinition = $productDefinition;
        $this->productRepository = $productRepository;
    }


    public function index()
    {
        $criteria = new Criteria();
        $criteria->setLimit(10);
        $criteria->addFilter(new EqualsFilter('productNumber', 'cd9b9478e6d44704b3de9330f7ef4145'));

        $this->productDefinition->extendCriteria($criteria);

        $context = Context::createDefaultContext();
        $products = $context->disableCache(function(Context $context) use ($criteria) {
            return $this->productRepository->search($criteria, $context);
        });


        $docs = $this->createDocuments($this->productDefinition, $products->getEntities());

        dd($docs[1]['listingPrices']);
    }

    private function createDocuments(AbstractElasticsearchDefinition $definition, iterable $entities): array
    {
        $documents = [];

        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $documents[] = ['index' => ['_id' => $entity->getUniqueIdentifier()]];

            $document = json_decode(json_encode($entity, JSON_PRESERVE_ZERO_FRACTION), true);

            $fullText = $definition->buildFullText($entity);

            $document['fullText'] = $fullText->getFullText();
            $document['fullTextBoosted'] = $fullText->getBoosted();

            $document = $definition->convertDocument($document, $entity);

            $documents[] = $document;
        }

        return $documents;
    }
}
