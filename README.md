# Dropzone 0.8.17

Drag-and-drop file uploads with image previews.

<p align="center"><img src="dropzone-screenshot.png?raw=true" width="795" height="586" alt="Screenshot"></p>

## How to create a drag-and-drop area for file uploads

Create a `[dropzone]` shortcut. 

Please remark that in this way you will allow visitors, without any authentication, to upload files to your server. To limit abuses you could want for example to make the page private with a setting `Status: unlisted`.

The following arguments are available, all of which are optional:

`AcceptedFiles` = a comma separated list of file extensions allowed   
`MaxFilesize` = maximum size of a single file, in MB  

These arguments, if specified, override the general settings.

## Example

Embedding a drag-and-drop area with various options:

    [dropzone]
    [dropzone ".jpg,.gif,.png"]
    [dropzone .zip 100]

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`DropzoneDir` (default: `media/uploads/`) = directory where files are uploaded  
`DropzoneAcceptedFiles` (default: `.jpg,.png,.txt,.md`) = a comma separated list of file extensions allowed  
`DropzoneMaxFilesize` (default: `10`) = maximum size of a single file, in MB  
`DropzoneOverwrite` (default: `0`) = whether files with the same name are overwritten (if not, after `file.txt` they are renamed `file.1.txt`, `file.2.txt` and so on)  

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-dropzone/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

This extension uses [DropzoneJS](https://www.dropzonejs.com/) by Matias Meno.

## Developer

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-dropzone/issues).
