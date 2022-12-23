# TGScraper

[![Bot Api](https://img.shields.io/badge/Bot%20Api-6.3-2686B7?labelColor=404040&style=flat&logo=Telegram&link=https://core.telegram.org/bots/api)](https://core.telegram.org/bots/api)
[![PHP](https://img.shields.io/badge/PHP-8.2-0066cc?labelColor=404040&style=flat&logo=PHP&link=https://php.net)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3.0-darkred?labelColor=404040&style=flat&logo=GNU-Privacy-Guard&link=https://github.com/PHPBotApi/TGScraper/blob/master/LICENSE)](https://github.com/PHPBotApi/TGScraper/blob/master/LICENSE)
[![Release](https://img.shields.io/badge/Release-1.4.0-green?labelColor=404040&style=flat&logo=GitHub&link=https://github.com/PHPBotApi/TGScraper/releases/tag/1.3.2)](https://github.com/PHPBotApi/TGScraper/releases/tag/1.3.2)

**TGScraper** is a simple PHP Scraper that uses only one easy-to-install external library. It generates a [JSON](https://www.codewall.co.uk/how-to-read-json-file-using-php-examples/)e containing all TelegramAPI's Types and Methods that will be used to auto-generate the core functions of the Framework.

_P.s. This Repository is a part of PHPBotApi Framework._


# Requirements
• [PHP 8+](https://www.php.net/downloads.php#v8.2.0)

• [PHPDomDocument Extension](https://www.php.net/manual/en/book.dom.php#book.dom)

• [PHP libxml Extension](https://www.php.net/manual/en/book.libxml.php) (**This extension is active by default**)

_Follow the official guides (click on hyperlink) to install PHP and enable the extensions._

Then we need an external library called [Guzzle](https://docs.guzzlephp.org/en/stable/) that we can easly install via composer with this command below:

```sh
composer require guzzlehttp/guzzle:^7.0
```


# How To Use
TGScraper is very easy to use. infact just run the php file called Scraper.php which will automatically generate the JSON file api.json

The JSON file is structured like this: 

```
{
  "types": { //An object containing objects of all Telegram Types
  
    "type_name": { //The Object of Type
    
      "name": "name", //The name of Type
      "description": "description", //The decription of Type
      
      "fields": [ //Fields represent an array of objects containing all fields of this Type, example below
        {
          "name": "update_id", //The name of field
          "type": "Integer", //The name of field
          "description": "The update's unique identifier" //The description of field
        }
      ]
    }
  },
  "methods": { //An object containing objects of all Telegram Methods
  
    "methods_name": { //The Object of Method
    
      "name": "name", //The name of Method
      "description": "description", //The description of Method
      
      "fields": [
        {
          "name": "offset", //The name of field
          "type": "Integer", //The type of field
          "required": "Yes", //if field is required or optional
          "description": "Identifier of the first update to be returned." //The description of field
        }
      ]
    }
  }
}
```
