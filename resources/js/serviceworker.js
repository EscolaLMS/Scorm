// self.importScripts("modules/jszip.js");
// self.importScripts("modules/mimetypes.js");
// self.importScripts("modules/txml.js");
const t_xml = txml();
//const zip = new JSZip();
const FOLDER_PREFIX = "__scorm__";
const resolvers = {};
const disabled = {};

const getSCORMDetails = (xml, sco = "index.html") => {
    const result = {
        // TODO this is very simple test
        version: xml.includes("2004")
            ? "2004"
            : xml.includes("AICC")
            ? "AICC"
            : "1.2",
        resources: {},
    };
    try {
        const dom = t_xml.simplify(t_xml.parse(xml));
        result.resources = Array.isArray(dom.manifest.resources.resource)
            ? dom.manifest.resources.resource
            : [dom.manifest.resources.resource];
    } catch (error) {
        console.log("error", error);
    }
    return result;
};

self.addEventListener("install", (e) => {
    // activate immediately so we can start serving, files as soon as possible
    this.skipWaiting();
});

self.addEventListener("activate", (e) => {
    e.waitUntil(this.clients.claim());
});

self.addEventListener("message", async (e) => {
    const url = e.data;
    const urlTrimmed = url.replace(/([^\w ]|_)/g, "");
    const request = await fetch(url);
    const blob = await request.blob();
    const PREFIX = `${FOLDER_PREFIX}/${urlTrimmed}/${FOLDER_PREFIX}`;
    resolvers[urlTrimmed] = resolvers[urlTrimmed] || new JSZip();
    const zip = resolvers[urlTrimmed];

    zip.loadAsync(blob).then(async function (zip) {
        // find entry point
        const xml = await zip.file("imsmanifest.xml").async("string");
        const scormObj = getSCORMDetails(xml);
        scormObj.PREFIX = PREFIX;
        /*
    scormObj.entrypoint =
      PREFIX +
      (scormObj.entrypoint.charAt(0) === "/" ? "" : "/") +
      scormObj.entrypoint;
    */

        const allClients = await clients.matchAll();

        for (const client of allClients) {
            client.postMessage({
                scormObj,
                urlTrimmed,
                msg: "loaded zip",
                file: e.data,
            });
        }
    });
});

// Fetching content using Service Worker
self.addEventListener("fetch", async (e) => {
    if (e.request.url.includes(FOLDER_PREFIX)) {
        const id = e.request.url.split(FOLDER_PREFIX)[1].split("/").join("");
        const zip = resolvers[id];

        // find zip ID
        if (zip) {
            const uri = e.request.url
                .split(FOLDER_PREFIX)[2]
                .substr(1)
                .split("?")[0];
            const ext = uri.split(".").pop();
            const mime = Mimes[ext];
            if (zip.files[uri]) {
                e.respondWith(
                    (async () => {
                        const responseBody = await zip.file(uri).async("blob");
                        return new Response(responseBody, {
                            headers: { "Content-Type": mime },
                        });
                    })()
                );
            } else {
                console.log("zip url not exists", uri);
            }
        }
    }
});
