# PMI Users Sync
Contributors: angelochillemix
Donate link: https://paypal.me/angelochillemi
Tags: pmi, users management
Requires at least: 5.x
Tested up to: 5.8.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Synchronizes the ID of PMI (Project Management Institute) members with the same users registered to the site.

## Description

Wordpress plugin for the synchronization of PMI (Project Management Institute) ID with users registered to the site. 
It is useful to the PMI Chapters that wants to provide specific services to PMI members.
The synchronization is done by matching the email address. If the email address the users is using in PMI is different than the one used to register to the site, then the synchonization does not occur.

## Installation

This section describes how to install the plugin and get it working.

e.g.

1. Upload `pmi-users-sync.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('pmi_users_sync_hook'); ?>` in your templates

## Frequently Asked Questions

None

## Screenshots

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

## Changelog

### 1.0.1
* Weekly scheduled synchronization
* Check Advanced Custom Fields plugin is active and that PMI-ID custom field exists
* PHPUnit setup for unit testing
* Fix some minor bugs
* Improved exception handling

### 1.0
* Setup the Excel file from PMI in the plugin settings, as well as the custom field representing the PMI-ID to be synchronized

## Upgrade Notice

None

