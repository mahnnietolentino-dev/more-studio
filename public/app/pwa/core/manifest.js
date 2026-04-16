export function parseManifest(xmlText) {
  const parser = new DOMParser();
  const xml = parser.parseFromString(xmlText, "text/xml");

  // =====================
  // 📦 METADATA
  // =====================
  const metadataNode = xml.querySelector("metadata");

  const metadata = {
    title: metadataNode?.querySelector("title")?.textContent || "",
    subject: metadataNode?.querySelector("subject")?.textContent || "",
    format: metadataNode?.querySelector("format")?.textContent || "",
    formatVersion: metadataNode?.querySelector("formatVersion")?.textContent || "",
    formatCreator: metadataNode?.querySelector("formatCreator")?.textContent || "",
    generator: metadataNode?.querySelector("generator")?.textContent || "",
    created: metadataNode?.querySelector("created")?.textContent || "",
    authorId: metadataNode?.querySelector("authorId")?.textContent || "",
    packageId: metadataNode?.querySelector("packageId")?.textContent || "default",
    version: metadataNode?.querySelector("version")?.textContent || ""
  };

  // =====================
  // 👥 CONTRIBUTORS
  // =====================
  const contributors = Array.from(xml.querySelectorAll("contributors person")).map(p => ({
    role: p.getAttribute("role") || "",
    id: p.querySelector("id")?.textContent || "",
    name: p.querySelector("name")?.textContent || ""
  }));

  // =====================
  // 📚 CHAPTERS
  // =====================
  const chapters = Array.from(xml.querySelectorAll("chapter")).map(ch => ({
    path: ch.getAttribute("file"),
    title: ch.getAttribute("title")
  }));

  // =====================
  // 📝 QUIZZES (optional for now)
  // =====================
  const quizzes = Array.from(xml.querySelectorAll("quiz")).map(q => ({
    path: q.getAttribute("file")
  }));

  console.log("Parsed Metadata:", metadata);
  console.log("Parsed Contributors:", contributors);
  console.log("Parsed Chapters:", chapters);
  console.log("Parsed Quizzes:", quizzes);

  return {
    metadata,
    contributors,
    chapters,
    quizzes
  };
}
