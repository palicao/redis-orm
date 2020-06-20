<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
class RestrictionsKeyGenerator
{
    /**
     * @param array $restrictions
     *
     * @return string
     */
    public function getKeyName(array $restrictions): string
    {
        $string = '';
        foreach ($restrictions as $restriction) {
            $value = $restriction->getValue();
            $finalValue = '';

            if (is_iterable($value)) {
                $finalValue .= '('.$this->getKeyName($value).')';
            } else {
                $finalValue = $restriction->getValue();
            }

            $string .= sprintf(
                '%s %s %s, ',
                $restriction->getKey(),
                get_class($restriction),
                $finalValue
            );
        }
        return rtrim($string,', ');
    }
}
