<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Cavesman\Enum\Locale;
use GuzzleHttp\Client;

class Translate
{

    private static array $strings = [];
    private static array $stringsOverride = [];
    private static ?string $override = null;
    private static Interface\Locale $currentLanguage = Locale::en;
    private static string|Interface\Locale $class = Locale::class;

    public static function getLocales(): array
    {
        return self::$class::cases();
    }

    public static function setClass(string $class): void {
        if (!enum_exists($class) || !is_subclass_of($class, Interface\Locale::class)) {
            throw new \InvalidArgumentException(
                "$class is not implements " . Interface\Locale::class
            );
        }

        self::$class = $class;
    }

    public static function setLanguage(Interface\Locale $language): void {
        self::$currentLanguage = $language;
    }

    public static function setOverride(?string $override): void {
        self::$override = $override;
    }

    public static function getFile(): string
    {
        return FileSystem::getPath(Directory::LOCALE) . '/messages.json';
    }

    /**
     * Get locale list
     *
     * @return array
     */
    public static function list(): array
    {
        $list = [];
        foreach (self::$class::cases() as $lang) {
            $list[$lang->value] = self::getLanguage($lang);
        }

        return $list;
    }

    /**
     * @param string $string
     * @param array $replace
     * @return string
     */
    public static function get(string $string, array $replace = []): string
    {
        self::getLanguage(self::$currentLanguage);


        $check = self::check(['string' => $string, 'replace' => $replace]);

        if (!$check) {
            self::getLanguage(self::$currentLanguage);
            self::merge();
        }


        if (!isset(self::$strings[$string]))
            return $string;

        return str_replace(array_map(fn($key) => '%' . $key . '%', array_keys($replace)), array_values($replace), self::$strings[$string]);
    }

    /**
     * Return language array
     *
     * @param Interface\Locale $lang
     * @return array
     */
    private static function getLanguage(Interface\Locale $lang): array
    {
        $file = FileSystem::getPath(Directory::LOCALE) . '/messages.' . $lang->value . '.json';
        $strings = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        if(self::$override) {
            $strings = array_merge($strings, self::getLanguageOverride($lang));
        }

        self::$strings = $strings;

        return self::$strings;
    }

    /**
     * Return language array
     *
     * @param $lang
     * @return array
     */
    private static function getLanguageOverride(Interface\Locale $lang): array
    {
        $file = FileSystem::getPath(Directory::LOCALE) . '/' . self::$override . '/messages.' . $lang->value . '.json';
        if(!is_dir(dirname($file)))
            mkdir(dirname($file), 0777, true);

        self::$stringsOverride = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        return self::$stringsOverride;
    }


    /**
     * Create locale file if not exists
     *
     * @param $item
     * @return boolean
     */
    public static function check($item): bool
    {
        if (empty($item['string']))
            return true;

        self::checkDirectory();

        $json = file_exists(self::getFile()) ? json_decode(file_get_contents(self::getFile()), true) : [];


        if (isset($json[$item['string']])) {
            if (!empty($json[$item['string']]['message']) && !isset(self::$strings[$item['string']])) {
                return false;
            } else {
                return true;
            }
        }


        $json[$item['string']] = ['message' => "", 'replace' => $item['replace']];

        $fp = fopen(self::getFile(), 'w+');
        fwrite($fp, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fclose($fp);


        return false;
    }

    /**
     * Create directory if not exists
     *
     * @return void
     */
    private static function checkDirectory(): void
    {
        if (!is_dir(dirname(self::getFile())))
            mkdir(dirname(self::getFile()), 0644, true);
    }

    /**
     * Merge messages into files in locale
     *
     * @return void
     */
    public static function merge(): void
    {
        $messages = file_exists(self::getFile()) ? json_decode(file_get_contents(self::getFile()), true) : [];

        foreach (self::$class::cases() as $lang) {

            $file = FileSystem::getPath(Directory::LOCALE) . "/messages.{$lang->value}.json";

            $messages_locale = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

            foreach ($messages as $key => $message) {
                if (!isset($messages_locale[$key]))
                    if (!empty($message['message'])) {

                        $translate = $message['message'];

                        foreach(glob(FileSystem::getPath(Directory::TOOL) . "/Translate/*.php") as $f) {
                            $translate = require $f;
                        }


                        $messages_locale[$key] = $translate;
                    }
            }
            $fp = fopen($file, 'w+');
            fwrite($fp, json_encode($messages_locale, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            fclose($fp);
        }

    }


}
