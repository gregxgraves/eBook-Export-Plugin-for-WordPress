<?php

class PlainText extends eBook {

    public $chapter_buffer = array();
    public $chapter_num = 0;
    public $file_extension = '.txt';
    
    function add_title()
    {
        $txt = $this->meta["book_title"]."\n";
        $txt .= "By ".$this->meta["book_author"]."\n\n";

        if (isset($this->meta["isbn10"]))
        {
            $txt .= $this->meta["isbn10"]."\n";
        }
        if (isset($this->meta["isbn13"]))
        {
            $txt .= $this->meta["isbn13"]."\n";
        }
        
        $txt .= "\n";
        $txt .= $this->meta["license_text"]."\n\n";
        return $txt;
    }

    function add_chapter($num, $content)
    {
        ++$this->chapter_num;
        $txt = "Chapter ".$this->chapter_num."\n\n";
        $txt .= strip_tags($content)."\n\n";
        $this->chapter_buffer[$this->chapter_num] = $txt;
    }
    
    function save_ebook()
    {
        $txt = $this->add_title();
        foreach ($this->chapter_buffer as $num => $text)
        {
            $txt .= $text;
        }
        
        $txt = wordwrap($txt, 80);
        
        file_put_contents(WP_EBOOK_CURRENT_PATH.'eBooks/'.$this->meta['book_title'].'.txt', $txt);
        
    }
    
}

?>
