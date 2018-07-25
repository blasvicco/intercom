# Intercom plugin for Craft CMS 3.x

Interface for Intercom

## Requirements

This plugin requires:
  - [Craft CMS 3.0.0](https://craftcms.com/news/craft-3) or later.
  - [intercom/intercom-php](https://github.com/intercom/intercom-php) or later.

## Installation

To install the plugin, follow these instructions.

```BASH
cd {craft app folder}
composer config repositories.blasvicco.intercom vcs https://github.com/blasvicco/intercom.git
composer require blasvicco/intercom
./craft install/plugin intercom
```

## Intercom Overview

Routes an email through intercom creating the user if doesn't exist.

## Configuring Intercom

A file called `config/intercom.php` need to be created with the next settings:

```PHP
return [
  'oauth'  => getenv('YOUR_INTERCOM_OAUTH_HERE'),
  'appId'  => getenv('YOUR_INTERCOM_APP_ID_HERE'),
  'body' => "WEB FORM:\nSubject: _PAGE_\n _DETAILS_",
  'requireToken' => TRUE, // could it be FALSE and token validation will be skipped
];
```

Where `_PAGE_` and `_DETAILS_` are the fields from the `$_POST`.

Other fields are also available like `_EMAIL_` or `_NAME_`.

## Using Intercom

In order to use the plugin after install you need to generate a FORM that post the next data structure:

```PHP
  'ticket' => [
    'extra'   => 'a_valid_token',
    'email'   => 'a_valid_email',
    'name'    => 'not_empty_name',
    'details' => 'not_empty_details',
    'page'    => 'optional_page_title'
  ]
```

A valid token can be requested to `intercom/api/token` that will return a JSON like this one:

```JSON
{
  "token":"Z0FmbC9tbnFXSnBUMmZHZDZNZV..."
}
```

Tokens are valid for no more than 5 minutes.

Brought to you by [Blas Vicco](https://github.com/blasvicco)
