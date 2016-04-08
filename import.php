<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 3/8/2016
 * Time: 5:39 PM
 */
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

?>

<!DOCTYPE HTML>
 <html>
    <head>
      <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>
      <style>
        .dropZoneParent{
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          flex-direction: row;
        }
        .dropZoneParent form{
          padding : 0;
          width: 100%;
          display: flex;
          align-items: center;
          flex-direction: row;

        }
        .dropZoneParent form .dz-message{
          margin: 0 auto;
        }

        body{
          height: 100%;
          overflow: hidden;
        }


      </style>
    </head>
<body>
<h1 style="text-align: center;">Import Data</h1>
<div class="row center-sm" style="
      display: flex;
      align-items: center;
      position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;">

  <div class="col-sm-6">
    <div id="dropzone" class="dropZoneParent"><form action="/util/php/serve/importData.php" class="dropzone needsclick" id="importUpload" style="padding-bottom: 0"></form></div>
    <a href="#" class="button button-block button-rounded button-primary button-large" id="uploadFile">Submit</a>
  </div>
</div>


</body>
<script>

  HTMLElement.prototype.wrap = function (elms) {
    // Convert `elms` to an array, if necessary.
    if (!elms.length) elms = [elms];

    // Loops backwards to prevent having to clone the wrapper on the
    // first element (see `child` below).
    for (var i = elms.length - 1; i >= 0; i--) {
      var child = (i > 0) ? this.cloneNode(true) : this;
      var el = elms[i];

      // Cache the current parent and sibling.
      var parent = el.parentNode;
      var sibling = el.nextSibling;

      // Wrap the element (is automatically removed from its current
      // parent).
      child.appendChild(el);

      // If the element had a sibling, insert the wrapper before
      // the sibling to maintain the HTML structure; otherwise, just
      // append it to the parent.
      if (sibling) {
        parent.insertBefore(child, sibling);
      } else {
        parent.appendChild(child);
      }
    }
  };


  $(document).ready(function(){

    <?php
    if(isset($_GET['success'])){
    echo 'toastr[\'success\']("All data has been imported successfully","Done!");';
    }
    ?>

    Dropzone.options.importUpload = {
      paramName: "file", // The name that will be used to transfer the file
      maxFilesize: 25, // MB
      maxFiles :1,
      autoProcessQueue : false,
      dictDefaultMessage : "Drop files here or click to upload",
      acceptedFiles : "text/plain",
      init : function() {
        var myDropzone = this;
        this.on("addedfile", function (file) {
          console.log(file);
          document.createElement('div').wrap(file.previewElement);
          var removeButton = Dropzone.createElement("" +
          "<button class='danger' style='display: block; margin: 0 auto; margin-bottom: 15px;'>Remove file</button>");

          var _this = this;
          $(file.previewElement).find(".dz-image").css("background","linear-gradient(to bottom, #eee,  #4CC417)");

          removeButton.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            file.previewElement.parentElement.remove();
            _this.removeFile(file);

          });



          file.previewElement.parentElement.appendChild(removeButton);
        });

        this.on("error", function (file) {
          $(file.previewElement).find(".dz-image").css("background","linear-gradient(to bottom, #eee, #E55451)");
        });
        this.on("success", function(file, responseText) {
          if(responseText == "Success"){
            var url = window.location.href;
            if (url.indexOf('?') > -1){
              url += '&success'
            }else{
              url += '?success'
            }
            window.location = "/import.php?success"
          }
          else{
            toastr['error']("Check console for more info","Import failed");
            console.log(responseText);
          }

        });

        $("#uploadFile").click(function(){
          myDropzone.processQueue();
        });

      },
      maxfilesexceeded: function(file) {
        this.removeAllFiles();
        this.addFile(file);
      }

    };
  });
</script>
</html>