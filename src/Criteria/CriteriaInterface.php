<?php

namespace Tystr\RedisOrm\Criteria;

use Doctrine\Common\Collections\Collection;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface CriteriaInterface
{
    /**
     * @return array|Collection|Restriction[]
     */
    public function getRestrictions(): array;

    /**
     * @param Collection $restrictions
     */
    public function setRestrictions(Collection $restrictions): void;

    /**
     * @param RestrictionInterface $restriction
     */
    public function addRestriction(RestrictionInterface $restriction): void;

    /**
     * @param RestrictionInterface $restriction
     */
    public function removeRestriction(RestrictionInterface $restriction): void;

    /**
     * @param RestrictionInterface $restriction
     * @return bool
     */
    public function hasRestriction(RestrictionInterface $restriction): bool;

    /**
     * @return string
     */
    public function __toString(): string;
}
