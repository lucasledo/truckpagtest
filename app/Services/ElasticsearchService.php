<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('elasticsearch.hosts')[0]])
            ->build();
    }

    public function indexProduct(array $product)
    {
        return $this->client->index([
            'index' => 'products',
            'id'    => $product['id'],
            'body'  => $product
        ]);
    }

    public function searchProducts(string $query)
    {
        return $this->client->search([
            'index' => 'products',
            'body'  => [
                'query' => [
                    'match' => [
                        'product_name' => $query
                    ]
                ]
            ]
        ]);
    }
}
