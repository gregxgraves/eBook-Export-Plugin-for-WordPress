<?php

class PlainText extends eBook {

    public $chapter_buffer = array();
    public $chapter_num = 0;
    
    function add_title()
    {
        $txt = $this->meta["title"]."\n";
        $txt .= "By ".$this->meta["author"]."\n\n";

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
    }

    function add_chapter($num, $content)
    {
        ++$this->chapter_num;
        $txt = "Chapter ".$num."\n\n";
        $txt .= strip_tags($content)."\n\n";
        $this->chapter_buffer[$this->chapter_num] = $txt;
    }
    
    function save_ebook()
    {
        $txt = $this->title();
        foreach ($this->chapter_buffer as $num => $text)
        {
            $txt .= $text;
        }
        
        $txt = wordwrap($txt, 80);
        
        file_put_contents(WP_EBOOK_CURRENT_PATH.'eBooks/'.$this->meta['title'].'.txt', $txt);
        
    }
    
}

?>
