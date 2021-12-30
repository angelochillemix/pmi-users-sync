# PMI Users Sync
Contributors: angelochillemix
Donate link: https://paypal.me/angelochillemi
Tags: pmi, users management
Requires at least: 5.x
Tested up to: 5.8.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Synchronizes the ID of PMI (Project Management Institute) members with the same users registered to the site.

## Description

Wordpress plugin for the synchronization of PMI (Project Management Institute) ID with users registered to the site. 
It is useful to the PMI Chapters that wants to provide specific services to PMI members.
The synchronization is done by matching the email address. If the email address the users is using in PMI is different than the one used to register to the site, then the synchonization does not occur.

## Installation

This section describes how to install the plugin and get it working.

The PMI Users Sync plugin requires Advanced Custom Fields pluing installed and activated to add the PMI-ID custome field to the user

### Manual installation
1. Download the zip file locally and extract it to a temporary folder 
1. Upload the extracted directory pmi-users-sync to the production wordpress under `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

### Installation from Wordpress
1. Download the zip file locally
1. Go to the 'Plugins' menu 
1. Click the 'Add New' button
1. Click the 'Upload Plugin' button
1. Click the 'Choose file" button and navigate to the directory where the plugin zip file is stored
1. Click the 'Install Now' button
1. Activate the plugin

### Post installation plugin setup
1. Setup the 'PMI-ID Priority' setting to set the overwrite priority. If you'd like to prioritize the PMI-ID from PMI then tick the checkbox
1. Set the Excel file which contains the PMI members list extraction
1. Check from the 'PMI-Users' plugin page if the users are loaded from the Excel file. The same file will be used by the WP Cron to weekly synchronize the PMI-IDs

## Frequently Asked Questions

None

## Screenshots

**TODO** when uploaded to Wordpress.org upload and set the screenshots

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

## Changelog

### 1.0.1
* Changes on README.txt and translation in italian

### 1.0.1
* Weekly scheduled synchronization using WP Cron
* Check Advanced Custom Fields plugin is active and that PMI-ID custom field exists
* PHPUnit setup for unit testing
* Fix some minor bugs
* Improved exception handling

### 1.0
* Setup the Excel file from PMI in the plugin settings, as well as the custom field representing the PMI-ID to be synchronized

## Upgrade Notice

None

