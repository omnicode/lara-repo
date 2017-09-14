<?php
namespace LaraRepo\Model;

use Illuminate\Database\Eloquent\Model;
use LaraRepo\Builder\LaraEloquentBuilder;
use LaraRepo\Builder\LaraQueryBuilder;
use LaraTools\Models\Traits\ModelExtrasTrait;

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

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return LaraEloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new LaraEloquentBuilder($query);
    }

}
