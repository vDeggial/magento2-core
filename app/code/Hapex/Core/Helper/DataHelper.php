<?php

namespace Hapex\Core\Helper;

use Magento\Store\Model\ScopeInterface;

class DataHelper extends BaseHelper
{

    public function getConfigFlag($path = null, $scopeCode = null)
    {
        $isSetFlag = false;
        try {
            $isSetFlag = $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $scopeCode);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $isSetFlag = false;
        } finally {
            return $isSetFlag;
        }
    }

    public function getConfigValue($path = null, $scopeCode = null)
    {
        $value = null;
        try {
            $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $scopeCode);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $value = null;
        } finally {
            return $value;
        }
    }

    public function getNameCase($name = null)
    {
        $properName = $name;
        try {
            // A list of properly cased parts
            $properCases = [
                "O'",
                "l'",
                "d'",
                "St.",
                "Mc",
                "the",
                "van",
                "het",
                "in",
                "'t",
                "ten",
                "den",
                "von",
                "und",
                "der",
                "de",
                "da",
                "of",
                "and",
                "the",
                "II",
                "III",
                "IV",
                "VI",
                "VII",
                "VIII",
                "IX",
            ];

            // Trim whitespace sequences to one space, append space to properly chunk
            $name = preg_replace("/\s+/", " ", $name) . " ";

            // Break name up into parts split by name separators
            $parts = preg_split("/( |-|O\'|l\'|d\'|St\\.|Mc|\()/", $name, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Chunk parts, use $properCases or uppercase first, remove unfinished chunks
            $parts = array_chunk($parts, 2);
            $parts = array_filter($parts, function ($part) {
                return count($part) == 2;
            });
            $parts = array_map(function ($part) use ($properCases) {
                // Extract to name and separator part
                list($name, $separator) = $part;

                // Use specified case for separator if set
                $cased = current(array_filter($properCases, function ($case) use ($separator) {
                    return strcasecmp($case, $separator) == 0;
                }));
                $separator = $cased ? $cased : $separator;

                // Choose specified part case, or uppercase first as default
                $cased = current(array_filter($properCases, function ($case) use ($name) {
                    return strcasecmp($case, $name) == 0;
                }));
                return [$cased ? $cased : ucfirst(strtolower($name)), $separator];
            }, $parts);
            $parts = array_map(function ($part) {
                return implode($part);
            }, $parts);
            $name = implode($parts);

            // Trim and return normalized name
            $properName = trim($name);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
            $properName = $name;
        } finally {
            return $properName;
        }
    }
}
