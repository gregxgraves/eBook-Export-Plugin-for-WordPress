<?php

class eBook_Creator {

    public $meta = array();
    public $chapter_category = 0;

    function set_book_info($book = array())
    {
        $this->meta = $book;
    }
    
    function rights()
    {

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
                $text .= "\n"._('This EPUB eBook is released under a Creative Commons (BY-NC-ND/3.0) Licence.');
            break;
            case 9:
                $text = $this->meta['title'].' Copyright '.date('Y').' '.$this->meta['author'].'. Some Rights Reserved.';
                $text .= "\n"._('This EPUB eBook is released under the GNU Free Documentation License 3.0 Licence.');
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
