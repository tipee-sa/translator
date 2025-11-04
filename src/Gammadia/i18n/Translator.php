<?php
/**
 * Created by PhpStorm.
 * User: zack
 * Date: 19.03.18
 * Time: 14:33
 */

namespace Gammadia\i18n;

use PhpMyAdmin\MoTranslator\Loader;

/**
 * Class Translator
 * @package Gammadia\i18n
 */
class Translator
{
    /**
     * @var Loader
     */
    private $translatorLoader;

    /**
     * @var Loader
     */
    private $fallbackTranslatorLoader;

    /**
     * @var string
     */
    private $domain;

    /**
     * Translator constructor.
     * @throws \Exception
     */
    public function __construct(string $locale = 'fr', string $path = '', string $domain = '')
    {
        $this->domain = $domain;

        $loader = new Loader();
        $loader->setlocale($locale);
        $loader->bindtextdomain($this->domain, $path);
        $loader->textdomain($this->domain);

        $fallbackTranslatorLoader = clone $loader;
        $fallbackTranslatorLoader->setlocale('fr');

        $this->translatorLoader = $loader;
        $this->fallbackTranslatorLoader = $fallbackTranslatorLoader;
    }

    /**
     * @param string $locale
     *
     * @return Translator
     */
    public function setLocale($locale): Translator
    {
        $this->translatorLoader->setlocale($locale);

        return $this;
    }

    /**
     * Translates the given string, first by looking into the custom translations,
     * and then if not found by looking into the global translations
     *
     * @param string $msgId
     * @param array<string, mixed> $params
     * @param bool $capitalize
     * @return string
     */
    public function translate($msgId, array $params = [], bool $capitalize = true): string
    {
        $translator = $this->translatorLoader->getTranslator();

        $value = $translator->exists($msgId)
            ? $translator->gettext($msgId)
            : $this->fallbackTranslatorLoader->getTranslator()->gettext($msgId);

        $keys = array_map(function ($key) {
            return sprintf('{%s}', $key);
        }, array_keys($params));

        /** @phpstan-ignore arrayValues.list, argument.type */
        $translated = str_replace(array_values($keys), array_values($params), $value);

        return $capitalize ? \ucfirst($translated) : $translated;
    }
}
