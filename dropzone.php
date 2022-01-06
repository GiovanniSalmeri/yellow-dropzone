<?php
// Dropzone extension, https://github.com/GiovanniSalmeri/yellow-dropzone

class YellowDropzone {
    const VERSION = "0.8.16";
    public $yellow;         //access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("dropzoneDirectory", "media/uploads/");
        $this->yellow->system->setDefault("dropzoneAcceptedFiles", ".jpg,.png,.txt,.md");
        $this->yellow->system->setDefault("dropzoneMaxFilesize", "10"); // in MB
        $this->yellow->system->setDefault("dropzoneOverwrite", "0");
    }

    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="dropzone" && ($type=="block" || $type=="inline")) {
            list($acceptedFiles, $maxFilesize) = $this->yellow->toolbox->getTextArguments($text);
            $uniqid = uniqid();
            $output .= "<form id=\"dz".$uniqid."\" action=\"".htmlspecialchars($this->yellow->page->getLocation(true))."\" class=\"dropzone\">\n";
            $output .= "<div class=\"fallback\"><input name=\"file\" type=\"file\" multiple=\"multiple\" /></div>\n";
            $output .= "</form>\n";
            if ($acceptedFiles || $maxFilesize) {
                $output .= "<span class=\"dropzone-data\" id=\"".$uniqid."\" ";
                if ($acceptedFiles) $output .= "data-accepted-files = \"".htmlspecialchars($acceptedFiles)."\" ";
                if ($maxFilesize) $output .= "data-max-filesize = \"".htmlspecialchars($maxFilesize)."\" ";
                $output .= "></span>\n";
            }
            if ($_FILES) {
                $maxFilesize = $this->yellow->system->get("dropzoneMaxFilesize")*1000000;
                $allowedExts = preg_split("/\s*,\s*/", $this->yellow->system->get("dropzoneAcceptedFiles"));
                foreach ((array)$_FILES['file']['tmp_name'] as $key => $tempFile) {
                    $target = pathinfo(((array)$_FILES['file']['name'])[$key]);
                    $fileType = strtoloweru($target["extension"]);
                    if (in_array(".".$fileType, $allowedExts) && ((array)$_FILES['file']['size'])[$key]<=$maxFilesize) {
                        $fileCount = 0;
                        if (!$this->yellow->system->get("dropzoneOverwrite")) {
                            while (file_exists($this->yellow->system->get("dropzoneDirectory").$target["filename"].($fileCount ? ".".$fileCount : "").".".$target["extension"])) {
                                $fileCount += 1;
                            }
                        }
                        if (!@move_uploaded_file($tempFile, $this->yellow->system->get("dropzoneDirectory").$target["filename"].($fileCount ? ".".$fileCount : "").".".$target["extension"])) {
                            $this->yellow->page->clean(500);
                        };
                    }
                }
                $_FILES = null; // avoids multiple submissions if more dropzones in the same page
            }
        }
        return $output;
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name == "header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $maxFilesize = min($this->yellow->toolbox->getNumberBytes(ini_get("post_max_size")), $this->yellow->toolbox->getNumberBytes(ini_get("upload_max_filesize")), $this->yellow->system->get("dropzoneMaxFilesize")*1000000);
            $output .= "<link href=\"{$extensionLocation}dropzone.css\" type=\"text/css\" rel=\"stylesheet\" />\n";
            $output .= "<script src=\"{$extensionLocation}dropzone-helper.js\"></script>\n"; // 1st
            $output .= "<script src=\"{$extensionLocation}dropzone.js\"></script>\n"; // 2nd
            $output .= "<script>\n"; // 3rd
            $output .= "var d = Dropzone.prototype.defaultOptions;\n";
            $output .= "d.acceptedFiles = ".json_encode($this->yellow->system->get("dropzoneAcceptedFiles")).";\n";
            $output .= "d.maxFilesize = ".$maxFilesize.";\n";
            $output .= "d.uploadMultiple = true;\n";
            foreach (["DefaultMessage", "FileTooBig", "InvalidFileType", "ResponseError"] as $string) {
                $output .= "d.dict".$string." = ".json_encode(preg_replace('/@(\w+)/', '{{$1}}', $this->yellow->language->getText("dropzone".$string))).";\n";
            }
            $output .= "d.dictFallbackMessage = null;\n";
            $output .= "</script>\n";
        }
        return $output;
    }
}
