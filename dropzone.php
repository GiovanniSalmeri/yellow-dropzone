<?php
// Dropzone extension, https://github.com/GiovanniSalmeri/yellow-dropzone

class YellowDropzone {
    const VERSION = "0.8.17";
    public $yellow;         //access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("dropzoneDirectory", "media/uploads/");
        $this->yellow->system->setDefault("dropzoneExtensions", ".jpg,.png,.txt,.md");
        $this->yellow->system->setDefault("dropzoneFileSizeMax", "10"); // in MB
        $this->yellow->system->setDefault("dropzoneOverwrite", "0");
        $this->yellow->language->setDefaults(array(
            "Language: en",
            "DropzoneDefaultMessage: Drop files here to upload",
            "DropzoneFileTooBig: File is too big: @filesize MB. Max filesize: @maxFilesize MB",
            "DropzoneInvalidFileType: You can't upload files of this type",
            "DropzoneResponseError: Server responded with @statusCode code",
            "Language: de",
            "DropzoneDefaultMessage: Dateien zum Hochladen hier ablegen",
            "DropzoneFileTooBig: Datei ist zu groß: @filesize MB. Maximale Dateigröße: @maxFilesize MB",
            "DropzoneInvalidFileType: Sie können Dateien dieses Typs nicht hochladen",
            "DropzoneResponseError: Der Server antwortete mit @statusCode Code",
            "Language: fr",
            "DropzoneDefaultMessage: Déposer les fichiers ici pour les télécharger",
            "DropzoneFileTooBig: Le fichier est trop gros: @filesize MB. Taille maximale @maxFilesize MB",
            "DropzoneInvalidFileType: Vous ne pouvez pas télécharger des fichiers de ce type",
            "DropzoneResponseError: Server responded with @statusCode code",
            "Language: it",
            "DropzoneDefaultMessage: Trascina qui i file da caricare",
            "DropzoneFileTooBig: Il file è troppo grande: @filesize MB. Dimensione massima: @maxFilesize MB",
            "DropzoneInvalidFileType: Non è permesso caricare file di questo tipo",
            "DropzoneResponseError: Il server ha risposto con un codice @statusCode",
            "Language: es",
            "DropzoneDefaultMessage: Deje caer los archivos aquí para subirlos",
            "DropzoneFileTooBig: El archivo es demasiado grande: @filesize MB. Tamaño máximo: @maxFilesize MB",
            "DropzoneInvalidFileType: No se pueden subir archivos de este tipo",
            "DropzoneResponseError: El servidor respondió con el código @statusCode",
            "Language: nl",
            "DropzoneDefaultMessage: Laat de bestanden hier vallen om te uploaden",
            "DropzoneFileTooBig: Bestand is te groot: @filesize MB. Maximale bestandsgrootte: @maxFilesize MB",
            "DropzoneInvalidFileType: Je kunt dit type bestanden niet uploaden",
            "DropzoneResponseError: Server reageerde met @statuscode code",
            "Language: pt",
            "DropzoneDefaultMessage: Solte os arquivos aqui para enviar",
            "DropzoneFileTooBig: O arquivo é demasiado grande: @filesize MB. Tamanho máximo: @maxFilesize MB",
            "DropzoneInvalidFileType: Não é possível enviar arquivos deste tipo",
            "DropzoneResponseError: O servidor respondeu com o código @statusCode",
        ));
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
                $maxFilesize = $this->yellow->system->get("dropzoneFileSizeMax")*1000000;
                $allowedExts = preg_split("/\s*,\s*/", $this->yellow->system->get("dropzoneExtensions"));
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
                            $this->yellow->page->status(500);
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
            $maxFilesize = min($this->yellow->toolbox->getNumberBytes(ini_get("post_max_size")), $this->yellow->toolbox->getNumberBytes(ini_get("upload_max_filesize")), $this->yellow->system->get("DropzoneFileSizeMax")*1000000);
            $output .= "<link href=\"{$extensionLocation}dropzone.css\" type=\"text/css\" rel=\"stylesheet\" />\n";
            $output .= "<script src=\"{$extensionLocation}dropzone-helper.js\"></script>\n"; // 1st
            $output .= "<script src=\"{$extensionLocation}dropzone.js\"></script>\n"; // 2nd
            $output .= "<script>\n"; // 3rd
            $output .= "var d = Dropzone.prototype.defaultOptions;\n";
            $output .= "d.acceptedFiles = ".json_encode($this->yellow->system->get("dropzoneExtensions")).";\n";
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
