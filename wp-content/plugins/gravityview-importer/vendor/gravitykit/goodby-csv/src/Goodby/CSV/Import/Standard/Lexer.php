<?php

namespace Goodby\CSV\Import\Standard;

use Goodby\CSV\Import\Protocol\LexerInterface;
use Goodby\CSV\Import\Protocol\InterpreterInterface;
use Goodby\CSV\Import\Standard\StreamFilter\ConvertMbstringEncoding;
use SplFileObject;

class Lexer implements LexerInterface
{
    /**
     * @var LexerConfig
     */
    private $config;

    /**
     * Return new Lexer object
     * @param LexerConfig $config
     */
    public function __construct(LexerConfig $config = null)
    {
        if (!$config) {
            $config = new LexerConfig();
        }

        $this->config = $config;
        ConvertMbstringEncoding::register();
    }

    /**
     * {@inherit}
     */
    public function parse($filename, InterpreterInterface $interpreter)
    {
        if (!version_compare(PHP_VERSION, '8.1.0', '>=')) {
            ini_set('auto_detect_line_endings', true); // For mac's office excel csv
        }

        $delimiter      = $this->config->getDelimiter();
        $enclosure      = $this->config->getEnclosure();
        $escape         = $this->config->getEscape();
        $fromCharset    = $this->config->getFromCharset();
        $toCharset      = $this->config->getToCharset();
        $flags          = $this->config->getFlags();
        $ignoreHeader   = $this->config->getIgnoreHeaderLine();

        if ( $fromCharset === null ) {
            $url = $filename;
        } else {
            $url = ConvertMbstringEncoding::getFilterURL($filename, $fromCharset, $toCharset);
        }

        $csv = new SplFileObject($url);
        $csv->setCsvControl($delimiter, $enclosure, $escape);
        $csv->setFlags($flags);

        // Backup originals.
        $prev_numeric = setlocale( LC_NUMERIC, '0' );
        $prev_ctype   = setlocale( LC_CTYPE,   '0' );
        $prev_time    = setlocale( LC_TIME,    '0' );
        
        // Safe candidates (first supported wins).
        $candidates = array( 'en_US.UTF-8', 'en_US', 'C', 'POSIX' );
        
        // Helper to call setlocale with a category + candidate list (no PHP 8 features).
        // Using a closure to avoid global function redeclaration issues
        $setlocale_try_candidates = function( $category, array $candidates ) {
            $args = array_merge( array( $category ), $candidates );
            $ok   = @call_user_func_array( 'setlocale', $args );
            if ( false === $ok ) {
                @setlocale( $category, 'C' ); // last-ditch safe fallback
            }
        };
        
        // Apply to the categories a CSV importer typically needs.
        $setlocale_try_candidates( LC_NUMERIC, $candidates ); // numbers (decimal sep)
        $setlocale_try_candidates( LC_CTYPE,   $candidates ); // character classes/case
        $setlocale_try_candidates( LC_TIME,    $candidates ); // localized dates (if any)

        foreach ( $csv as $lineNumber => $line ) {
            if ($ignoreHeader && $lineNumber == 0 || (count($line) === 1 && trim($line[0]) === '')) {
                continue;
            }
            $interpreter->interpret($line);
        }

        // Restore originals exactly.
        if ( is_string( $prev_numeric ) && $prev_numeric !== '' ) {
            @setlocale( LC_NUMERIC, $prev_numeric );
        }
        if ( is_string( $prev_ctype ) && $prev_ctype !== '' ) {
            @setlocale( LC_CTYPE, $prev_ctype );
        }
        if ( is_string( $prev_time ) && $prev_time !== '' ) {
            @setlocale( LC_TIME, $prev_time );
        }
    }
}
