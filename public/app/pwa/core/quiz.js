export function openQuiz(path, runtime) {

const packageId =
  runtime.manifest.metadata.packageId || "default";

const key = "more_scores_" + packageId;

const existing = JSON.parse(localStorage.getItem(key)) || [];

const alreadyTaken = existing.find(r => r.quizId === path);

if (alreadyTaken) {
  alert("You have already taken this quiz.");
  showResults(alreadyTaken.results, alreadyTaken.score);
  return;
}

  const raw = runtime.files[path];
  const data = JSON.parse(raw);
  let quizTitle =
  data.title ||
  runtime.manifest?.quizzes?.[path]?.title ||
  path;

// 🔥 clean path fallback
quizTitle = quizTitle
  .replace("quizzes/", "")
  .replace(".json", "")
  .toUpperCase();

// 🔥 CREATE SESSION
const session = {
  path: path,
  answers: [],
  questions: data.questions,
  startTime: Date.now() // 🔥 start timer
};

const safeIndex =
  typeof runtime.currentIndex === "number"
    ? runtime.currentIndex
    : 0;

window.QUIZ_CONTEXT = {
  chapterIndex: safeIndex
};

console.log("QUIZ CONTEXT INDEX:", safeIndex);

// store globally (temporary for now)
window.QUIZ_SESSION = session;

  const modal = document.getElementById("quizModal");
  const container = document.getElementById("quizContent");

  modal.classList.remove("show");
  
  modal.style.display = "block";
  setTimeout(() => modal.classList.add("show"), 10);

  container.innerHTML = "";
  const header = document.createElement("div");
header.style.marginBottom = "20px";

const titleEl = document.createElement("h2");
titleEl.innerText = quizTitle;

const instructions = document.createElement("p");
instructions.innerHTML = `
  Total: ${data.questions.length} questions<br>
  Answer all questions. You only have one attempt.
  Your score will be saved and can be viewed in the menu.
`;

instructions.style.opacity = "0.8";

header.appendChild(titleEl);
header.appendChild(instructions);

container.appendChild(header);

header.style.padding = "12px";
header.style.borderRadius = "10px";
header.style.background = "#f5f7fb";

const hr = document.createElement("hr");
hr.style.margin = "16px 0";
container.appendChild(hr);

  data.questions.forEach((q, index) => {
    const qDiv = document.createElement("div");
    qDiv.style.marginBottom = "20px";

    const title = document.createElement("p");
    title.innerText = (index + 1) + ". " + q.question;

    qDiv.appendChild(title);

    if (q.type === "MCQ" && Array.isArray(q.choices)) {
	  q.choices.forEach(choice => {
	    const btn = document.createElement("button");
	    btn.innerText = choice.text;
	    btn.className = "quiz-option";

	    btn.onclick = () => {
  session.answers[index] = {
    type: "MCQ",
    selected: choice.text,
    correct: choice.correct
  };

  // visual feedback (optional)
  // remove selection from siblings
  const buttons = qDiv.querySelectorAll("button");
  buttons.forEach(b => b.classList.remove("selected"));

  // mark selected
  btn.classList.add("selected");
btn.classList.add("pop");

// remove pop after animation
setTimeout(() => btn.classList.remove("pop"), 200);

};

	    qDiv.appendChild(btn);
	  });
	}
	
	if (q.type === "TF") {
	  ["True", "False"].forEach(val => {
	    const btn = document.createElement("button");
	    btn.innerText = val;
		btn.className = "quiz-option";

	    btn.onclick = () => {
  const userAnswer = (val === "True");

  session.answers[index] = {
    type: "TF",
    selected: userAnswer,
    correct: (userAnswer === q.answer)
  };

  // remove selection from siblings
  const buttons = qDiv.querySelectorAll("button");
  buttons.forEach(b => b.classList.remove("selected"));

  // mark selected
  btn.classList.add("selected");
btn.classList.add("pop");

// remove pop after animation
setTimeout(() => btn.classList.remove("pop"), 200);
};

	    qDiv.appendChild(btn);
	  });
	}

	if (q.type === "SHORT") {
	  const input = document.createElement("input");
	  input.type = "text";
	  input.style.display = "block";
	  input.style.marginBottom = "10px";

	  const btn = document.createElement("button");
	  btn.innerText = "Submit";
	  btn.className = "quiz-option";

	  btn.onclick = () => {
  const userAnswer = input.value.trim();

  session.answers[index] = {
    type: "SHORT",
    input: userAnswer,
    correct: null // subjective for now
  };

  // remove selection from siblings
  const buttons = qDiv.querySelectorAll("button");
  buttons.forEach(b => b.classList.remove("selected"));

  // mark selected
  btn.classList.add("selected");
btn.classList.add("pop");

// remove pop after animation
setTimeout(() => btn.classList.remove("pop"), 200);
  alert("Answer saved");
};

	  qDiv.appendChild(input);
	  qDiv.appendChild(btn);
	}

    container.appendChild(qDiv);
  });
  
  const submitBtn = document.createElement("button");
submitBtn.innerText = "Submit Quiz";
submitBtn.style.marginTop = "20px";

submitBtn.onclick = () => {
  submitQuiz(session);
};

container.appendChild(submitBtn);
}

