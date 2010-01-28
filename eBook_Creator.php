<?php

class eBook_Creator {

    public $meta = array();
    public $chapter_category = 0;

    function set_title($title)
    {
        $this->meta['title'] = $title;
    }
    
    function set_isbn($isbn)
    {
        $this->meta['isbn'] = $isbn;
    }
    
    function set_isbn13($isbn)
    {
        $this->meta['isbn13'] = $isbn;
    }
    
    function set_author($author)
    {
        $this->meta['author'] = $author;
    }
    
    function set_license($license)
    {
        $this->meta['license'] = $license;
    }
    
    function set($key, $data)
    {
        $this->meta[$key] = $data;
    }
    
    function rights()
    {
/*
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
*/

        $right = $this->meta['license'];

        switch ($right)
        {
            case 1:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. All Rights Reserved.';
            break;
            case 2:
                $text = $this->meta['title'].' created by '.$this->meta['author'].', released into the public domain. No Rights Reserved.';
            break;
            case 3:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY/3.0) Licence.';
            break;
            case 4:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY-SA/3.0) Licence.';
            break;
            case 5:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY-NC/3.0) Licence.';
            break;
            case 6:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY-NC-SA/3.0) Licence.';
            break;
            case 7:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY-ND/3.0) Licence.';
            break;
            case 8:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under a Creative Commons (BY-NC-ND/3.0) Licence.';
            break;
            case 9:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n".'This EPUB eBook is released under the GNU Free Documentation License 3.0 Licence.';
            break;
            default:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. All Rights Reserved.';
            break;
        }

        $text .= "\n".'ePub-specific XHTML/CSS is in the public domain.';
        
        return $text;
    }

    function add_chapters()
    {
    
        $chapters = get_posts('numberposts=-1&category='.$this->chapter_category.'&orderby=date&order=ASC');
        foreach ($chapters as $chapter)
        {
            $this->add_chapter($chapter->post_title, $chapter->post_content);
        }
    
    }
    
    function add_chapter($title, $content)
    {
        // stub
    }

}
