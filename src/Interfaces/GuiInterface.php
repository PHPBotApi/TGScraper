<?php

namespace PHPBotApi\Interfaces;

interface GuiInterface
{
    public static function getIntroduction(): string;
    public static function getApiVersion(string $version): string;
    public static function getReport(int $types_count, int $methods_count): string;
}