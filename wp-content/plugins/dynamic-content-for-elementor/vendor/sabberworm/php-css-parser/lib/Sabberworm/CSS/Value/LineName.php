<?php

namespace DynamicOOOS\Sabberworm\CSS\Value;

use DynamicOOOS\Sabberworm\CSS\Parsing\ParserState;
use DynamicOOOS\Sabberworm\CSS\Parsing\UnexpectedTokenException;
class LineName extends ValueList
{
    public function __construct($aComponents = array(), $iLineNo = 0)
    {
        parent::__construct($aComponents, ' ', $iLineNo);
    }
    public static function parse(ParserState $oParserState)
    {
        $oParserState->consume('[');
        $oParserState->consumeWhiteSpace();
        $aNames = array();
        do {
            if ($oParserState->getSettings()->bLenientParsing) {
                try {
                    $aNames[] = $oParserState->parseIdentifier();
                } catch (UnexpectedTokenException $e) {
                }
            } else {
                $aNames[] = $oParserState->parseIdentifier();
            }
            $oParserState->consumeWhiteSpace();
        } while (!$oParserState->comes(']'));
        $oParserState->consume(']');
        return new LineName($aNames, $oParserState->currentLine());
    }
    public function __toString()
    {
        return $this->render(new \DynamicOOOS\Sabberworm\CSS\OutputFormat());
    }
    public function render(\DynamicOOOS\Sabberworm\CSS\OutputFormat $oOutputFormat)
    {
        return '[' . parent::render(\DynamicOOOS\Sabberworm\CSS\OutputFormat::createCompact()) . ']';
    }
}
