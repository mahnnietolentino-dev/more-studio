const style = `
<style>
  :root {
    --bg: #ffffff;
    --text: #000000;
    --font-size: 17px;
  }

  body {
    font-family: sans-serif;
    padding: 16px;
    line-height: 1.7;
    max-width: 700px;
    margin: auto;

    background: var(--bg);
    color: var(--text);
    font-size: var(--font-size);

    transition: background 0.2s ease, color 0.2s ease;
  }

  h1, h2, h3 {
    margin-top: 1.2em;
  }

  img {
    max-width: 100%;
    border-radius: 8px;
  }

  .quiz-block {
    margin: 20px 0;
    padding: 12px;
    text-align: center;
  }

  a {
    display: inline-block;
    padding: 10px 14px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
  }
</style>
`;

export async function renderChapter(path) {
  const runtime = window.RUNTIME;
  const viewer = document.getElementById("viewer");

  // 🔥 fade out
  viewer.classList.add("fade-out");

  await new Promise(r => setTimeout(r, 150));

  let html = runtime.files[path];

// inject style into <head>
if (html.includes("<head>")) {
  html = html.replace("<head>", `<head>${style}`);
} else {
  html = `<head>${style}</head>${html}`;
}

  html = html.replace(/src="\.\.\/assets\/(.*?)"/g, (match, p1) => {
    const assetPath = "assets/" + p1;
    const fileData = runtime.files[assetPath];

    if (!fileData) return match;

    const blob = new Blob([fileData], { type: "image/jpeg" });
    return `src="${URL.createObjectURL(blob)}"`;
  });

  const blob = new Blob([html], { type: "text/html" });
  viewer.src = URL.createObjectURL(blob);
  
viewer.addEventListener("load", () => {
  const doc = viewer.contentDocument;
  if (!doc) return;

  const runtime = window.RUNTIME;
  if (!runtime) return;

  const bookId = runtime.manifest.metadata.packageId;
  const index = runtime.currentIndex;

  // 🔥 IMAGE ZOOM
  const images = doc.querySelectorAll("img");

  images.forEach(img => {
    img.style.cursor = "zoom-in";

    img.onclick = () => {
      const modal = document.createElement("div");

      modal.style.position = "fixed";
      modal.style.top = 0;
      modal.style.left = 0;
      modal.style.width = "100vw";
      modal.style.height = "100vh";
      modal.style.background = "rgba(0,0,0,0.9)";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = 9999;

      const zoomed = document.createElement("img");
      zoomed.src = img.src;
      zoomed.style.maxWidth = "95%";
      zoomed.style.maxHeight = "95%";

      modal.appendChild(zoomed);

      modal.onclick = () => modal.remove();

      document.body.appendChild(modal);
    };
  });

  // 🔥 SCROLL TRACKING
  const el = doc.documentElement;

  function onScroll() {
    const nearBottom =
      el.scrollTop + el.clientHeight >= el.scrollHeight - 10;

    if (nearBottom) {
      window.markChapterComplete(bookId, index);
      el.removeEventListener("scroll", onScroll);
    }
  }

  el.addEventListener("scroll", onScroll);
});
  // 🔥 fade back in
  setTimeout(() => {
    viewer.classList.remove("fade-out");
  }, 100);
}
