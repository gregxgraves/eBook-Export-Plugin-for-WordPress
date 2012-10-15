# eBook Export Plugin for WordPress
Developed by [Christopher Clarke](http://www.cclarke.me) and released under the GNU General Public License (see LICENSE).

## About

WordPress eBook Export takes a specified category of posts and creates an eBook suitable for use on an eReader device that supports ePub.

This is an early alpha version of the plugin - only use if your feeling adventurous.

## Features

* Automatic table of contents generation
* Formats: ePub (other formats like MobiPocket, HTML, and DocBook coming soon)
* Creative Commons Support
* [Muse's Success](http://muses-success.info) , [WebFictioGuide](http://webfictionguide.com/) and [WebFicDirectory](hhttp://www.tonyamoore.com/web-fic-directory/) support - include your listing URLs in your eBook.

## Requirements

* WordPress 2.9 or later
* PHP 5.2 or later
* PHP must have write access to the eBooks directory

## Installation and Usage

Upload the zip through Plugins -> Add Plugin, then Activate.

Use Tools -> Create eBook and fill out the form to create your eBook.

## Questions and Answers

### What about other eBook formats?

Other formats will be considered once ePub support is stable. For now, please use [Calibre](http://calibre-ebook.com/) to convert ePub to another format.

### The ordering of chapters is wrong, can I reorder?

Currently the only way to do this is to go through each post and manually change the post date. Chapter reordering support will be added in a future release.

## Credit

* Johan Dahlstrom, @mastermute (Twitter) - for replacing ZipArchive with PclZip, adding support for images, bug reports

## Changelog

### 0.1

* Initial pre-alpha release.
* Basic support for ePub