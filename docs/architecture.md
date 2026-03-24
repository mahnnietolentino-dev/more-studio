# MoRe Studio v0.1 — Architecture

## 1. Overview

MoRe Studio is a server-side system that transforms a teacher-authored DOCX file into a structured **MoRe Learning Package (.more)**.

It acts as a **compiler for educational content**, converting unstructured documents into structured, machine-readable learning assets.

---

## 2. System Layers

MoRe Studio is composed of three main layers:

### 2.1 Authoring Layer

Responsible for user interaction and data management.

**Responsibilities:**

* User authentication
* Book/project management
* DOCX upload and versioning
* Contributor management
* Build triggering
* Audit logging

---

### 2.2 Processing Layer

Responsible for transforming raw DOCX into structured content.

**Responsibilities:**

* DOCX → HTML conversion (Pandoc)
* Chapter detection (Heading 1)
* Quiz parsing ([MCQ], [SHORT], [TF])
* Image extraction and compression (ImageMagick)
* Content normalization

---

### 2.3 Build Layer

Responsible for generating the final `.more` package.

**Responsibilities:**

* Chapter splitting
* Asset organization
* XML generation (manifest.xml, metadata.xml)
* Packaging into ZIP container
* Temporary file handling

---

## 3. High-Level Data Flow

```
[Teacher Uploads DOCX]
        ↓
[Stored + Versioned]
        ↓
[Build Triggered]
        ↓
[Processing Layer]
    - Convert to HTML
    - Parse chapters
    - Extract quizzes
    - Extract images
        ↓
[Build Layer]
    - Generate chapters
    - Generate XML files
    - Package into .more
        ↓
[Download .more]
```

---

## 4. Core Services

### 4.1 BuildService

* Orchestrates the entire pipeline
* Handles build states and logging

---

### 4.2 ChapterParser

* Detects chapters via Heading 1
* Splits HTML into structured sections

---

### 4.3 QuizParser

* Detects `[QUIZ]` blocks
* Parses:

  * MCQ
  * SHORT
  * True/False

---

### 4.4 PackagingService

* Generates:

  * manifest.xml
  * metadata.xml
* Builds `.more` file using ZIP

---

## 5. Storage Strategy

### 5.1 Upload Storage

* Stores versioned DOCX files
* Persistent

### 5.2 Processing Storage

* Temporary build files
* Deleted after build or via cron

### 5.3 Output Storage

* Generated `.more` packages
* Temporary only (not permanently stored)

---

## 6. Build Execution Model

Builds are executed via:

* PHP CLI worker (`build_worker.php`)
* Triggered by:

  * Manual build request
  * Queue system (future)

---

## 7. Design Principles

### 7.1 Stateless Builds

Generated `.more` files are not permanently stored.

---

### 7.2 Deterministic Output

Same DOCX input → same `.more` output.

---

### 7.3 Separation of Concerns

Each service has a single responsibility.

---

### 7.4 Teacher-Centric Input

Teachers only need to:

* Upload DOCX
* Follow simple tagging rules

---

## 8. Future Considerations

* Build queue system
* Version comparison
* Live preview of parsed content
* Validation before build
* Distributed processing

---

## 9. Summary

MoRe Studio transforms traditional documents into structured, portable learning systems.

It serves as the **foundation of the MoRe ecosystem**, enabling scalable, offline-capable education delivery.

