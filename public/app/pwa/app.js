import { saveBook, getBooks, deleteBook } from './db.js';
import { loadMorePackage } from './core/loader.js';
import { renderChapter } from './core/renderer.js';
import { setupInterceptor } from './core/interceptor.js';
import { saveCSV } from './core/export.js';

// 🔥 PROGRESS ENGINE

const getProgressKey = (bookId) => `more_progress_${bookId}`;

function getDefaultProgress() {
  return { chapters: {} };
}

function loadProgress(bookId) {
  const raw = localStorage.getItem(getProgressKey(bookId));
  if (!raw) return getDefaultProgress();

  try {
    return JSON.parse(raw);
  } catch {
    return getDefaultProgress();
  }
}

function saveProgress(bookId, data) {
  localStorage.setItem(getProgressKey(bookId), JSON.stringify(data));
}

function ensureChapter(progress, index) {
  if (!progress.chapters[index]) {
    progress.chapters[index] = { completed: false, time: 0 };
  }
}

// 🔥 session tracking
let currentChapterIndex = null;
let chapterStartTime = null;

function onChapterOpen(bookId, index) {
  const progress = loadProgress(bookId);
  ensureChapter(progress, index);

  currentChapterIndex = index;
  chapterStartTime = Date.now();

  saveProgress(bookId, progress);
}

function onChapterExit(bookId) {
  if (currentChapterIndex === null || !chapterStartTime) return;

  const progress = loadProgress(bookId);
  ensureChapter(progress, currentChapterIndex);

  const elapsed = Math.floor((Date.now() - chapterStartTime) / 1000);
  progress.chapters[currentChapterIndex].time += elapsed;

  saveProgress(bookId, progress);

  currentChapterIndex = null;
  chapterStartTime = null;
}

function markChapterComplete(bookId, index) {
  const progress = loadProgress(bookId);
  ensureChapter(progress, index);

  // prevent unnecessary refresh spam
  if (progress.chapters[index].completed) return;

  progress.chapters[index].completed = true;
  saveProgress(bookId, progress);

  console.log("COMPLETED:", index);
  
  // 🔥 SINGLE SOURCE OF UI REFRESH
  if (window.RUNTIME) {
    buildMenu();
  }
}

window.markChapterComplete = markChapterComplete;

const fileInput = document.getElementById('fileInput');
const SUBJECT_COLORS = {
  "english": "#00b0e7",
  "filipino": "#013563",
  "science": "#f06720",
  "math": "#02a450",
  "ap": "#fc0102",
  "gmrc": "#774c23",
  "ma": "#fff000",
  "peh": "#faf59d",
  "tle": "#625ca4",
  "makabansa": "#fc0102",
  "language": "#00b0e7",
  "literacy": "#00b0e7",
  "shs": "#fc0102"
};

fileInput.addEventListener('change', async (e) => {
  const file = e.target.files[0];

  const runtime = await loadMorePackage(file);

  window.RUNTIME = runtime;
  
  // 🔥 SAVE TO INDEXEDDB
const manifest = runtime.manifest;
const meta = manifest.metadata;

const book = {
  id: meta.packageId, // still unique storage id
  title: meta.title,
  subject: meta.subject,
  authorId: meta.authorId,
  version: Number(meta.version) || 1,
  file: file
};

const existingBooks = await getBooks();

const match = existingBooks.find(b =>
  b.title === book.title &&
  b.subject === book.subject &&
  b.authorId === book.authorId
);

if (match) {
  if (book.version > match.version) {
    const ok = confirm(
      `A newer version of "${book.title}" is available.\nUpdate from v${match.version} to v${book.version}?`
    );

    if (!ok) return;

    // 🔥 preserve progress BEFORE delete
    migrateProgress(match.id, book.id);

    await deleteBook(match.id);
    await saveBook(book);
    // 🔥 reload runtime to ensure fresh state
const newRuntime = await loadMorePackage(file);
window.RUNTIME = newRuntime;
  } else {
    alert("This version is not newer than the existing one.");
    return;
  }
} else {
  await saveBook(book);
}

// 🔥 REFRESH BOOK LIST
const books = await getBooks();
renderBookList(books);
setupContinueReading(books);

  // 🔥 Update title
  const titleEl = document.getElementById("title");
  titleEl.innerText = runtime.manifest.metadata.title || "MORE Container";
  
  const subject = (runtime.manifest.metadata.subject || "").toLowerCase();
  const color = SUBJECT_COLORS[subject] || "#222"; // fallback

  const topbar = document.getElementById("topbar");
  const textColor = getTextColor(color);
  topbar.style.color = textColor;
  topbar.style.background = color;
  
  // 🔥 SWITCH UI
  document.getElementById("welcome").style.display = "none";
  document.getElementById("app").style.display = "flex";
  
  const firstChapter = runtime.manifest.chapters[0];

  const bookId = runtime.manifest.metadata.packageId;
  console.log("MENU BOOK ID:", bookId);
console.log("AVAILABLE KEYS:", Object.keys(localStorage));

onChapterOpen(bookId, 0);
await renderChapter(firstChapter.path);
runtime.currentIndex = 0;
saveLastState(runtime, runtime.currentIndex);

  buildMenu();
  
  setupInterceptor(runtime);
});

