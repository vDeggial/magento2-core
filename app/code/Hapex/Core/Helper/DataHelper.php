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

    public function getNameCase(?string $name = null): ?string
    {
        // Return early if null or entirely whitespace
        if (trim((string) $name) === '') {
            return $name;
        }

        try {
            // 1. Normalize spacing to a single space
            $name = preg_replace('/\s+/', ' ', trim($name));

            // 2. Native Capitalization
            // ucwords natively capitalizes after spaces, hyphens, and apostrophes
            $name = ucwords(strtolower($name), " '-");

            // 3. Dictionary of special cases (O(1) lookups)
            // Keys must be strictly lowercase for case-insensitive matching
            $specialCases = [
                // Lowercase particles
                'van' => 'van',
                'het' => 'het',
                'in' => 'in',
                "'t" => "'t",
                'ten' => 'ten',
                'den' => 'den',
                'von' => 'von',
                'und' => 'und',
                'der' => 'der',
                'de' => 'de',
                'da' => 'da',
                'of' => 'of',
                'and' => 'and',
                'the' => 'the',
                // Roman Numerals
                'ii' => 'II',
                'iii' => 'III',
                'iv' => 'IV',
                'v' => 'V',
                'vi' => 'VI',
                'vii' => 'VII',
                'viii' => 'VIII',
                'ix' => 'IX',
            ];

            // 4. Apply special cases to whole words
            $parts = explode(' ', $name);
            foreach ($parts as &$part) {
                $lowerPart = strtolower($part);
                if (isset($specialCases[$lowerPart])) {
                    $part = $specialCases[$lowerPart];
                }
            }
            $name = implode(' ', $parts);

            // 5. Target specific prefixes (Mc, Mac, O', d', l')
            $name = preg_replace_callback(
                '/\b(mc|mac|o\'|d\'|l\')([a-z]+)/i',
                function ($matches) {
                    $prefix = strtolower($matches[1]);

                    // Set exact casing for the prefix
                    if ($prefix === 'mc')
                        $prefix = 'Mc';
                    elseif ($prefix === 'mac')
                        $prefix = 'Mac';
                    elseif ($prefix === "o'")
                        $prefix = "O'";
                    // d' and l' remain lowercase as expected
    
                    // Re-attach prefix to the capitalized next letter
                    return $prefix . ucfirst(strtolower($matches[2]));
                },
                $name
            );

            // 6. Enforce "St." formatting
            $name = preg_replace('/\bst\b\.?/i', 'St.', $name);

            return $name;

        } catch (\Throwable $ex) {
            $this->errorHandleException($ex);
            // Fallback to the original unformatted string if something fails
            return $name;
        }
    }
}
