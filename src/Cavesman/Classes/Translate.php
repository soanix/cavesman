<?php

namespace Cavesman;

class Translate
{
    const string FILE = _APP_ . '/locale/messages.json';

    public static string $currentLanguage = 'en';
    public static array $strings = [];

    /**
     * @param $string
     * @param array $replace
     * @return string
     */
    public static function get($string, array $replace = []): string
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
     * Get locale list
     *
     * @return array
     */
    public static function list(): array
    {
        $list = [];
        foreach (Config::get('locale.languages') as $lang) {
            $list[$lang] = self::getLanguage($lang);
        }

        return $list;
    }

    /**
     * Merge messages into files in locale
     *
     * @return void
     */
    public static function merge(): void
    {
        $messages = file_exists(self::FILE) ? json_decode(file_get_contents(self::FILE), true) : [];

        foreach (Config::get('locale.languages') as $lang) {

            $file = _APP_ . "/locale/messages.{$lang}.json";

            $messages_locale = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

            foreach ($messages as $key => $message) {
                if (!isset($messages_locale[$key]))
                    if (!empty($message['message']))
                        $messages_locale[$key] = $message['message'];
            }
            $fp = fopen($file, 'w+');
            fwrite($fp, json_encode($messages_locale, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            fclose($fp);
        }

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

        $json = file_exists(self::FILE) ? json_decode(file_get_contents(self::FILE), true) : [];


        if (isset($json[$item['string']])) {
            if(!empty($json[$item['string']]['message']) && !isset(self::$strings[$item['string']])) {
                return false;
            }else{
                return true;
            }
        }



        $json[$item['string']] = ['message' => "", 'replace' => $item['replace']];

        $fp = fopen(self::FILE, 'w+');
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
        if (!is_dir(dirname(self::FILE)))
            mkdir(dirname(self::FILE), 0644, true);
    }

    /**
     * Return language array
     *
     * @param $lang
     * @return array
     */
    private static function getLanguage($lang): array
    {
        $file = _APP_ . "/locale/messages.{$lang}.json";
        self::$strings = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        return self::$strings;
    }

}
