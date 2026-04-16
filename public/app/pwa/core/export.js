export async function saveCSV(filename, content) {
  try {
    const Plugins = window.Capacitor?.Plugins;

    if (!Plugins) return;

    const Filesystem = Plugins.Filesystem;
    const FileOpener = Plugins.FileOpener;

    if (!Filesystem) return;

    const result = await Filesystem.writeFile({
      path: filename,
      data: content,
      directory: "DOCUMENTS",
      encoding: "utf8"
    });

    // 🔥 open immediately (no dialogs)
    if (FileOpener) {
      await FileOpener.open({
        filePath: result.uri,
        contentType: 'text/csv'
      });
    }

  } catch (err) {
    console.error("EXPORT FAILED:", err);
  }
}
