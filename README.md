# Dropzone 0.8.17

Drag-and-drop file uploads with image previews.

<p align="center"><img src="dropzone-screenshot.png?raw=true" alt="Screenshot"></p>

## How to install an extension

[Download ZIP file](https://github.com/GiovanniSalmeri/yellow-dropzone/archive/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to create a drag-and-drop area for file uploads

Create a `[dropzone]` shortcut. 

Please remark that in this way you will allow visitors, without any authentication, to upload files to your server. To limit abuses you could want for example to make the page private with a setting `Status: unlisted`.

The following arguments are available, all of which are optional:

`AcceptedFiles` = a comma separated list of file extensions allowed   
`MaxFilesize` = maximum size of a single file, in MB  

These arguments, if specified, override the general settings.

## Examples

Embedding a drag-and-drop area with various options:

    [dropzone]
    [dropzone ".jpg,.gif,.png"]
    [dropzone .zip 100]

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`DropzoneDirectory` = directory where files are uploaded  
`DropzoneExtensions` = a comma separated list of file extensions allowed  
`DropzoneFileSizeMax` = maximum size of a single file, in MB  
`DropzoneOverwrite` = whether files with the same name are overwritten (if not, after `file.txt` they are renamed `file.1.txt`, `file.2.txt` and so on)  

## Acknowledgements

This extension includes [DropzoneJS](https://www.dropzonejs.com/) by Matias Meno. Thank you for the good work.

## Developer

Giovanni Salmeri. [Get help](https://datenstrom.se/yellow/help/).
