# Intercom plugin for Craft CMS 3.x

Interface for Intercom

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.
This plugin requires [intercom/intercom-php](https://github.com/intercom/intercom-php) or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        ```BASH
        cd {craft app folder}
        composer config repositories.blasvicco.intercom vcs https://github.com/blasvicco/intercom.git
        composer require blasvicco/intercom
        ./craft install/plugin intercom
        ```

## Intercom Overview

Routes an email through intercom creating the user if doesn't exist.

## Configuring Intercom

A file called `intercom.php` need to be created with the next settings:

```PHP
return [
  'oauth'  => getenv('YOUR_INTERCOM_OAUTH_HERE'),
  'appId'  => getenv('YOUR_INTERCOM_APP_ID_HERE'),
  'body' => "WEB FORM:\nSubject: {{ PAGE }}\n {{ DETAILS }}."
];
```

Where `{{ PAGE }}` and `{{ DETAILS }}` are fields from the `$_POST`.

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

A valid token can be requested to `intercom/api/token` that will retunr a JSON like this one:

```JSON
{
  "token":"Z0FmbC9tbnFXSnBUMmZHZDZNZVkyd3RLUHk2c2xUaU8vakFNWHpWdm5WRStaeS8xYXhYaTRCM3VGcWQyTmJ2b1RUMHg3bS9xcUJIb3FHRS9TZ0ZZWHRmUTV5OERsY2orV1dxSnIvVGZ4WjQ9"
}
```

## Intercom Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Blas Vicco](https://github.com/blasvicco)