window.toggleMenu = function () {
  const menu = document.getElementById("menu");
  const overlay = document.getElementById("overlay");

  menu.classList.toggle("open");
  overlay.classList.toggle("show");
};

window.closeQuiz = function () {
  const modal = document.getElementById("quizModal");

  modal.classList.remove("show");

  setTimeout(() => {
    modal.style.display = "none";
  }, 200);
};

function buildMenu() {
  const runtime = window.RUNTIME;
  
    if (!runtime || !runtime.manifest) {
    console.warn("Runtime not ready yet");
    return;
  }
  
  const bookId = runtime.manifest.metadata.packageId;
const progress = loadProgress(bookId);
const chapters = runtime.manifest.chapters;

const total = chapters.length;

let completedCount = 0;

chapters.forEach((_, i) => {
  // 🔥 ensure structure exists
  if (!progress.chapters[i]) {
    progress.chapters[i] = { completed: false, time: 0 };
  }

  if (progress.chapters[i].completed) {
    completedCount++;
  }
});

saveProgress(bookId, progress);

const percent = total > 0
  ? Math.round((completedCount / total) * 100)
  : 0;

  const menu = document.getElementById("menu");
  
  const homeItem = document.createElement("div");
  homeItem.innerText = "🏠 Home";
  homeItem.className = "menu-item";
  homeItem.style.cursor = "pointer";

  homeItem.onclick = () => {
    goHome();
    toggleMenu();
  };
  
  const chapItem = document.createElement("div");
  chapItem.innerText = `📖 Chapters (${percent}%)`;
  chapItem.className = "menu-item";
  chapItem.style.cursor = "pointer";

  const aboutItem = document.createElement("div");
  aboutItem.innerText = "ℹ️ About";
  aboutItem.className = "menu-item";
  aboutItem.style.cursor = "pointer";

  aboutItem.onclick = () => {
    showAbout();
    toggleMenu();
  };

  const scoreItem = document.createElement("div");
  scoreItem.innerText = "📊 Scores";
  scoreItem.className = "menu-item";
  scoreItem.style.cursor = "pointer";

  scoreItem.onclick = () => {
    showScores();
    toggleMenu();
  };

  menu.innerHTML = "<img src = \"more-logo.png\" style=\"width:100%;\" />";

  menu.appendChild(homeItem);

const contentSection = document.createElement("div");
contentSection.innerText = "CONTENT";
contentSection.style.fontSize = "12px";
contentSection.style.opacity = "0.6";
contentSection.style.margin = "10px 0 5px";
menu.appendChild(contentSection);

  menu.appendChild(chapItem);
  
runtime.manifest.chapters.forEach((ch, index) => {
  const item = document.createElement("div");
  item.style.padding = "12px 12px 12px 28px";
  item.style.cursor = "pointer";

  const isDone = progress.chapters[index]?.completed;
  const isCurrent = runtime.currentIndex === index;

  // ✔ or •
  const status = isDone ? "✔ " : "• ";

  item.innerText = status + ch.title;

  // highlight current
  if (isCurrent) {
    item.style.fontWeight = "bold";
  }

  item.onclick = () => {
    const bookId = runtime.manifest.metadata.packageId;

    onChapterExit(bookId);

    runtime.currentIndex = index;

    onChapterOpen(bookId, index);

    renderChapter(ch.path);
    saveLastState(runtime, index);
    toggleMenu();
  };

  menu.appendChild(item);
});

  
  // optional divider
  const divider = document.createElement("hr");
  menu.appendChild(divider);

  // 🔥 add scores AFTER clearing
const generalSection = document.createElement("div");
generalSection.innerText = "GENERAL";
generalSection.style.fontSize = "12px";
generalSection.style.opacity = "0.6";
generalSection.style.margin = "10px 0 5px";
menu.appendChild(generalSection);

  menu.appendChild(scoreItem);
  menu.appendChild(aboutItem);
  const exportItem = document.createElement("div");
exportItem.innerText = "⬇️ Export Data";
exportItem.className = "menu-item";
exportItem.style.cursor = "pointer";

exportItem.onclick = () => {
  exportCSV();
  toggleMenu();
};

menu.appendChild(exportItem);

}

