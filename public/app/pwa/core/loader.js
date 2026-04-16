import { parseManifest } from './manifest.js';

export async function loadMorePackage(file) {
  const zip = await JSZip.loadAsync(file);

  const runtime = {
    files: {},
    manifest: null
  };

  const entries = Object.keys(zip.files);

for (let path of entries) {
  const fileObj = zip.files[path];

  if (!fileObj.dir) {

    if (path.startsWith("assets/")) {
      runtime.files[path] = await fileObj.async("uint8array"); // 🔥 binary
    } else {
      runtime.files[path] = await fileObj.async("string");
    }

  }
}

  runtime.manifest = parseManifest(runtime.files["manifest.xml"]);

  return runtime;
}
