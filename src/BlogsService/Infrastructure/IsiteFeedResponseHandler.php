<?php
declare(strict_types = 1);
namespace App\BlogsService\Infrastructure;

use App\BlogsService\Domain\IsiteEntity;
use App\BlogsService\Infrastructure\Exception\ParseException;
use App\BlogsService\Mapper\IsiteToDomain\Mapper;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class IsiteFeedResponseHandler
{
    /** @var Mapper */
    private $mapper;

    /** @var XmlParser */
    private $xmlParser;

    public function __construct(Mapper $mapper, XmlParser $xmlParser)
    {
        $this->mapper = $mapper;
        $this->xmlParser = $xmlParser;
    }

    public function getIsiteResult(?ResponseInterface $response): IsiteResult
    {
        if (!$response) {
            return new IsiteResult(0, 0, 0, []);
        }

        $responseBody = $response->getBody()->getContents();

        try {
            $decodedResponseBody = $this->xmlParser->parse($responseBody);
        } catch (ParseException $e) {
            throw new IsiteResultException('Invalid Isite response body.', 0, $e);
        }

        $envelopeName = $decodedResponseBody->getName();

        if ($envelopeName === 'search') {
            return $this->parseSearchEnvelope($decodedResponseBody);
        }

        if ($envelopeName === 'result') {
            return new IsiteResult(1, 1, 1, $this->mapDomainModels([$decodedResponseBody]));
        }

        throw new IsiteResultException(sprintf(
            'Invalid Isite response: (Status: "%s" Body: "%s")',
            $response->getStatusCode(),
            $responseBody
        ));
    }

    private function parseSearchEnvelope(SimpleXMLElement $decodedResponseBody): IsiteResult
    {
        if (!isset($decodedResponseBody->metadata->page)) {
            throw new IsiteResultException('No page information found in result');
        }

        if (!isset($decodedResponseBody->metadata->pageSize)) {
            throw new IsiteResultException('No page size information found in result');
        }

        if (!isset($decodedResponseBody->metadata->totalResults)) {
            throw new IsiteResultException('No total results information found in result');
        }

        $page = (int) $decodedResponseBody->metadata->page;
        $pageSize = (int) $decodedResponseBody->metadata->pageSize;
        $total = (int) $decodedResponseBody->metadata->totalResults;
        $items = $decodedResponseBody->results->xpath('./*');

        return new IsiteResult($page, $pageSize, $total, $this->mapDomainModels($items));
    }

    /**
     * @param SimpleXMLElement[] $items
     * @return IsiteEntity[]
     */
    private function mapDomainModels(array $items): array
    {
        $domainModels = [];
        foreach ($items as $item) {
            $model = $this->mapper->getDomainModel($item);
            if ($model !== null) {
                $domainModels[] = $model;
            }
        }

        return $domainModels;
    }
}
