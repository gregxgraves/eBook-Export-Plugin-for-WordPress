<?php
/*
Plugin Name: WP eBook Export
Plugin URI: http://sorrowfulunfounded.com/wp-ebook-export
Description: Exports a category as an eBook (currently only ePub).
Version: 0.1
Author: Cbristopher Clarke
Author URI: http://sorrowfulunfounded.com
*/
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

define('WP_EBOOK_CURRENT_PATH', str_replace('wp_eBook_Export.php', '', __FILE__));

function wp_eBook_Export_add_menu_item()
{
    add_management_page(_('Create e-book'), _('E-book Manager'), 5, __FILE__, 'wp_eBook_Export_options_page' );
}

function wp_eBook_Export_options_page()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $errors = array();
        if (isset($_POST['book_title']) == false || strlen($_POST['book_title']) < 1)
        {
            $errors[] = _('You must enter a book title.');
        }
        if (isset($_POST['book_author']) == false || strlen($_POST['book_author']) < 1)
        {
            $errors[] = _('You must enter a book author.');
        }
        if (isset($_POST['category']) == false)
        {
            $errors[] = _('You must specify the category containing your chapters.');
        }
    }

    echo '<h2>',_('Create e-book'),'</h2>';

    if (count($errors) >= 1)
    {
        echo '<div class="error"><strong>',_('We were unable to create your ebook because the following errors occured:'),'</strong>'
            ,'<ul>'
            ,'<li>',implode('</li><li>', $errors),'</li>'
            ,'</ul></div>';
    }
    
    if (count($errors) >= 1 || $_SERVER['REQUEST_METHOD'] != 'POST')
    {

        wp_ebook_export_admin_form();

    } else {
    
        $book_info = array();
        
        $book_info['book_title'] = $_POST['book_title'];
        $book_info['book_author'] = $_POST['book_author'];
        
        if (isset($_POST['muses_success_listing']) && strlen($_POST['muses_success_listing']) > 3)
        {
            $book_info['muses_success_listing'] = $_POST['muses_success_listing'];
        }
        if (isset($_POST['wfg_listing']) && strlen($_POST['wfg_listing']) > 10)
        {
            $book_info['wfg_listing'] = $_POST['wfg_listing'];
        }
        if (isset($_POST['tmwfd_listing']) && strlen($_POST['tmwfd_listing']) > 10)
        {
            $book_info['tmwfd_listing'] = $_POST['tmwfd_listing'];
        }
        
        $book_info['publication_year'] = intval($_POST['pubyear']);
        
        require_once 'ISBN/ISBN.php';
        $isbn = new ISBN;
        
        if (isset($_POST['isbn10']) && $isbn->validateten($_POST['isbn10']) == true)
        {
            $book_info['isbn10'] = $_POST['isbn10'];
        }
        if (isset($_POST['isbn13']) && $isbn->validatettn($_POST['isbn13']) == true)
        {
            $book_info['isbn13'] = $_POST['isbn13'];
        }

        $split_format = explode('|', $_POST['format']);

        require_once 'ebook.class.php';
        require_once $split_format[1].'.'.$split_format[0].'.format.php';

        $ebook = new $split_format[1];
        $ebook->chapter_category = intval($_POST['category']);
        $ebook->set_book_info($book_info);
        $ebook->add_chapters();
        $ebook->save_ebook();
        
        echo '<div id="message" class="updated fade"><p>',_('The eBook has been created successfully.'),'</p></div>';
        echo '<p>',_('Download Your eBook:'),'</p>';
        echo '<p><a href="'.plugins_url('eBooks/'.sanitize_title($_POST['book_title']).$ebook->file_extension,__FILE__).'">'.plugins_url('eBooks/'.sanitize_title($_POST['book_title']).$eBook->file_extension,__FILE__).'</a></p>';
        
    }

}

