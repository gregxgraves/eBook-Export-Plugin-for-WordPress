<?php

class PlainText extends eBook {
    
    function title()
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

    function chapter($title, $content)
    {
        $txt = $title."\n\n";
        $txt .= strip_tags($content)."\n\n";
        return $txt;
    }
    
    function save_ebook()
    {

    }
    
}

?>
