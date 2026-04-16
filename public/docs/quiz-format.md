# MoRe Studio v0.1 — Quiz Format Specification

## 1. Overview

MoRe Studio supports embedded quizzes inside DOCX files using simple text markers.

These markers allow teachers to define assessments directly within their content without needing technical tools.

---

## 2. General Structure

All quizzes must be enclosed within a `[QUIZ]` block.

```
[QUIZ]
... quiz content ...
```

Each quiz must specify a type:

* `[MCQ]` — Multiple Choice
* `[SHORT]` — Short Answer
* `[TF]` — True/False

---

## 3. Multiple Choice (MCQ)

### Format

```
[QUIZ]
[MCQ]
What is 2 + 2?
A. 3
B. 4 *
C. 5
```

---

### Rules

* Each choice starts with a letter (`A.`, `B.`, etc.)
* The correct answer is marked with `*`
* Only ONE correct answer is allowed
* Question must appear before choices

---

### Example

```
[QUIZ]
[MCQ]
Which planet is known as the Red Planet?
A. Earth
B. Mars *
C. Venus
D. Jupiter
```

---

## 4. True/False (TF)

### Format

```
[QUIZ]
[TF]
The Earth is flat.
Answer: False
```

---

### Rules

* Must include `Answer: True` or `Answer: False`
* Case-insensitive

---

## 5. Short Answer (SHORT)

### Format

```
[QUIZ]
[SHORT]
What is the capital of France?
Answer: Paris
```

---

### Rules

* Must include `Answer:`
* Accepts free text
* Future versions may support multiple answers

---

## 6. Placement Rules

* Quizzes can appear anywhere in the document
* They are assigned to the **current chapter**
* Chapter is determined by **Heading 1**

---

## 7. Parsing Rules (System Behavior)

### 7.1 Block Detection

* System detects `[QUIZ]` markers
* Splits content into quiz blocks

---

### 7.2 Type Detection

* First tag after `[QUIZ]` determines type
* Only one type per block

---

### 7.3 MCQ Parsing

* Detect choices using:

  * `A.`, `B.`, etc.
* Detect correct answer via `*`

---

### 7.4 TF Parsing

* Extract answer from:

  ```
  Answer: True/False
  ```

---

### 7.5 SHORT Parsing

* Extract answer from:

  ```
  Answer: <text>
  ```

---

## 8. Error Handling

The system should flag:

* Missing `[QUIZ]` tag
* Missing question
* Multiple correct answers in MCQ
* Missing `Answer:` in TF or SHORT
* Invalid format

---

## 9. Best Practices for Teachers

* Keep questions clear and concise
* Use consistent formatting
* Avoid extra symbols inside choices
* Always mark correct answers clearly

---

## 10. Future Enhancements

* Multiple correct answers (checkbox style)
* Case-insensitive short answers
* Auto-feedback support
* Difficulty tagging

---

## 11. Summary

The quiz format is designed to be:

* Simple for teachers
* Easy to parse programmatically
* Consistent across all learning packages

It forms the foundation of assessment within MoRe Learning Packages.
