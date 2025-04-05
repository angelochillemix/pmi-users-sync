# PMI Users Sync
Contributors: angelochillemix
Donate link: https://paypal.me/angelochillemi
Tags: pmi, users management
Requires at least: 5.x
Tested up to: 5.8.2
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Synchronizes the ID of PMI (Project Management Institute) members with the same users registered to the site.

## Description

Wordpress plugin for the synchronization of PMI (Project Management Institute) ID with users registered to the site. 
It is useful to the PMI Chapters that wants to provide specific services to PMI members.
The synchronization is done by matching the email address. If the email address the users is using in PMI is different than the one used to register to the site, then the synchonization does not occur.

## Installation

This section describes how to install the plugin and get it working.

The PMI Users Sync plugin requires the following plugins:
* Advanced Custom Fields pluing installed and activated to add the PMI-ID custome field to the user
* Members plugin to add the custom roles to set for the PMI members

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

### Plugin settings
1. *PMI-ID Priority*: Setup the *'PMI-ID Priority'* setting to set the overwrite priority. If you'd like to prioritize the PMI-ID from PMI then tick the checkbox, therefore any ID set by the users will be overwritten by the actual ID from PMI.
1. *Role to set*: Role to set Set the role to set for the members of PMI
1. *Role to remove*: Set the role to be removed for the users not members of PMI
1. *PMI-ID custom field*: The ACF custom field for the PMI-ID set for each user
1. *Memberships to set*: The membership to set to each PMI member used for access to the events, discount or special prices
1. *Memberships to remove*: The membership to be removed for each users not a member of PMI
1. *Membership custom field*: The ACF custom field for the membership set for each user
1. *Synchonization schedule*: The recurrence of the loader that synchornize the Wordpresss users with the PMI members from the selected loader
1. *Loader*: The source of the users list with members of PMI. It can be an Excel file, manually downloaded and stored in the local filesystem, or a Web Service (see following section Web Service Loader)
1. *File*: Set the Excel file which contains the PMI members list extraction when the selected loader is Excel
1. *DEPService username*: The authentication username to access to PMI Web Service 
1. *DEPService password*: The authentication password to access to PMI Web Service 

#### Web Service Loader
The Web Service loader uses the Component Service of PMI as documented in the official documentation [How To Use Component System](https://components.pmi.org/UI/HelpDocs/HowToUseCS.pdf) starting from page 29.
First of all, the PMI Chapter Leader must create an API account, to have the credentials username and password to access the Web Service. These will be then used to retrieve the list of PMI members of the Chapter in real time.


## Frequently Asked Questions

None

## Screenshots
1. The PMI Users Sync plugin menu contains two menu items.
2. PMI Users Sync plugin setting page
3. PMI Users page where PMI users list from Excel file or Web Serice are displayed. From this page it is possible to trigger a manual synchornization clicking the Update button.

## Changelog

### 1.5.0
* Improve INFO logging
* Membership-Role Mapping
  * Map a role to the membership to assign the proper WordPress role to the user.

### 1.4.2
* Fix null post returned when checking ACF fields exist by returning "WP_Post | null" notation applicable only starting with PHP 8.1

### 1.4.1
* Typo in README.txt
* Delete doc folder since referncing the Component Service file directly from the original URL to ensure always the updated document is available

### 1.4.0
* Distinguish caching of different ACF fields when checking thye exists
* Improve this README.txt with more post installation steps

### 1.3.0
* Shows last PMI users synchronization date and time
* Added user loaded daily recurrence option for PMI users synchonization
* Update the user's role and membership as per subscription to PMI
* Refactoring of the users list page

### 1.2.0
* Changes according to the results of PHP Code Sniffer and Beautifier analysis following WordPress standard
* Resolved unmanaged exception thrown when username or password for DEP web service are not set
* Check that ACF PMI-ID field is set during the schedule update

### 1.1.0
* Implementation of web service call to retrieve the list of PMI members from PMI directly
* Refactoring for improved WP options management
* PHP Code Sniffer and Beautifier setup for Wordpress coding standard

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

