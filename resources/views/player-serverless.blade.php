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
    </head>

    <body>
        <div id="loading">loading</div>
        <script type="module">
            function scorm12(settings) {
                window.API = new Scorm12API(settings);
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
                // TODO: Implement your BACKEND endpoint for set data:
                // this is authenticated endpoint  Route::post('/{uuid}', [ScormTrackController::class, 'set']);

                console.log(
                    "TODO: Implement your BACKEND endpoint for set data:",
                    data
                );
                return new Promise((resolve, reject) => {
                    resolve(data);
                });
            }

            function get(key) {
                // TODO: Implement your BACKEND endpoint for get key:
                // this is authenticated endpoint  Route::get('/{scoId}/{key}', [ScormTrackController::class, 'get']);
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

                const settings = window.ScormSettings.data;

                if (scormObj.version === "2004") {
                    scorm2004(settings);
                } else if (scormObj.version === "AICC") {
                    aicc(settings);
                } else {
                    scorm12(settings);
                }

                const iframeEl = document.getElementById("iframe_el");

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

            function loadScormSCO(uuid, registration, retry = true) {
                fetch(`/api/scorm/show/${uuid}`)
                    .then((res) => res.json())
                    .then(async (settings) => {
                        window.ScormSettings = settings;
                        const zipUrl = settings.data.entry_url_zip;
                        // 4. check if zip file exists
                        const exists = await fetch(zipUrl, { method: "HEAD" });
                        if (exists.ok) {
                            // 4a. send zip url to service worker
                            registration.active.postMessage(zipUrl);
                        } else {
                            // 4b. create zip file
                            const exists = await fetch(
                                `/api/scorm/zip/${uuid}`
                            );
                            if (exists.ok) {
                                // 5b. zip created, recall function
                                retry &&
                                    loadScormSCO(uuid, registration, false);
                            }
                        }
                    });
            }

            function init() {
                // 1. show loading
                loading();
                // 2. register service worker
                register("/api/scorm/service-worker").then((reg) => {
                    navigator.serviceWorker.ready.then((registration) => {
                        loadScormSCO("{{$data['uuid']}}", registration);
                        loading(true);
                    });
                });
            }

            init();
        </script>
        <div id="iframe_el"></div>
    </body>
</html>