window.showScores = function () {
  const runtime = window.RUNTIME;

  const packageId =
    runtime.manifest.metadata.packageId || "default";

  const key = "more_scores_" + packageId;

  const records = JSON.parse(localStorage.getItem(key)) || [];

  const modal = document.getElementById("quizModal");
  const container = document.getElementById("quizContent");

  modal.style.display = "block";
  setTimeout(() => modal.classList.add("show"), 10);
  container.innerHTML = "<h2>Scores</h2>";

  if (records.length === 0) {
    container.innerHTML += "<p>No attempts yet.</p>";
    return;
  }

  records.forEach((rec, index) => {
    const div = document.createElement("div");
    div.className = "score-card";
    div.style.padding = "10px";
    div.style.marginBottom = "10px";
    div.style.border = "1px solid #ccc";
    div.style.cursor = "pointer";

    const date = new Date(rec.timestamp).toLocaleString();

    div.innerText = `
Quiz: ${rec.quizId}
Score: ${rec.score.correct}/${rec.score.total}
Date: ${date}
`;

    div.onclick = () => {
      showResults(rec.results, rec.score);
    };

    container.appendChild(div);
  });
};

window.showAbout = function () {
  const runtime = window.RUNTIME;
  const manifest = runtime.manifest;

  const modal = document.getElementById("quizModal");
  const container = document.getElementById("quizContent");

  modal.style.display = "block";
  setTimeout(() => modal.classList.add("show"), 10);
  container.innerHTML = "<h2>About</h2>";

  const meta = manifest.metadata;

// 🔥 SYSTEM INFO
container.innerHTML = `
  <img src = "more-logo.png" style="width:100%;" />
  <h2>About MORE</h2>

  <p><strong>MORE (Mobile Offline Resources for Education) Standard</strong></p>
  <hr>

  <p>
    A lightweight, offline-first learning system for structured reading,
    assessment, and progress tracking.
  </p>

  <p>
    Created by Mahnnie Q. Tolentino<br>
    Developed by the Department of Education - Division of Aklan<br>
    Training supported by Benito and Catalino Yap Foundation
  </p>

  <hr>
  <h3>Book Information</h3>
  <strong>Title:</strong> ${meta.title}<br>
  <strong>Version:</strong> ${meta.version}<br>
  <strong>Created:</strong> ${meta.created}
`;

  // 👥 Contributors
  container.innerHTML += "<h3>Contributors</h3>";

  manifest.contributors.forEach(person => {
    container.innerHTML += `
      ${person.role}: ${person.name}<br>
    `;
  });
  
  const bookId = runtime.manifest.metadata.packageId;

const progress =
  JSON.parse(localStorage.getItem("more_progress_" + bookId)) || {};

const scores =
  JSON.parse(localStorage.getItem("more_scores_" + bookId)) || [];

const totalChapters = runtime.manifest.chapters.length;

let completed = 0;
let totalTime = 0;

Object.values(progress.chapters || {}).forEach(c => {
  if (c.completed) completed++;
  totalTime += c.time || 0;
});

container.innerHTML += `
  <h3>Learning Stats</h3>
  <strong>Chapters Completed:</strong> ${completed} / ${totalChapters}<br>
  <strong>Total Reading Time:</strong> ${totalTime} sec<br>
  <strong>Quizzes Taken:</strong> ${scores.length}
`;

container.innerHTML += `
  <hr>
  <p style="opacity:0.7; font-size:12px;">
    Licensed for educational use.<br>
    For training and deployment inquiries, contact: mahnnie.tolentino@deped.gov.ph
  </p>
`;
};

