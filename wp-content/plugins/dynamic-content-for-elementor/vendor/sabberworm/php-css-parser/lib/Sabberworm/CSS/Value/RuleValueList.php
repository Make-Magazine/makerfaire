<?php

namespace DynamicOOOS\Sabberworm\CSS\Value;

class RuleValueList extends ValueList
{
    public function __construct($sSeparator = ',', $iLineNo = 0)
    {
        parent::__construct(array(), $sSeparator, $iLineNo);
    }
}
