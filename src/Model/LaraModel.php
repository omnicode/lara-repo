<?php
namespace LaraRepo\Model;

use Illuminate\Database\Eloquent\Model;
use LaraRepo\Builder\LaraQueryBuilder;

abstract class LaraModel extends Model
{
    /**
     * @return LaraQueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new LaraQueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

}
