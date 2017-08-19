<?php
namespace LaraRepo\Contracts;

use LaraRepo\Criteria\Criteria;

/**
 * Interface CriteriaInterface
 * @package LaraRepo\Contracts
 */
interface CriteriaInterface
{
    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param Criteria $criteria
     * @return $this
     */

    public function getByCriteria(Criteria $criteria);
    /**
     * @return mixed
     */
    public function resetScope();

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * @return $this
     */
    public function applyCriteria();
}