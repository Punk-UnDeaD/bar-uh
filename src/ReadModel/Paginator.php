<?php

declare(strict_types=1);

namespace App\ReadModel;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @implements \Iterator<mixed>
 */
class Paginator implements \Countable, \Iterator
{
    private QueryBuilder $query;

    private QueryBuilder $countQuery;

    private ?int $count = null;

    /** @var array<mixed>|null */
    private ?array $data = null;

    private int $pageSize;

    private int $page;

    private int $index = 0;

    /**
     * @var callable|null
     */
    private $callback = null;

    public function __construct(QueryBuilder $queryBuilder, int $page = 1, int $pageSize = 10)
    {
        $this->query = (clone $queryBuilder)->setMaxResults($pageSize)
            ->setFirstResult(($page - 1) * $pageSize);
        $this->countQuery = (clone $queryBuilder)
            ->setMaxResults(null)
            ->setFirstResult(0)
            ->resetQueryPart('orderBy')
            ->select('count(*)');
        $this->pageSize = $pageSize;
        $this->page = $page;
    }

    public function getPageCount(): int
    {
        return (int)ceil($this->count() / $this->pageSize);
    }

    public function count(): int
    {
        return $this->count ?? $this->count = $this->queryExecute($this->countQuery)->fetchOne();
    }

    private function queryExecute(QueryBuilder $query): Result
    {
        /** @var Result $result */
        $result = $query->execute();

        return $result;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function current()
    {
        return $this->getData()[$this->index];
    }

    /**
     * @return array<mixed>
     */
    private function getData(): array
    {
        return $this->data ??
            $this->data = $this->getProcessedData($this->queryExecute($this->query)->fetchAllAssociative());
    }

    /**
     * @param array<mixed> $rows
     *
     * @return array<mixed>
     */
    private function getProcessedData(array $rows): array
    {
        if ($this->callback) {
            $rows = array_map($this->callback, $rows);
        }

        return $rows;
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): int
    {
        return $this->index + 1 + $this->getOffset();
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }

    public function valid(): bool
    {
        return $this->index < count($this->getData());
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

}
