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
        if (trim((string) $name) === '') {
            return $name;
        }

        try {
            // 1. Normalize spacing and mobile "smart" apostrophes
            $name = preg_replace('/\s+/', ' ', trim($name));
            $name = str_replace(['’', '‘', '`'], "'", $name);

            // 2. Multi-byte Native Capitalization (Safe for Á, É, Ñ, etc.)
            // MB_CASE_TITLE automatically capitalizes after spaces, hyphens, and apostrophes
            $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');

            // 3. Expanded Dictionary (O(1) lookups)
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
                'la' => 'la',
                'los' => 'los',
                'las' => 'las',
                'el' => 'el',
                'del' => 'del',
                'di' => 'di',
                'della' => 'della',
                // Suffixes & Roman Numerals
                'ii' => 'II',
                'iii' => 'III',
                'iv' => 'IV',
                'v' => 'V',
                'vi' => 'VI',
                'vii' => 'VII',
                'viii' => 'VIII',
                'ix' => 'IX',
                'jr' => 'Jr.',
                'jr.' => 'Jr.',
                'sr' => 'Sr.',
                'sr.' => 'Sr.',
            ];

            // 4. Apply special cases to whole words
            $parts = explode(' ', $name);
            foreach ($parts as &$part) {
                $lowerPart = mb_strtolower($part, 'UTF-8');
                if (isset($specialCases[$lowerPart])) {
                    $part = $specialCases[$lowerPart];
                }
            }
            $name = implode(' ', $parts);

            // 5. Target prefixes using Unicode regex modifier (u) and letter property (\p{L})
            $name = preg_replace_callback(
                '/\b(mc|o\'|d\'|l\')([\p{L}]+)/iu',
                function ($matches) {
                    $prefix = mb_strtolower($matches[1], 'UTF-8');

                    if ($prefix === 'mc')
                        $prefix = 'Mc';
                    elseif ($prefix === "o'")
                        $prefix = "O'";
                    // d' and l' remain lowercase
    
                    // Capitalize the first multi-byte character of the remainder
                    $firstLetter = mb_substr($matches[2], 0, 1, 'UTF-8');
                    $rest = mb_substr($matches[2], 1, null, 'UTF-8');

                    $capitalizedRemainder = mb_convert_case($firstLetter, MB_CASE_UPPER, 'UTF-8')
                        . mb_strtolower($rest, 'UTF-8');

                    return $prefix . $capitalizedRemainder;
                },
                $name
            );

            // 6. Enforce "St." formatting
            $name = preg_replace('/\bst\b\.?/iu', 'St.', $name);

            return $name;

        } catch (\Throwable $ex) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($ex));
            return $name;
        }
    }
}
