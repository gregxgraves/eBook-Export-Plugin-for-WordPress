<?php
/*  Copyright 2010  Christopher Clarke  (email : chrisclarke@sorrowfulunfounded.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
Plugin Name: WP eBook Export
Plugin URI: http://sorrowfulunfounded.com/wp-ebook-export
Description: Exports a category as an eBook (currently only ePub).
Version: 0.1
Author: Cbristopher Clarke
Author URI: http://sorrowfulunfounded.com
*/

function wp_eBook_Export_add_menu_item()
{
    add_management_page( 'Create eBook', 'eBook Creator', 5, __FILE__, 'wp_eBook_Export_options_page' );
}

function wp_eBook_Export_options_page()
{
    $error = true;
    
    if (isset($_POST['create']))
    {
        $error = false;
        if (isset($_POST['book_title']) == false)
        {
            $error = true;
            echo '<p><strong>ERROR:</strong> You must enter a book title.</p>';
        }
        if (isset($_POST['book_author']) == false)
        {
            $error = true;
            echo '<p><strong>ERROR:</strong> You must enter a book author.</p>';
        }
        if (isset($_POST['category']) == false)
        {
            $error = true;
            echo '<p><strong>ERROR:</strong> You must specify the category containing your chapters.</p>';
        }
    }

    echo '<h2>Create eBook</h2>';

    if ($error == true)
    {
        wp_ebook_export_admin_form();
    } else {
    
        require_once 'eBook_Creator.php';
        require_once 'ePub.format.php';
        
        $eBook = new ePub;
        $eBook->set_title($_POST['book_title']);
        $eBook->set_author($_POST['book_author']);
        $eBook->set_license($_POST['license']);
        $eBook->chapter_category = intval($_POST['category']);
        if (isset($_POST['muse_listing']) && strlen($_POST['muse_listing']) > 10)
        {
            $eBook->set('muses-success', $_POST['muse_listing']);
        }
        if (isset($_POST['guide_listing']) && strlen($_POST['guide_listing']) > 10)
        {
            $eBook->set('webfictionguide', $_POST['guide_listing']);
        }
        if (isset($_POST['webficdic_listing']) && strlen($_POST['webficdic_listing']) > 10)
        {
            $eBook->set('web-fic-directory', $_POST['webficdic_listing']);
        }
        if (isset($_POST['isbn10']) && strlen($_POST['isbn10']) > 10)
        {
            $eBook->set_isbn($_POST['isbn10']);
        }
        if (isset($_POST['isbn13']) && strlen($_POST['isbn13']) > 10)
        {
            $eBook->set_isbn13($_POST['isbn13']);
        }
        $eBook->add_chapters();
        $eBook->save_ebook();
        
        echo '<p>The eBook has been created successfully.</p>';
        echo '<p>Download Your eBook:</p>';
        echo '<p><a href="'.plugins_url('eBooks/'.sanitize_title($_POST['book_title']).$eBook->file_extension,__FILE__).'">'.plugins_url('eBooks/'.sanitize_title($_POST['book_title']).$eBook->file_extension,__FILE__).'</a></p>';
        
    }

}

function wp_ebook_export_admin_form()
{

?><form method="post" action="">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="book_title">Book Title</label></th>
<td><input name="book_title" type="text" id="book_title" value="" class="regular-text" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="book_author">Book Author Name</label></th>
<td><input name="book_author" type="text" id="book_author" value="" class="regular-text" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="category">Chapter Category:</label></th>
<td><input name="category" type="text" id="category" value="" class="category" maxlength="4" /> <span class="description">Post category containing the chapters of your book, published in chronological order.</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="book_author">License</label></th>
<td><select name="license">
<option value="1">Standard Copyright</option>
<option value="2">Public Domain</option>
<optgroup label="Creative Commons">
    <option value="3">Attribution</option>
    <option value="4">Attribution-ShareAlike</option>
    <option value="5">Attribution-NonCommercial</option>
    <option value="6">Attribution-NonCommercial-ShareAlike</option>
    <option value="7">Attribution-NoDerivs</option>
    <option value="8">Attribution-NonCommercial-NoDerivs</option>
</optgroup>
<option value="9">GNU Free Documentation License</option>
</select></td>
</tr>
<tr valign="top">
<th scope="row"><label for="pubyear">First Publication Year</label></th>
<td><input name="pubyear" type="text" id="pubyear" value="" class="year" maxlength="4" /> <span class="description">What was the first year in which you published this book online or in print, whichever came first?</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="isbn10">ISBN-10 Number</label></th>
<td><input name="isbn10" type="text" id="isbn10" value="" class="regular-text" /> <span class="description">Your International Standard Book Number if you have one</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="isbn13">ISBN-13 Number</label></th>
<td><input name="isbn13" type="text" id="isbn13" value="" class="regular-text" /> <span class="description">Same as above.</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="format">eBook Format</label></th>
<td><select name="format" id="forat"><option value="ePub">EPUB (.epub)</option></select> <span class="description">Formats other then ePub will be available in a future update.</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="muse_listing"><a href="http://muses-success.info/">Muse's Success</a> Listing URL</label></th>
<td><input name="muse_listing" type="text" id="muse_listing" value="" class="regular-text" /> <span class="description">URL to this web fiction's page on Muse's Success.</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="guide_listing"><a href="http://webfictionguide.com/">Web Fiction Guide</a> Listing URL</label></th>
<td><input name="guide_listing" type="text" id="guide_listing" value="" class="regular-text" /> <span class="description">URL to this web fiction's page on WebFictionGuide.</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="webficdic_listing"><a href="http://www.tonyamoore.com/web-fic-directory/">Web Fic Directory</a> Listing URL</label></th>
<td><input name="webficdic_listing" type="text" id="webficdic_listing" value="" class="regular-text" /> <span class="description">URL to this web fiction's page on Web Fic Directory.</span></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" name="create" value="Create eBook" /></td>
</tr>
</table>
<?php

}
    
add_action('admin_menu', 'wp_eBook_Export_add_menu_item');

?>
