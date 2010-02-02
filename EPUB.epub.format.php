<?php

class epub extends eBook {

    public $chapter_num = 0;
    public $file_extension = '.epub';
    public $pretext_buffer = array();
    public $chapter_buffer = array();
    public $posttext_buffer = array();

    function add_chapter($title, $text)
    {
        ++$this->chapter_num;

        $chapter = file_get_contents(WP_EBOOK_CURRENT_PATH.'/templates/ePub/chapter.html');
        
        $chapter = str_replace('(title)', $title, $chapter);
        $chapter = str_replace('(booktitle)', $this->meta['title'], $chapter);
        $chapter = str_replace('(chapter_text)', wpautop($text), $chapter);

        $this->chapter_buffer[$this->chapter_num] = $chapter;
        
    }
    
    function add_copyright()
    {

        $copyright = file_get_contents(WP_EBOOK_CURRENT_PATH.'templates/ePub/copyright.html');
        $copyright = str_replace('(title)', $this->meta['title'], $copyright);
        $copyright = str_replace('(rights)', nl2br($this->rights()), $copyright);
        
        if (isset($this->meta['isbn']))
        {
            $isbn = $this->meta['isbn'];
        }
        if (isset($this->meta['isbn13']) && isset($isbn))
        {
            $isbn .= '<br />'.$this->meta['isbn13'];
        }
        $copyright = str_replace('(isbn)', $isbn, $copyright);
        return $copyright;
    }

    function create_ncx()
    {

        $blog_title = get_bloginfo('name');
        $site_url = get_bloginfo('siteurl');

        $file = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE ncx
  PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
   <head>
      <!--The following four metadata items are required for all
            NCX documents, including those conforming to the relaxed
            constraints of OPS 2.0-->
      <meta name="dtb:uid" content="'.sha1($blog_title.$this->meta['title']).'"/>
      <meta name="epub-creator" content="'.$blog_title.' ('.$site_url.')"/>
      <meta name="dtb:depth" content="1"/>
      <meta name="dtb:totalPageCount" content="0"/>
      <meta name="dtb:maxPageNumber" content="0"/>
   </head>
    <docTitle>
      <text>'.$this->meta['title'].'</text>
   </docTitle>
   <docAuthor>
      <text>'.$this->meta['author'].'</text>
   </docAuthor>
   <navMap>
   ';

   $file .= '<navPoint id="navpoint-1" playOrder="1">
         <navLabel>
            <text>Title Page</text>
         </navLabel>
         <content src="title.html"/>
      </navPoint>
      <navPoint id="navpoint-2" playOrder="2">
         <navLabel>
            <text>Copyright Information</text>
         </navLabel>
         <content src="copyright.html"/>
      </navPoint>';

   foreach ($this->chapter_buffer as $num => $text)
   {
        $file .= '<navPoint id="navpoint-'.($num+2).'" playOrder="'.($num+2).'">
         <navLabel>
            <text>Chapter '.$num.'</text>
         </navLabel>
         <content src="chapter'.$num.'.html"/>
      </navPoint>';
   }

   $file .= '   </navMap>
</ncx>
   ';

    return $file;


    }
    
    function titlepage()
    {

        $title = file_get_contents(WP_EBOOK_CURRENT_PATH.'templates/ePub/title.html');

        $title = str_replace('(title)', $this->meta['title'], $title);
        $title = str_replace('(author)', $this->meta['author'], $title);
        $title = str_replace('(blogname)', get_bloginfo('name'), $title);
        $title = str_replace('(blogurl)', get_bloginfo('siteurl'), $title);
        
        $listings = array();
        if (isset($this->meta['muses-success']))
        {
            $listings[] = '<a href="'.$this->meta['muses-success'].'">Muse\'s Success</a>';
        }
        if (isset($this->meta['webfictionguide']))
        {
            $listings[] = '<a href="'.$this->meta['webfictionguide'].'">Web Fiction Guide</a>';
        }
        if (isset($this->meta['web-fic-directory']))
        {
            $listings[] = '<a href="'.$this->meta['web-fic-directory'].'">Web-Fic-Directory</a>';
        }
        
        $title = str_replace('(webficlistings)', implode(' | ', $listings), $title);
        
        return $title;
    }
    
