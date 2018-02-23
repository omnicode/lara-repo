<?php

namespace LaraRepo\Contracts;

interface TransactionInterface
{
    /**
     * @return mixed
     */
    public function startTransaction();

    /**
     * @return mixed
     */
    public function commitTransaction();

    /**
     * @return mixed
     */
    public function rollbackTransaction();
}
