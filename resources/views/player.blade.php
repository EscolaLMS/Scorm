<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SCORM 1.2 </title>
  <script src="https://cdn.jsdelivr.net/npm/scorm-again@2.2.0/dist/scorm-again.js"></script>
  <style>
    html,body,iframe { width: 100%; height: 100%; padding: 0; margin: 0; border: none}
    iframe { display:block }
  </style>
  <script type="text/javascript">
    const settings = @json($data);
    const token = settings.token;
    const cmi = settings.player.cmi;

    if (settings.version === 'scorm_12') {
        scorm12();
    }
    else if (settings.version === 'scorm_2004') {
        scorm2004();
    }

    function scorm12() {
        window.API = new Scorm12API(settings.player);
        window.API.loadFromJSON(cmi);

        window.API.on('LMSSetValue.cmi.*', function(CMIElement, value) {
            const data = {
                cmi: {
                    [CMIElement]: value
                }
            }

            post(data);
        });

        // window.API.on('LMSGetValue.cmi.*', function(CMIElement) {
        //     get(CMIElement)
        //         .then(res => res.json())
        //         .then(res => {
        //             window.API.LMSSetValue(CMIElement, res)
        //         })
        // });

        window.API.on('LMSCommit', function() {
            const data = {
                cmi: window.API.cmi
            }

            post(data);
        });
    }

    function scorm2004() {
        window.API_1484_11 = new Scorm2004API(settings.player);
        window.API_1484_11.loadFromJSON(cmi);

        window.API_1484_11.on('SetValue.cmi.*', function(CMIElement, value) {
            const data = {
                cmi: {
                    [CMIElement]: value
                }
            }

            post(data);
        });

        // window.API_1484_11.on('GetValue.cmi.*', function(CMIElement) {
        //     get(CMIElement)
        //         .then(res => res.json())
        //         .then(res => {
        //             window.API_1484_11.SetValue(CMIElement, res)
        //         });
        // });

        window.API_1484_11.on('Commit', function() {
            const data = {
                cmi: window.API_1484_11.cmi
            }

            post(data);
        });
    }

    function post(data) {
        if (!token) {
            return;
        }

        fetch(settings.lmsUrl + '/' + settings.uuid, {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
            },
            body: JSON.stringify(data)
        });
    }

    function get(key) {
        if (!token) {
            return;
        }

        return fetch(settings.lmsUrl + '/' + settings.scorm_id + '/' + key, {
            method: 'GET',
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });
    }

  </script>
</head>

<body>
  <iframe src={{ $data['entry_url_absolute'] }}></iframe>
</body>

</html>
