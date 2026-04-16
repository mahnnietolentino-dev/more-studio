import { openQuiz } from './quiz.js';

export function setupInterceptor(runtime) {
  const iframe = document.getElementById("viewer");

iframe.addEventListener("load", () => {
  const doc = iframe.contentDocument;

  const links = doc.querySelectorAll("a");

  links.forEach(link => {
    link.onclick = (e) => {
      const href = link.getAttribute("href");

      if (href && href.includes("../quizzes/")) {
        e.preventDefault();

        const cleanPath = href.replace("../", "");

        openQuiz(cleanPath, runtime);
      }
    };
  });
});
}
