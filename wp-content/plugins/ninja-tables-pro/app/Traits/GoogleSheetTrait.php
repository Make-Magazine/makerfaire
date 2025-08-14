<?php

namespace NinjaTablesPro\App\Traits;

trait GoogleSheetTrait
{
    public function getData($tableId, $url)
    {
        $columns = array();
        foreach (ninja_table_get_table_columns($tableId) as $column) {
            $columns[$column['original_name']] = $column;
        }
        $data = $this->getDataFromUrl($url, $columns);

        if (is_wp_error($data)) {
            return [];
        }

        return array_map(function ($row) use ($columns) {
            $newRow = array();
            foreach ($columns as $key => $column) {
                if (isset($row[$key])) {
                    $newRow[$column['key']] = $row[$key];
                }
            }

            return $newRow;
        }, $data);
    }

    public function getColumns($url)
    {
        $url = $this->sanitizeGoogleUrl($url);
        try {
            $dom = new \DomDocument;
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            $dom->encoding           = 'UTF-8';
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->getRemoteContents($url));
            libxml_clear_errors();
            $xpath = new \DomXPath($dom);

            $columns  = [];
            $firstRow = $xpath->query('//table//tbody//tr')->item(0);
            if ($firstRow) {
                foreach ($firstRow->getElementsByTagName('td') as $index => $node) {
                    $headerName = wp_kses(trim($node->nodeValue), ninja_tables_allowed_html_tags());
                    if (!$headerName) {
                        $headerName = 'nt_header_' . $index;
                    }
                    $columns[$headerName] = $headerName;
                }
            }

            return $columns;
        } catch (\Exception $e) {
            return new \WP_Error(423, $e->getMessage());
        }
    }

    /**
     * Get remote contents using either file_get_contents or curl.
     *
     * @param string $url
     *
     * @return string
     */
    private function getRemoteContents($url)
    {
        return ninjaTablesGetRemoteContent($url);
    }

    private function getDataFromUrl($url, $tableColumns)
    {
        $url = $this->sanitizeGoogleUrl($url);

        $columns = [];
        try {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            $dom->encoding           = 'UTF-8';
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->getRemoteContents($url));
            libxml_clear_errors();
            $xpath   = new \DomXPath($dom);
            $columns = [];
            $allRows = $xpath->query('//table//tbody//tr');

            $firstRow = $allRows->item(0);
            if ($firstRow) {
                foreach ($firstRow->getElementsByTagName('td') as $index => $node) {
                    $headerName = trim($node->nodeValue);
                    if (!$headerName) {
                        $headerName = 'nt_header_' . $index;
                    }

                    if (isset($tableColumns[$headerName])) {
                        $columns[$index] = $headerName;
                    } else {
                        $columns[$index] = false;
                    }
                }
            }
        } catch (\Exception $e) {
            return new \WP_Error(423, $e->getMessage());
        }

        if (!$columns) {
            return new \WP_Error(423, 'No Columns found');
        }

        $result = [];

        $validColumns = array_filter($columns);

        foreach ($allRows as $index => $row) {
            if ($index == 0) {
                continue;
            }
            $newRow = [];

            if (!$row) {
                continue;
            }
            foreach ($row->getElementsByTagName('td') as $columnIndex => $td) {
                if (empty($columns[$columnIndex])) {
                    continue;
                }

                $isHtml = $tableColumns[$columns[$columnIndex]]['data_type'] == 'html';
                if ($isHtml) {
                    $innerHTML = '';
                    $children  = $td->childNodes;
                    if ($children) {
                        foreach ($children as $child) {
                            $innerHTML .= $child->ownerDocument->saveXML($child);
                        }
                    }
                } else {
                    $innerHTML = $td->nodeValue;
                }
                if ($innerHTML != '0' && !$innerHTML) {
                    $innerHTML = ''; // adding empty string
                }
                $newRow[] = $innerHTML;
            }

            if ($this->escapeZero($newRow) && (count($newRow) === count($validColumns))) {
                $sanitizedRow = array_map(function ($rowValue) {
                    return wp_kses($rowValue, ninja_tables_allowed_html_tags());
                }, $newRow);
                $result[]     = array_combine($validColumns, $sanitizedRow);
            }
        }

        return $result;
    }

    public function escapeZero($newRow)
    {
        $status = apply_filters('ninja_tables_google_sheet_escape_zero_value', true);

        if (!$status) {
            return true;
        }

        return array_filter($newRow);
    }

    public function sanitizeGoogleUrl($url)
    {
        if (strpos($url, '/pubhtml/sheet?') !== false) {
            return $url;
        }

        $spreadsheetId = null;
        if (preg_match('/\/spreadsheets\/d\/(?:e\/)?([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $spreadsheetId = $matches[1];
        }

        if (!$spreadsheetId) {
            return $url;
        }

        $gid = '0';
        if (preg_match('/(?:#|\?|&)gid=(\d+)/', $url, $gidMatches)) {
            $gid = $gidMatches[1];
        } elseif (strpos($url, '/pubhtml') !== false) {
            $firstGid = $this->getGidFromURl($url);
            if ($firstGid) {
                $gid = $firstGid;
            }
        }

        $basePath = (strpos($url, 'spreadsheets/d/e') === false) ? "d" : "d/e";

        return "https://docs.google.com/spreadsheets/{$basePath}/{$spreadsheetId}/pubhtml/sheet?headers=false&gid={$gid}";
    }


    public function getGidFromURl($url)
    {
        $html = $this->getRemoteContents($url);

        if (preg_match('/gid: "(\d+)",/', $html, $matches)) {
            return $matches[1];
        }

        return false;
    }
}
