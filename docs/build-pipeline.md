# MoRe Studio v0.1 — Build Pipeline

## 1. Overview

The build pipeline is responsible for transforming a DOCX file into a structured **MoRe Learning Package (.more)**.

It operates as a sequential process executed by a server-side worker.

---

## 2. Pipeline Stages

### Stage 1 — Upload Handling

**Input:** DOCX file

**Process:**

* Store file in `/storage/uploads/`
* Assign version number
* Record in database (`uploads` table)

---

### Stage 2 — Build Initialization

**Triggered by:**

* User action ("Build" button)

**Process:**

* Create new build record
* Set status: `queued` → `processing`
* Create working directory:

```
/storage/processing/build_{id}/
```

---

### Stage 3 — DOCX to HTML Conversion

**Tool:** Pandoc

**Command Example:**

```bash
pandoc input.docx -o output.html
```

**Output:**

* Raw HTML file

---

### Stage 4 — Image Extraction & Compression

**Tools:**

* Pandoc (extract images)
* ImageMagick (compress/optimize)

**Process:**

* Extract images into `/images/`
* Convert to optimized formats (e.g., JPG, PNG)
* Reduce file size

---

### Stage 5 — Chapter Parsing

**Logic:**

* Detect `<h1>` tags
* Split HTML into chapters

**Output:**

* Multiple chapter HTML files
* Stored in `/chapters/`

---

### Stage 6 — Quiz Parsing

**Input:** HTML content

**Process:**

* Detect `[QUIZ]` blocks
* Identify type:

  * MCQ
  * SHORT
  * TF
* Extract:

  * Questions
  * Answers
  * Choices

**Output:**

* Structured quiz JSON files

---

### Stage 7 — Content Structuring

**Process:**

* Associate quizzes with chapters
* Normalize HTML
* Clean formatting

---

### Stage 8 — XML Generation

**Files Generated:**

#### manifest.xml

* Lists all assets
* Defines structure

#### metadata.xml

* Book title
* Author
* Version
* Build info

---

### Stage 9 — Packaging

**Process:**

* Collect:

  * Chapters
  * Quizzes
  * Assets
  * XML files

* Package into ZIP

**Output:**

```
book.more
```

---

### Stage 10 — Output Handling

**Process:**

* Store in `/storage/output/`
* Update build status:

  * `success` or `failed`
* Log build details

---

### Stage 11 — Cleanup

**Process:**

* Remove temporary files
* Optional delayed cleanup via cron

---

## 3. Pipeline Flow Summary

```
[Upload DOCX]
      ↓
[Store + Version]
      ↓
[Trigger Build]
      ↓
[Convert to HTML]
      ↓
[Extract Images]
      ↓
[Parse Chapters]
      ↓
[Parse Quizzes]
      ↓
[Generate XML]
      ↓
[Package .more]
      ↓
[Download]
```

---

## 4. Build Worker

### Execution Model

* PHP CLI script:

```
scripts/build_worker.php
```

---

### Responsibilities

* Fetch queued builds
* Execute pipeline steps
* Update status
* Log errors

---

## 5. Error Handling

System must handle:

* Invalid DOCX files
* Pandoc failure
* Missing quiz structure
* File permission issues
* Packaging errors

---

## 6. Logging

Each build must log:

* Start time
* End time
* Errors encountered
* Processing steps

Stored in:

```
builds.log
```

---

## 7. Performance Considerations

* Large DOCX files may take longer
* Image compression reduces package size
* Future: parallel processing

---

## 8. Design Principles

### 8.1 Deterministic Builds

Same input → same output

---

### 8.2 Modular Processing

Each stage is independent

---

### 8.3 Recoverability

Failed builds can be retried

---

## 9. Future Enhancements

* Queue system (Redis, etc.)
* Progress tracking UI
* Partial rebuilds
* Validation before build

---

## 10. Summary

The build pipeline is the core engine of MoRe Studio.

It transforms raw educational content into structured, portable learning systems ready for deployment.
