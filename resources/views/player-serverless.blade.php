<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>SCORM 1.2</title>
        <script src="https://cdn.jsdelivr.net/npm/scorm-again@2.2.0/dist/scorm-again.js"></script>
        <style>
            #iframe_el,
            iframe,
            body,
            html {
                width: 100%;
                height: 100%;
                border: 0;
                padding: 0;
                margin: 0;
            }
            html,
            body,
            iframe {
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                border: none;
            }

            iframe {
                display: block;
            }
        </style>

        <!-- <script type="text/javascript">
        const settings = @json($data);
        const token = settings.token;
        const cmi = settings.player.cmi;

        if (settings.version === 'scorm_12') {
            scorm12();
        } else if (settings.version === 'scorm_2004') {
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
    </script> -->
    </head>

    <body>
        <div id="loading">loading</div>
        <script type="module">
            function scorm12(settings) {
                window.API = new Scorm12API(settings);
                //window.API.loadFromJSON(cmi);

                window.API.on(
                    "LMSSetValue.cmi.*",
                    function (CMIElement, value) {
                        const data = {
                            cmi: {
                                [CMIElement]: value,
                            },
                        };

                        post(data);
                    }
                );

                window.API.on("LMSGetValue.cmi.*", function (CMIElement) {
                    get(CMIElement)
                        //.then((res) => res.json())
                        .then((res) => {
                            window.API.LMSSetValue(CMIElement, res);
                        });
                });

                window.API.on("LMSCommit", function () {
                    const data = {
                        cmi: window.API.cmi,
                    };

                    post(data);
                });
            }

            function scorm2004(settings) {
                window.API_1484_11 = new Scorm2004API(settings);
                //window.API_1484_11.loadFromJSON(cmi);

                window.API_1484_11.on(
                    "SetValue.cmi.*",
                    function (CMIElement, value) {
                        const data = {
                            cmi: {
                                [CMIElement]: value,
                            },
                        };

                        post(data);
                    }
                );

                window.API_1484_11.on("GetValue.cmi.*", function (CMIElement) {
                    get(CMIElement)
                        //.then((res) => res.json())
                        .then((res) => {
                            window.API_1484_11.SetValue(CMIElement, res);
                        });
                });

                window.API_1484_11.on("Commit", function () {
                    const data = {
                        cmi: window.API_1484_11.cmi,
                    };

                    post(data);
                });
            }

            function aicc(settings) {
                window.API = new AICC(settings);
            }

            function post(data) {
                console.log(
                    "TODO: Implement your BACKEND endpoint for set data:",
                    data
                );
                return new Promise((resolve, reject) => {
                    resolve(data);
                });
            }

            function get(key) {
                console.log(
                    "TODO: Implement your BACKEND endpoint for get key:",
                    key
                );
                return new Promise((resolve, reject) => {
                    resolve(key);
                });
            }

            navigator.serviceWorker.addEventListener("message", (event) => {
                const scormObj = event.data.scormObj;
                // Those Settings should be fetched from the backend
                const settings = @json($data);

                console.log("settings", settings);

                if (scormObj.version === "2004") {
                    scorm2004(settings);
                } else if (scormObj.version === "AICC") {
                    aicc(settings);
                } else {
                    scorm12(settings);
                }

                const scoEl = document.getElementById("scos");
                const iframeEl = document.getElementById("iframe_el");

                scoEl.innerHTML == "";

                loading(false);

                const iframe = document.createElement("iframe");
                iframe.src = `${scormObj.PREFIX}/${settings.entry_url}`;
                iframeEl.innerHTML = "";
                iframeEl.appendChild(iframe);



            });

            function register(url = "serviceworker.js") {
                return new Promise((resolve, reject) => {
                    navigator.serviceWorker.register(url).then((reg) => {
                        if (!reg.active) {
                            (reg.installing || reg.waiting).addEventListener(
                                "statechange",
                                () => {
                                    resolve(reg.active);
                                }
                            );
                        } else {
                            requestAnimationFrame(() => resolve(reg.active));
                        }
                    });
                });
            }
            function loading(isLoading = true) {
                document.getElementById("loading").style.display = isLoading
                    ? "flex"
                    : "none";
            }

            function init() {
                loading();
                // prettier-ignore
                register("/api/scorm/service-worker").then((reg) => {
                    navigator.serviceWorker.ready.then((registration) => {
                        //console.log("ready", registration);
                        loading(false);
                    });
                }).then(() => {
                    navigator.serviceWorker.ready.then((registration) => {
                        registration.active.postMessage("{{ $data['entry_url_zip'] }}");
                        loading(true);
                     });
                });
            }

            init();
        </script>
        <div id="scos"></div>
        <div id="iframe_el"></div>
        <!-- <iframe src={{ $data['entry_url_absolute'] }}></iframe> -->
    </body>
</html>
