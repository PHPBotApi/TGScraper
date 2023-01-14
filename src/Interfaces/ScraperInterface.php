<?php

namespace PHPBotApi\Interfaces;

use DOMNodeList;
use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface;

interface ScraperInterface
{
    public function getResponse(): ResponseInterface;
    public function getDOMDocument($response): DOMNodeList;
    public function getInfo($element): array|null;
    public function setDescription($element): ScraperInterface;
    #[NoReturn] public function setFields($matches): ScraperInterface;
    public function setExtendedBy($element): ScraperInterface;
    public function getJson(): bool;

}