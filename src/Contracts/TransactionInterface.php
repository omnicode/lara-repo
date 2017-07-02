<?php
namespace LaraRepo\Contracts;


interface TransactionInterface
{
    public function startTransaction();

    public function commitTransaction();

    public function rollbackTransaction();

}