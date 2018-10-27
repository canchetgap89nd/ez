(function ($) {
  $.extend({
    uploadPreview : function (options) {

      // Options + Defaults
      var settings = $.extend({
        input_field: ".image-input",
        preview_box: ".image-preview",
        label_field: ".image-label",
        label_default: "Choose File",
        label_selected: "Change File",
        no_label: false,
        success_callback : null,
      }, options);

      // Check if FileReader is available
      if (window.File && window.FileList && window.FileReader) {
        if (typeof($(settings.input_field)) !== 'undefined' && $(settings.input_field) !== null) {
          $(settings.input_field).change(function() {
            var files = this.files;

            if (files.length > 0) {
              var file = files[0];
              var reader = new FileReader();

              // Load file
              reader.addEventListener("load",function(event) {
                var loadedFile = event.target;

                // Check format
                if (file.type.match('image')) {
                  // Image
                  $(settings.preview_box).css("background-image", "url("+loadedFile.result+")");
                  $(settings.preview_box).css("background-size", "cover");
                  $(settings.preview_box).css("background-position", "center center");
                } else if (file.type.match('audio')) {
                  // Audio
                  $(settings.preview_box).html("<audio controls><source src='" + loadedFile.result + "' type='" + file.type + "' />Your browser does not support the audio element.</audio>");
                } else {
                  alert("This file type is not supported yet.");
                }
              });

              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).remove(); //html(settings.label_selected) change label when select file
              }

              // Read the file
              reader.readAsDataURL(file);

              // Success callback function call
              if(settings.success_callback) {
                settings.success_callback();
              }
            } else {
              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).html(settings.label_default);
              }

              // Clear background
              $(settings.preview_box).css("background-image", "none");

              // Remove Audio
              $(settings.preview_box + " audio").remove();
            }
          });
        }
      } else {
        alert("You need a browser with file reader support, to use this form properly.");
        return false;
      }
    }
  });
})(jQuery);


var storedFiles = [];
//preview multiple images
function handleFileSelect(evt) {
   var files = evt.target.files; // FileList object

   var idElement = evt.target.getAttribute('id');

   // Loop through the FileList and render image files as thumbnails.
   for (var i = 0, f; f = files[i]; i++) {

       // Only process image files.
       if (!f.type.match('image.*')) {
          alert("This file type is not supported yet.");
          continue;
       }

       storedFiles.push(f);

       var reader = new FileReader();

       // Closure to capture the file information.
       reader.onload = (function(theFile) {
           var nameFile = theFile.name;
           return function(e) {
               // Render thumbnail.
               var span = document.createElement('span');
               span.id = nameFile;
               span.innerHTML = ['<img class="thumb" src="', e.target.result,
                   '" title="', escape(nameFile), '"/><span data-file="'+nameFile+'" class="remove-img" onclick="removeImgList(\''+nameFile+'\');"><i class="fa fa-close"></i></span>'
               ].join('');

               document.getElementById('list').insertBefore(span, null);
           };
       })(f);

       // Read in the image file as a data URL.
       reader.readAsDataURL(f);
   }

}

function removeImgList(nameFile) {

    for(var i=0;i<storedFiles.length;i++) {
        if(storedFiles[i].name === nameFile) {
            storedFiles.splice(i,1);
            break;
        }
    }
    document.getElementById(nameFile).remove();
}

function handleForm(e) {
    e.preventDefault();
    var data = new FormData();
    
    for(var i=0, len=storedFiles.length; i<len; i++) {
        data.append('files', storedFiles[i]); 
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'handler.cfm', true);
    
    xhr.onload = function(e) {
        if(this.status == 200) {
            console.log(e.currentTarget.responseText);  
            alert(e.currentTarget.responseText + ' items uploaded.');
        }
    }
    
    xhr.send(data);
}

document.getElementById('files').addEventListener('change', handleFileSelect, false);

