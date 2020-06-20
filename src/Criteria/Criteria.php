<?php

namespace Tystr\RedisOrm\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Criteria implements CriteriaInterface
{
    /**
     * @var Collection
     */
    protected $restrictions;

    /**
     * @param Collection $restrictions
     */
    public function __construct(Collection $restrictions = null)
    {
        if (null === $restrictions) {
            $restrictions = new ArrayCollection();
        }
        $this->restrictions = $restrictions;
    }

    /**
     * @return array|Collection|Restriction[]
     */
    public function getRestrictions(): array
    {
        return $this->restrictions;
    }

    /**
     * @param Collection $restrictions
     */
    public function setRestrictions(Collection $restrictions): void
    {
        $this->restrictions = $restrictions;
    }

    /**
     * @param RestrictionInterface $restriction
     */
    public function addRestriction(RestrictionInterface $restriction): void
    {
        $this->restrictions->add($restriction);
    }

    /**
     * @param RestrictionInterface $restriction
     */
    public function removeRestriction(RestrictionInterface $restriction): void
    {
        $this->restrictions->removeElement($restriction);
    }

    /**
     * @param RestrictionInterface $expectedRestriction
     *
     * @return bool
     */
    public function hasRestriction(RestrictionInterface $expectedRestriction): bool
    {
        foreach ($this->restrictions as $restriction) {
            if ($restriction->equals($expectedRestriction)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $keyGenerator = new RestrictionsKeyGenerator();
        return $keyGenerator->getKeyName($this->getRestrictions()->toArray());
    }
}
