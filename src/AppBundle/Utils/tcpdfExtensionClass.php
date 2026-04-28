<?php

namespace AppBundle\Utils;
 
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

class tcpdfExtensionClass extends TCPDF

{

    protected $last_page_flag = false;

    //Page header
    public function Header() {
        // set JPEG quality
        $this->setJPEGQuality(75);
        // Image example with resizing
        $this->Image($this->header_logo, 10, 5, 40, 20, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if ($this->title == "Rapport Drankstand")
        {
            $this->writeHTMLCell(230, 10, 60, 5, $this->header_title, 0, 0, false, true, "C", true);
            $this->SetFont('helvetica', '', 12);
            $this->writeHTMLCell(230, 6, 61, 22, $this->header_string, 0, 0, false, true, "L", true);
            $this->writeHTMLCell(0, 1, 5, 25, "", "B", 1, false, true, "C", true);
            $this->writeHTMLCell(0, 1, 5, 30, "", 0, 1, false, true, "C", true);
        }
        else
        {
            $this->writeHTMLCell(80, 10, 60, 5, $this->header_title, 0, 0, false, true, "L", true);
            $this->SetFont('helvetica', '', 12);
            $this->writeHTMLCell(80, 10, 61, 15, $this->header_string, 0, 0, false, true, "L", true);
            $this->writeHTMLCell(0, 1, 5, 25, "", "B", 1, false, true, "C", true);
            $this->writeHTMLCell(0, 1, 5, 30, "", 0, 1, false, true, "C", true);

        }


    }

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }

    public function Footer()
    {
        if ($this->title == "Rapport Drankstand")
        {
            $this->SetFont('helvetica', '', 10);
            $paginatext = "Pagina : " . trim($this->getAliasNumPage()) . "/" . trim($this->getAliasNbPages());
            $this->writeHTMLCell(40, 5, 250, 200, trim($paginatext), 0, 1, false, true, "R", true);

        }
        else
        {
            $this->SetFont('helvetica', '', 10);
            $paginatext = "Pagina : " . trim($this->getAliasNumPage()) . "/" . trim($this->getAliasNbPages());
            $this->writeHTMLCell(80, 5, 65, 200, $paginatext, 0, 1, false, true, "L", true);
        }
    }


}