@php

// self.importScripts("modules/jszip.js");
// self.importScripts("modules/mimetypes.js");
// self.importScripts("modules/txml.js");

echo file_get_contents (base_path().'/vendor/escolalms/scorm/resources/js/modules/jszip.js');
echo file_get_contents (base_path().'/vendor/escolalms/scorm/resources/js/modules/mimetypes.js');
echo file_get_contents (base_path().'/vendor/escolalms/scorm/resources/js/modules/txml.js');
echo file_get_contents (base_path().'/vendor/escolalms/scorm/resources/js/serviceworker.js');
@endphp