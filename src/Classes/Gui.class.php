<?php

namespace PHPBotApi\TGScraper;

use PHPBotApi\Interfaces\GuiInterface;

class Gui implements GuiInterface
{
    /**
     * @return string
     */
    public static function getIntroduction(): string
    {
        $text = "\e[1;35mTelegram Api Scraper developed by Guard4534\e[0m" . PHP_EOL . PHP_EOL;
        $text .= "\e[1;35mThis project is an integral part of a framework\e[0m" . PHP_EOL;
        $text .= "\e[1;35mto develop Telegram Bot with PHP in a very easy way.\e[0m" . PHP_EOL . PHP_EOL;
        $text .= "\e[1;35mGithub Framework:\e[0m https://github.com/PHPBotApi" . PHP_EOL;
        $text .= "\e[1;35mGithub:\e[0m https://github.com/Guard4534" . PHP_EOL;
        $text .= "\e[1;35mTelegram:\e[0m https://t.me/Guard4534" . PHP_EOL . PHP_EOL;
        $text .= "\e[1;31mI release myself from any responsibility for any improper use of the code.\e[0m" . PHP_EOL;
        $text .= PHP_EOL . PHP_EOL . PHP_EOL;
        $text .= "Starting Script..." . PHP_EOL . PHP_EOL;

        return $text;
    }

    /**
     * @param string $version
     * @return string
     */
    public static function getApiVersion(string $version): string
    {
        return "New Telegram Bot Api version found: $version" . PHP_EOL;
    }

    /**
     * @param int $types_count
     * @param int $methods_count
     * @return string
     */
    public static function getReport(int $types_count, int $methods_count): string
    {
        $text = "Types founds -> " . $types_count . PHP_EOL;
        $text .= "Methods founds -> " . $methods_count . PHP_EOL;
        return $text;
    }
}