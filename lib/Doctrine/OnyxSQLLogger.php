<?php

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Includes executed SQLs in a Debug Stack.
 */
class OnyxSQLLogger implements SQLLogger {
    /**
     * Executed SQL queries.
     * @var array<int, array<string, mixed>>
     */
    public $queries = [];

    /**
     * If Debug Stack is enabled (log queries) or not.
     * @var bool
     */
    public $enabled = true;

    /** @var float|null */
    public $start = null;

    /** @var int */
    public $currentQuery = 0;

    /** @var float */
    public $totalExecutionMS = 0;

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        if (!$this->enabled) {
            return;
        }

        $this->start = microtime(true);
        $this->queries[++$this->currentQuery] = [
            'sql'         => $sql,
            'params'      => $params,
            'types'       => $types,
            'executionMS' => 0,
            'startMS'     => $this->start,
            'endMS'       => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if (!$this->enabled) {
            return;
        }

        $end = microtime(true);
        $this->queries[$this->currentQuery]['executionMS'] = $end - $this->start;
        $this->queries[$this->currentQuery]['endMS'] = $end;
        $this->totalExecutionMS += $end - $this->start;
    }
}
