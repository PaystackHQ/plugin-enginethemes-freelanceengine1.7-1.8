# Paystack Gateway for EngineThemes FreelanceEngine 1.7 / 1.8

A WordPress plugin that adds [Paystack](https://paystack.com) as a payment gateway to the
[EngineThemes](https://www.enginethemes.com/) FreelanceEngine theme (versions 1.7 and 1.8).
It adds a "Paystack" option to the payment list, sends the customer to Paystack to pay, and
verifies the transaction when they return to the site.

## Requirements

- PHP with the 21 Oct 2026 security fixes for critical remote code execution flaws (GHSA-w8hw-w9fx-j79w, GHSA-6wmf-4rv5-p3rp; [php.net advisory](https://www.php.net/releases/8_2_35.php)): **8.2.35, 8.3.36, 8.4.27, 8.5.12 or later**. PHP 8.1 and older no longer receive security fixes.
- FreelanceEngine theme 1.7 or 1.8 (a commercial theme from EngineThemes)
- WordPress version supported by your FreelanceEngine release

### A note on FreelanceEngine and PHP 8

EngineThemes' changelog says FreelanceEngine 1.8.2 added compatibility with WordPress 4.8 and PHP 7
([changelog](https://www.enginethemes.com/changelogs/freelanceengine-v1-8-2-july-18-2016/)). The only
PHP 8 statement we found from the vendor is for FreelanceEngine 1.12, described as a compatibility
update for servers on PHP 8.2 through 8.5
([announcement](https://www.enginethemes.com/freelanceengine-1-12-wordpress-compatibility-update/)).
We found no statement that 1.7 or 1.8 support PHP 8. This plugin's own code has been checked on
PHP 8.3 and 8.5, but it has not been tested with the FreelanceEngine theme itself. If you must run a
patched PHP version, check with EngineThemes about upgrading the theme.

## Installation

1. Copy this folder (containing `ae_paystack.php` and `logos@2x.png`) to `wp-content/plugins/` on your site.
2. In WordPress admin, go to **Plugins** and activate "Paystack Gateway for Enginethemes.com FreelanceEngine site".
3. Go to the FreelanceEngine overview menu, open **Paystack Api Key settings**, choose a **Mode**
   (Disable, Test or Live), and enter your test and live secret/public keys from the Paystack
   dashboard (Settings, Developer/API).
