<?php

namespace MoovMoney\i18n;

class LanguageManager
{
    // Default language
    static public $lang = 'fr';

    // Array to cache loaded translations
    private static $translations = [];

    /**
     * Get the current language.
     *
     * @return string Current language code.
     */
    private static function getLocal(): string
    {
        // Returns the current language or the default value
        return self::$lang ?? ($_ENV['local'] ?? 'fr');
    }

    /**
     * Load the translation file for the current language, if not already loaded.
     *
     * @return array The array of translations for the language.
     */
    private static function loadTranslations(): array
    {
        $locale = self::getLocal();

        // If translations for the language are already loaded, return them
        if (isset (self::$translations[$locale])) {
            return self::$translations[$locale];
        }

        // Set the path of the translation file
        $filePath = __DIR__ . "/translations/{$locale}.php";

        // Check if the translation file exists and load it
        if (file_exists($filePath)) {
            self::$translations[$locale] = include $filePath;
        } else {
            // Use an empty array if the file does not exist
            self::$translations[$locale] = [];
        }

        return self::$translations[$locale];
    }

    /**
     * Get a translated message for the given key.
     *
     * @param string $key Translation key.
     * @param array|null $parameters Values to replace placeholders in the translation.
     * @return string Translated message.
     */
    public static function getTranslation(string $key, ?array $parameters = []): string
    {
        // Load translations for the current language
        $translations = self::loadTranslations();

        // Get the translation, or use the key if the translation does not exist
        $translation = $translations[$key] ?? $key;

        // Replace placeholders in the translation
        if ($parameters) {
            foreach ($parameters as $placeholder => $value) {
                $translation = str_replace(":$placeholder", $value, $translation);
            }
        }

        return $translation;
    }
}
