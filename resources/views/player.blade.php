<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SCORM 1.2 </title>
  <script src="https://cdn.jsdelivr.net/npm/scorm-again@latest/dist/scorm-again.js"></script>
  <style>
    html,body,iframe { width: 100%; height: 100%; padding: 0; margin: 0; border: none}
  </style>
  <script type="text/javascript">
    let settings = @json($data);
    if (settings.version === 'scorm_12') {
        scorm12();
    }
    else if (settings.version === 'scorm_2004') {
        scorm2004();
    }

    function scorm12() {
        window.API = new Scorm12API(settings.player);
        console.log(window.API);

        window.API.on('LMSSetValue.cmi.*', function(CMIElement, value) {
            // TODO push this data though post message
            console.log(arguments);
        });
    }

    function scorm2004() {
        window.API_1484_11 = new Scorm2004API(settings.player);
        console.log(window.API_1484_11);

        window.API_1484_11.on("SetValue.cmi.* ", function(CMIElement, value) {
            console.log(arguments);
        });
    }

  </script>
</head>

<body>
  <iframe src={{ $data['entry_url_absolute'] }}></iframe>
</body>

</html>