window.goHome = function () {
if (window.RUNTIME) {
  const runtime = window.RUNTIME;
  const bookId = runtime.manifest.metadata.packageId;

  // 🔥 save reading time
  onChapterExit(bookId);

  // 🔥 save last position
  saveLastState(runtime, runtime.currentIndex);
}

  const app = document.getElementById("app");
  const welcome = document.getElementById("welcome");

  // 🔥 fade out app
  app.classList.add("fade-out");

  setTimeout(() => {
    // hide app
    app.style.display = "none";
    app.classList.remove("fade-out");

    // show welcome
    welcome.style.display = "flex";

    // reset runtime
    window.RUNTIME = null;
    document.getElementById("viewer").src = "";
    document.getElementById("fileInput").value = "";
    document.getElementById("title").innerText = "MORE Container";
    document.getElementById("topbar").style.background = "#222";

  }, 200);
};

function getTextColor(bgColor) {
  const c = bgColor.substring(1); // remove #
  const rgb = parseInt(c, 16);

  const r = (rgb >> 16) & 0xff;
  const g = (rgb >> 8) & 0xff;
  const b = (rgb >> 0) & 0xff;

  const brightness = (r * 299 + g * 587 + b * 114) / 1000;

  return brightness > 150 ? "#000" : "#fff";
}

window.toggleTheme = function () {
  const iframe = document.getElementById("viewer");
  const doc = iframe.contentDocument;

  const currentBg = doc.documentElement.style.getPropertyValue('--bg');

  if (currentBg === "#000000") {
    doc.documentElement.style.setProperty('--bg', '#ffffff');
    doc.documentElement.style.setProperty('--text', '#000000');
  } else {
    doc.documentElement.style.setProperty('--bg', '#000000');
    doc.documentElement.style.setProperty('--text', '#ffffff');
  }
};

let currentFontSize = 16;

window.increaseFont = function () {
  currentFontSize += 2;

  const iframe = document.getElementById("viewer");
  iframe.contentDocument.documentElement
    .style.setProperty('--font-size', currentFontSize + 'px');
};

window.decreaseFont = function () {
  currentFontSize = Math.max(12, currentFontSize - 2);

  const iframe = document.getElementById("viewer");
  iframe.contentDocument.documentElement
    .style.setProperty('--font-size', currentFontSize + 'px');
};

window.addEventListener("DOMContentLoaded", async () => {
  const books = await getBooks();
  renderBookList(books);
  setupContinueReading(books);
  
  setTimeout(() => {
  const splash = document.getElementById("splash");

  if (splash) {
    splash.style.opacity = "0";
    splash.style.transition = "opacity 0.3s ease";

    setTimeout(() => splash.remove(), 300);
  }
	}, 2000);

});

function renderBookList(books) {
  books.sort((a, b) => a.title.localeCompare(b.title));
  const container = document.getElementById("bookList");

  if (!container) return;

  container.innerHTML = "";

  if (books.length === 0) {
    container.innerHTML = `
  <p style="opacity:0.6;">
    No books yet.<br>
    Upload a .more package to get started.
  </p>
`;
    return;
  }

  books.forEach(book => {
    const item = document.createElement("div");
item.className = "book-item";

// wrapper for layout
const row = document.createElement("div");
row.style.display = "flex";
row.style.justifyContent = "space-between";
row.style.alignItems = "center";

// title
const title = document.createElement("span");
title.innerText = book.title;

// delete button
const del = document.createElement("span");
del.innerText = "✕";
del.style.cursor = "pointer";
del.style.padding = "4px 8px";

// 🔥 DELETE LOGIC
del.onclick = async (e) => {
  e.stopPropagation();

  // 🔥 confirm first
  const ok = confirm("Delete this book?");
  if (!ok) return;

  await deleteBook(book.id);

  const updated = await getBooks();
  renderBookList(updated);
  setupContinueReading(updated); // keep continue button in sync
};

// assemble
row.appendChild(title);
row.appendChild(del);
item.appendChild(row);

// open book
item.onclick = () => {
  openBookFromStorage(book);
};

container.appendChild(item);
  });
}

