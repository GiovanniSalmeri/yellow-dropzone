"use strict";
document.addEventListener("DOMContentLoaded", function() {
    var dz = document.getElementsByClassName("dropzone-data");
    for (var i = 0; i < dz.length; i++) {
        var acceptedFiles = dz[i].getAttribute("data-accepted-files");
        var maxFilesize = dz[i].getAttribute("data-max-filesize");
        Dropzone.options['dz'+dz[i].id] = {
            acceptedFiles: acceptedFiles || Dropzone.prototype.defaultOptions.acceptedFiles,
            maxFilesize: maxFilesize || Dropzone.prototype.defaultOptions.maxFilesize,
        }
    }
});