    function create_epb_opf()
    {
        $blog_title = get_bloginfo('name');
        $site_url = get_bloginfo('siteurl');
        
        $file = '<?xml version="1.0" encoding="UTF-8"?>

<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="EPB-UUID" version="2.0">
   <metadata xmlns:opf="http://www.idpf.org/2007/opf"
             xmlns:dc="http://purl.org/dc/elements/1.1/">
      <dc:title>'.$this->meta['title'].'</dc:title>
      <dc:creator opf:role="aut">'.$this->meta['author'].'</dc:creator>
      <dc:date opf:event="original-publication"/>
      <dc:publisher>'.$blog_title.' ('.$site_url.')</dc:publisher>
      <dc:date opf:event="epub-publication">'.date('Y-m-d').'</dc:date>
      <dc:subject/>
      <dc:source>'.$blog_title.'</dc:source>
      <dc:rights>
        '.$this->rights().'
      </dc:rights>
      <dc:identifier id="EPB-UUID">urn:uuid:D56BD73C-6BFE-1014-8E4E-B5B05E9FBBCD</dc:identifier>
      <dc:language>en-gb</dc:language>
   </metadata>';

   $file .= '<manifest>
      <!-- Content Documents -->
      <item id="titlepage" href="title.html" media-type="text/html"/>
      <item id="copyright" href="copyright.html" media-type="text/html"/>
      ';

   $temp = '';
   $temp .= '<itemref idref="titlepage" linear="yes"/>';
   $temp .= '<itemref idref="copyright" linear="yes"/>';
   foreach ($this->chapter_buffer as $num => $text)
   {
        $file .= '<item id="chapter-00'.$num.'" href="chapter'.$num.'.html" media-type="text/html"/>';
        $temp .= '<itemref idref="chapter-00'.$num.'" linear="yes"/>';
   }

   $file .= '<!-- CSS Style Sheets -->
   <item id="main-css" href="css/style.css" media-type="text/css"/>
   <!-- NCX -->
      <item id="ncx" href="epb.ncx" media-type="application/x-dtbncx+xml"/>
   </manifest>
   ';
   
   $file .= '<spine toc="ncx">'.$temp.'</spine>
</package>';


    return $file;


    }
    
    function save_ebook()
    {

        $zip = new ZipArchive;
        
        if (file_exists(WP_EBOOK_CURRENT_PATH.'eBooks/'.sanitize_title($this->meta['title']).'.epub'))
        {
            $zip->open(WP_EBOOK_CURRENT_PATH.'eBooks/'.sanitize_title($this->meta['title']).'.epub', ZIPARCHIVE::OVERWRITE);
        } else {
            $zip->open(WP_EBOOK_CURRENT_PATH.'eBooks/'.sanitize_title($this->meta['title']).'.epub', ZIPARCHIVE::CREATE);
        }

        $zip->addEmptyDir('META-INF');
            
        $container = '<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="urn:oasis:names:tc:opendocument:xmlns:container" version="1.0">
   <rootfiles>
      <rootfile full-path="OPS/epb.opf" media-type="application/oebps-package+xml"/>
   </rootfiles>
</container>';
            
        $zip->addFromString('META-INF/container.xml', $container);
            
        $zip->addEmptyDir('OPS');
        $zip->addEmptyDir('OPS/css');
        $epbopf = $this->create_epb_opf();
        $ncx = $this->create_ncx();
        $titlepage = $this->titlepage();
        $copyright = $this->add_copyright();
        $zip->addFromString('mimetype', 'application/epub+zip');
        $zip->addFromString('OPS/epb.opf', $epbopf);
        $zip->addFile(WP_EBOOK_CURRENT_PATH.'templates/ePub/style.css','OPS/css/style.css');
        $zip->addFile(WP_EBOOK_CURRENT_PATH.'templates/ePub/titlepage.css', 'OPS/css/titlepage.css');
        $zip->addFromString('OPS/epb.ncx', $ncx);
        $zip->addFromString('OPS/title.html', $titlepage);
        $zip->addFromString('OPS/copyright.html', $copyright);
        foreach ($this->chapter_buffer as $chapter_num => $chapter_content)
        {
            $zip->addFromString('OPS/chapter'.$chapter_num.'.html', $chapter_content);
        }
        
        $zip->close();
    
        return true;

    }

}

?>