export function submitQuiz(session) {
  let correct = 0;
  let total = session.questions.length;

  const results = [];

  session.questions.forEach((q, index) => {
    const user = session.answers[index];

    let isCorrect = false;
    let correctAnswer = "";

    if (q.type === "MCQ") {
      const correctChoice = q.choices.find(c => c.correct);
      correctAnswer = correctChoice ? correctChoice.text : "";

      isCorrect = user && user.correct === true;
    }

    if (q.type === "TF") {
      correctAnswer = q.answer ? "True" : "False";
      isCorrect = user && user.correct === true;
    }

    if (q.type === "SHORT") {
  const userInput = (user && user.input) ? user.input : "";

  const normalize = (str) =>
    str.toLowerCase().trim().replace(/\s+/g, " ");

  const userNorm = normalize(userInput);
  const correctNorm = normalize(q.answer || "");

  isCorrect =
    userNorm === correctNorm ||
    userNorm.includes(correctNorm);

  correctAnswer = q.answer;
}

    if (isCorrect === true) correct++;

    results.push({
      question: q.question,
      userAnswer: user ? (user.selected ?? user.input) : "No answer",
      correctAnswer: correctAnswer,
      isCorrect: isCorrect
    });
  });

  const score = {
    correct,
    total
  };

  console.log("RESULTS:", results);
  console.log("SCORE:", score);

  showResults(results, score, session);
}

function showResults(results, score, session = null) {
  const container = document.getElementById("quizContent");

  container.innerHTML = "";

  const title = document.createElement("h2");
  title.innerText = `Score: ${score.correct} / ${score.total}`;
  container.appendChild(title);

  results.forEach((r, index) => {
    const div = document.createElement("div");
    div.style.marginBottom = "20px";
    div.style.padding = "12px";
	div.style.borderRadius = "10px";

    const q = document.createElement("p");
    q.innerText = `${index + 1}. ${r.question}`;

    const user = document.createElement("p");
    user.innerText = `Your answer: ${r.userAnswer}`;

    const correct = document.createElement("p");
    correct.innerText = `Correct answer: ${r.correctAnswer}`;

    if (r.isCorrect === true) {
      div.style.background = "#d4edda"; // green
    } else if (r.isCorrect === false) {
      div.style.background = "#f8d7da"; // red
    } else {
      div.style.background = "#fff3cd"; // yellow (SHORT)
    }

    div.appendChild(q);
    div.appendChild(user);
    div.appendChild(correct);

    container.appendChild(div);
  });
  
  if (session) {
  saveScore(session, score, results);
}
}

function saveScore(session, score, results) {
  const duration = Math.floor((Date.now() - session.startTime) / 1000);
  const runtime = window.RUNTIME;

  const packageId =
    runtime.manifest.metadata.packageId || "default";

  const key = "more_scores_" + packageId;

  const existing = JSON.parse(localStorage.getItem(key)) || [];

  const record = {
  quizId: session.path,
  score: score,
  results: results,
  timestamp: Date.now(),
  duration: duration // 🔥 quiz time
};

  existing.push(record);

  localStorage.setItem(key, JSON.stringify(existing));

  console.log("Saved score:", record);
  
  // 🔥 mark chapter as completed via quiz
//const runtime = window.RUNTIME;
const bookId = runtime.manifest.metadata.packageId;

if (window.markChapterComplete) {
  let index = window.QUIZ_CONTEXT?.chapterIndex;

if (typeof index !== "number") {
  index = runtime.currentIndex;
}

if (typeof index !== "number") {
  console.warn("Invalid chapter index, skipping completion");
  return;
}

window.markChapterComplete(bookId, index);
}
}
window.showResults = showResults;