async function openBookFromStorage(book) {
  const file = book.file;

  const runtime = await loadMorePackage(file);

  window.RUNTIME = runtime;

  // reuse your EXACT existing flow 👇
  const titleEl = document.getElementById("title");
  titleEl.innerText = runtime.manifest.metadata.title || "MORE Container";

  const subject = (runtime.manifest.metadata.subject || "").toLowerCase();
  const color = SUBJECT_COLORS[subject] || "#222";

  const topbar = document.getElementById("topbar");
  const textColor = getTextColor(color);
  topbar.style.color = textColor;
  topbar.style.background = color;

  document.getElementById("welcome").style.display = "none";
  document.getElementById("app").style.display = "flex";

  const saved = JSON.parse(localStorage.getItem("more_last"));

let index = 0;

if (saved && saved.packageId === runtime.manifest.metadata.packageId) {
  index = saved.chapterIndex || 0;
}

runtime.currentIndex = index;
saveLastState(runtime, index);

const bookId = runtime.manifest.metadata.packageId;

onChapterOpen(bookId, index);

await renderChapter(runtime.manifest.chapters[index].path);


  buildMenu();
  setupInterceptor(runtime);
  const books = await getBooks();
setupContinueReading(books);
}

function saveLastState(runtime, chapterIndex) {
  const packageId = runtime.manifest.metadata.packageId || "default";

  localStorage.setItem("more_last", JSON.stringify({
  packageId,
  chapterIndex,
  title: runtime.manifest.metadata.title
}));
}

function setupContinueReading(books) {
  const btn = document.getElementById("continueReading");

  const saved = JSON.parse(localStorage.getItem("more_last"));

  if (!saved) {
    btn.style.display = "none";
    return;
  }

  const book = books.find(b => b.id === saved.packageId);

  if (!book) {
    btn.style.display = "none";
    return;
  }

  // ✅ ALWAYS SHOW when valid
  btn.style.display = "inline";

  btn.onclick = () => {
    openBookFromStorage(book);
  };
}

function migrateProgress(oldId, newId) {
  // resume
  const last = JSON.parse(localStorage.getItem("more_last"));
  if (last && last.packageId === oldId) {
    last.packageId = newId;
    localStorage.setItem("more_last", JSON.stringify(last));
  }

  // scores
  const oldKey = "more_scores_" + oldId;
  const newKey = "more_scores_" + newId;

  const scores = localStorage.getItem(oldKey);
  if (scores) {
    localStorage.setItem(newKey, scores);
    localStorage.removeItem(oldKey);
  }
  
  // 🔥 progress
const oldProgressKey = "more_progress_" + oldId;
const newProgressKey = "more_progress_" + newId;

const progress = localStorage.getItem(oldProgressKey);

if (progress) {
  localStorage.setItem(newProgressKey, progress);
  localStorage.removeItem(oldProgressKey);
  }
}

window.addEventListener("beforeunload", () => {
  if (window.RUNTIME) {
    const bookId = window.RUNTIME.manifest.metadata.packageId;
    onChapterExit(bookId);
  }
});

window.exportCSV = function () {
  const runtime = window.RUNTIME;

  if (!runtime) {
    alert("No book loaded.");
    return;
  }

  const bookId = runtime.manifest.metadata.packageId;
  const title = runtime.manifest.metadata.title;

  const progress =
    JSON.parse(localStorage.getItem("more_progress_" + bookId)) || {};

  const scores =
    JSON.parse(localStorage.getItem("more_scores_" + bookId)) || [];

  const chapters = runtime.manifest.chapters;

  let csv = "Book,Chapter,Completed,Reading Time (sec),Quiz,Score,Total,Quiz Time (sec),Date\n";

  chapters.forEach((ch, index) => {
    const p = progress.chapters?.[index] || {};

    // find quiz related to this chapter
    const relatedScores = scores.filter(s =>
      s.quizId.includes(`_${index + 1}`)
    );

    if (relatedScores.length === 0) {
      csv += `${title},"${ch.title}",${p.completed || false},${p.time || 0},,,,\n`;
    } else {
      relatedScores.forEach(s => {
        const date = new Date(s.timestamp).toLocaleString();

        csv += `${title},"${ch.title}",${p.completed || false},${p.time || 0},${s.quizId},${s.score.correct},${s.score.total},${s.duration || 0},"${date}"\n`;
      });
    }
  });

  const filename = `${title.replace(/\s+/g, "_")}_report_${Date.now()}.csv`;
saveCSV(filename, csv);
};
