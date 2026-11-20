# Paystack Gateway for EngineThemes FreelanceEngine 1.7 / 1.8

A WordPress plugin that adds [Paystack](https://paystack.com) as a payment gateway to the
[EngineThemes](https://www.enginethemes.com/) FreelanceEngine theme (versions 1.7 and 1.8).
It adds a "Paystack" option to the payment list, sends the customer to Paystack to pay, and
verifies the transaction when they return to the site.

## Requirements

- PHP with the 19 Nov 2026 security fixes, rated up to critical by php.net and including remote code execution flaws in sessions, PHP-FPM and the engine core (for example GHSA-658j-r8rw-q6mx, GHSA-fm2m-fjf5-v3cp, GHSA-qc49-hpvq-g2w6; [php.net announcement](https://www.php.net/archive/2026.php#2026-11-19-1), [ChangeLog](https://www.php.net/ChangeLog-8.php)): **8.2.36, 8.3.37, 8.4.28, 8.5.13, 8.6.0 or later**. These releases also include the 21 Oct 2026 fixes. PHP 8.1 and older no longer receive security fixes.
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