function wp_ebook_export_admin_form()
{
    global $current_user;

    get_currentuserinfo();

    echo '<form method="post" action="">',"\n"
        ,'<table class="form-table">',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="book_title">',_('Book Title'),' <span class="required">*</span></label></th>',"\n\t"
        ,'<td><input name="book_title" type="text" id="book_title" value="',(isset($_POST['book_title']) ? $_POST['book_title'] : ''),'" class="regular-text" /></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="book_author">',_('Book Author Name'),' <span class="required">*</span></label></th>',"\n\t"
        ,'<td><input name="book_author" type="text" id="book_author" value="',(isset($_POST['book_author']) ? $_POST['book_author'] : $current_user->display_name),'" class="regular-text" /></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="category">',_('Chapter Category'),' <span class="required">*</span></label></th>',"\n\t"
        ,'<td><select name="category">',ebook_list_category_options(),'</select>'
        ,'<span class="description">',_('Post category containing the chapters of your book, published in chronological order.'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="book_author">',_('License'),' <span class="required">*</span></label></th>',"\n\t"
        ,'<td><select name="license">',ebook_license_options(),'</select></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="pubyear">',_('First Publication Year'),'</label></th>',"\n\t"
        ,'<td><input name="pubyear" type="text" id="pubyear" value="',(isset($_POST['pubyear']) ? $_POST['pubyear'] : date('Y')),'" class="year" maxlength="4" />',
        '<span class="description">',_('What was the first year in which you published this book online or in print, whichever came first?'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="isbn10">',_('ISBN-10 Number'),'</label></th>',"\n\t"
        ,'<td><input name="isbn10" type="text" id="isbn10" value="',(isset($_POST['isbn10']) ? $_POST['isbn10'] : ''),'" class="regular-text" />'
        ,'<span class="description">',_('10 digit International Standard Book Number.'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="isbn13">',_('ISBN-13 Number'),'</label></th>',"\n\t"
        ,'<td><input name="isbn13" type="text" id="isbn13" value="',(isset($_POST['isbn13']) ? $_POST['isbn13'] : ''),'" class="regular-text" />'
        ,'<span class="description">',_('13 digit International Standard Book Number.'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="format">',_('eBook Format'),'</label></th>',"\n\t"
        ,'<td><select name="format" id="format">',ebook_format_options(),'</select></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="muses_success_listing">',sprintf(_('%s Listing'),'<a href="http://muses-success.info/">Muse\'s Success</a>'),'</label></th>',"\n\t"
        ,'<td>http://muses-success.info/browse/view/<input name="muses_success_listing" type="text" id="muses_success_listing" value="',(isset($_POST['isbn13']) ? $_POST['isbn13'] : ''),'" class="medium-text" />'
        ,'<span class="description">',sprintf(_('URL to %s listing.'),'Muse\'s Success'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="wfg_listing">',sprintf(_('%s Listing'), '<a href="http://webfictionguide.com/">Web Fiction Guide</a>'),'</label></th>',"\n\t"
        ,'<td>http://webfictionguide.com/listings/<input name="wfg_listing" type="text" id="wfg_listing" value="" class="medium-text" />'
        ,'<span class="description">',sprintf(_('URL to %s listing.'),'Web Fiction Guide'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr valign="top">',"\n\t"
        ,'<th scope="row"><label for="tmwfd_listing">',sprintf(_('%s Listing'), '<a href="http://www.tonyamoore.com/web-fic-directory/">Web Fic Directory</a>'),'</label></th>',"\n\t"
        ,'<td><input name="tmwfd_listing" type="text" id="tmwfd_listing" value="" class="regular-text" />'
        ,'<span class="description">',sprintf(_('URL to %s listing.'),'Tonya R Moore\'s Web Fic Directory'),'</span></td>',"\n"
        ,'</tr>',"\n"
        ,'<tr>',"\n\t"
        ,'<td colspan="2"><input type="submit" name="create" value="',_('Create e-book'),'" /></td>',"\n"
        ,'</tr>',"\n"
        ,'</table>',"\n"
        ,'<p><span class="required">*</span> ',_('indicates a required field.'),'</p>';
}

function ebook_format_options()
{
    $options = '';
    $handle = opendir(WP_EBOOK_CURRENT_PATH);
    while (false != ($file = readdir($handle)))
    {
        if (strpos($file, '.format.php') != false)
        {
            $format = str_replace('.format.php', '', $file);
            $format = explode('.', $format);
            $options .= '<option value="'.$format[1].'|'.$format[0].'">'.$format[0].' (.'.$format[1].')</option>';
        }
    }
    closedir($handle);
    return $options;
}

function ebook_list_category_options()
{
    $categories = get_categories('type=post');
    $options = '';
    foreach ($categories as $category)
    {
        $options .= '<option value="'.$category->term_id.'">'.$category->cat_name.'</option>';
    }
    return $options;
}

function ebook_license_options()
{
    require_once 'constant.php';
    $options = '';
    foreach ($licenses as $license_abbr => $license_detail)
    {
        $options .= '<option value="'.$license_abbr.'">'.$license_detail[0].'</option>';
    }
    return $options;
}
    
add_action('admin_menu', 'wp_eBook_Export_add_menu_item');

?>
