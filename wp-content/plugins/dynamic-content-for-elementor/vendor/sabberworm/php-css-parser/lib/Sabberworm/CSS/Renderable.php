<?php

namespace DynamicOOOS\Sabberworm\CSS;

interface Renderable
{
    public function __toString();
    public function render(\DynamicOOOS\Sabberworm\CSS\OutputFormat $oOutputFormat);
    public function getLineNo();
}
