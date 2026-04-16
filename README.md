# 🚀 MORE Studio — v1.1

MORE Studio is a web-based authoring tool that converts structured `.docx` learning materials into `.more` packages for use in the MORE learning container.

---

## About MORE

MORE is an open standard for packaging and delivering educational content in low-connectivity environments.

It enables educators to transform documents into structured, portable learning packages.

No single entity owns or controls the MORE standard.

This repository provides a reference implementation.

---

## License

- Code: GPL v3
- Standard and documentation: CC-BY 4.0

---

## 🎯 Overview

MORE Studio enables educators to:

- Create structured learning content using familiar tools (Microsoft Word)
- Define assessments using simple markup
- Generate portable `.more` packages
- Deploy content to mobile learning environments

---

## 🔁 Workflow

Create (DOCX) → Upload → Process → Download (.more) → Use in MORE Container

---

## 💾 Database

Import the database structure before running the application.

### Option 1 — phpMyAdmin
1. Open phpMyAdmin
2. Create a database (e.g. `more_studio`)
3. Go to **Import**
4. Upload `/database/database.sql`

### Option 2 — MySQL CLI
mysql -u root -p < database/database.sql

### Database Connection
$conn = new mysqli("localhost", "root", "", "more_studio");

---

## ✨ Features

### 📄 Content Processing Pipeline
- DOCX → HTML conversion (Pandoc)
- Chapter detection via headings (H1)
- Structured content packaging
- Quiz extraction:
  - MCQ (Multiple Choice)
  - TF (True/False)
  - SHORT (Short Answer)
- Image extraction and optimization
- Final `.more` package generation

---

### 🧠 Guided Authoring Interface
- Clean Studio UI for content submission
- Metadata input:
  - Title
  - Subject
  - Contributors
- Inline contributor management (+ / ✕)
- Built-in Authoring Guide (modal)
- Sample `.docx` template download

---

### ✅ Validation (v1)
- Required field checks
- File type restriction (`.docx` only)
- Contributor presence validation
- Guided error feedback via dialog

---

### 📦 Results & Deployment
- Package summary:
  - Chapters detected
  - Assessment items
  - Images extracted
- Download-ready `.more` file
- Post-processing guidance for mobile usage
- Rename + storage instructions

---

### 📚 Package Library
- Integrated modal-based package viewer
- Search by title
- Pagination (Prev / Next)
- Version tracking per package

---

### 🧩 UI System
- Minimal, focused interface
- Unified modal system (Guide + Packages)
- Click-outside-to-close behavior
- Inline navigation:
  - Guide
  - Packages
  - Logout

---

## 🧾 Authoring Format

### 📘 Chapter Structure

Use **Heading 1** in your `.docx` file:

## 🆕 v1.1 — Content Stability & QA Feedback

MORE Studio v1.1 introduces a **deterministic content contract** and a **lightweight validation layer** to improve reliability and authoring feedback.

---

### 🔗 Quiz Linking System (Deterministic Contract)

- Each `[QUIZ]...[/QUIZ]` block is now:
  - Extracted into an individual JSON file  
    ```
    /quizzes/quiz_{chapter}_{index}.json
    ```
  - Replaced in HTML with:
    ```html
    <div class="quiz-block">
      <a href="../quizzes/quiz_X_Y.json">Start Quiz</a>
    </div>
    ```

**Ensures:**
- 1 quiz block → 1 JSON file  
- 1 UI trigger → 1 data source  
- No runtime ambiguity in the container  

---

### 🖼 Image Optimization Pipeline

All images are automatically processed during packaging:

- Max width: **1024px** (no upscaling)
- Format: **JPG**
- Quality: **70%**
- Transparency handling (PNG → white background)
- Inline styles and dimensions removed

**Results:**
- Smaller file size  
- Mobile-ready assets  
- Consistent rendering  

---

### ⚠️ Minimal Validation Layer (Non-blocking)

MORE Studio now detects structural issues in quiz markup:

- Mismatched `[QUIZ]`, `[MCQ]`, `[TF]`, `[SHORT]` tags
- Potential malformed assessment blocks

Validation is:

- Non-blocking (processing continues)
- Visible to QA via warnings
- Not auto-corrected (intent preserved)

**Example warning:**
Chapter 1: Mismatched [MCQ] tags (5 vs 4)


---

### 🔍 QA Feedback Integration

- Validation warnings are passed through the pipeline
- Displayed in the Results page
- Enables faster QA review and correction

---

### 🧠 Parsing Stability Improvements

- Prevents merging of nested or malformed quiz blocks
- Uses safer extraction logic to isolate `[QUIZ]` sections
- Ensures consistent JSON output even with imperfect input

---

### 🧩 Design Philosophy

MORE Studio v1.1 follows:

> **“Guardrails + Human QA”**

- The system detects structural risks  
- QA ensures content quality and intent  

---

# ✨ Version Update

## v1.1
feat: introduce deterministic quiz linking, image optimization, and QA-visible validation layer

- Implement quiz linking system:
  - split quiz blocks into individual JSON files
  - replace [QUIZ] blocks with explicit HTML links
  - enforce 1-to-1 mapping (UI trigger → data file)

- Add image optimization pipeline:
  - resize to max 1024px (no upscaling)
  - convert to JPG (70% quality)
  - handle PNG transparency (white background)
  - remove inline styles and dimensions

- Introduce minimal validation layer:
  - detect mismatched [QUIZ], [MCQ], [TF], [SHORT] tags
  - prevent silent parsing corruption
  - non-blocking validation with warning output

- Integrate warning feedback system:
  - pass validation warnings through pipeline
  - display in Results UI for QA visibility

- Improve parsing stability:
  - prevent merging of malformed or nested quiz blocks
  - ensure consistent JSON output under imperfect input

This update establishes a stable and deterministic content contract, preparing MORE Studio for container integration.

## v1.0
feat: release MORE Studio v1.0 with complete DOCX-to-.more pipeline, guided authoring UI, and integrated package management

- Finalize full processing pipeline:
  DOCX → HTML → structured chapters → quiz parsing → image processing → .more packaging

- Implement guided Studio interface:
  - metadata input (title, subject, contributors)
  - inline contributor management (+ / ✕ controls)
  - file validation (.docx restriction)
  - step-by-step authoring guide (modal)
  - sample DOCX download support

- Add validation layer (v1):
  - required field checks
  - file type enforcement
  - contributor presence validation

- Integrate Results page:
  - package summary (chapters, assessments, images)
  - download flow
  - next-step guidance for mobile usage
  - unified modal system (Guide + Packages)

- Replace dashboard with embedded Package Library:
  - modal-based package browsing
  - search functionality
  - pagination (Prev / Next)
  - version viewing support

- Standardize modal system:
  - shared overlay
  - click-outside-to-close behavior
  - reusable across Studio and Results

- Improve UX consistency:
  - inline navigation (Guide | Packages | Logout)
  - refined button hierarchy and layout
  - minimal, guided interface

This release establishes MORE Studio as a complete, production-ready authoring tool for generating .more learning packages.

## v0.9.0
feat: complete end-to-end processing pipeline with image handling, packaging, and result validation

- Implement full DOCX → .more pipeline
- Add chapter parsing and structured content generation
- Integrate quiz extraction and assessment packaging
- Implement image processing:
  - extract via pandoc (media)
  - convert to optimized JPG (assets)
  - ensure unique filenames (no overwrite)
  - clean HTML paths and remove inline styles

- Add processing flow UI with step indicators
- Fix POST → processing synchronization
- Add result screen with:
  - chapter, assessment, and image counts
  - manual download flow

- Exclude /media from final package
- Ensure stable zip packaging and .more output

This completes the core lifecycle:
input → parse → structure → transform → validate → package → output

## v0.8.0
feat: implement core studio workflow, dashboard system, and UI foundation for MORE Standard

- Add versioning logic based on author_id + title
- Generate dynamic version numbers per package
- Extend manifest with title, subject, packageId, version, and authorId
- Add subject field to packages table and registry

- Implement dashboard (index.php) with:
  - latest version per package
  - search functionality
  - pagination (10 per page)
  - total package count

- Add version history modal via AJAX (get_versions.php)

- Refactor upload flow:
  - structured contributors input
  - replace comma-based input with dynamic fields

- Add studio metadata inputs:
  - title
  - subject
  - contributors

- Establish core workflow:
  landing → dashboard → upload → processing → structured output

This completes the core lifecycle:
create → structure → version → track → output

## v0.7.0
feat: implement dashboard, versioning system, metadata input, and studio improvements

- Add versioning logic based on author_id + title
- Update pipeline to generate dynamic version numbers
- Extend manifest with title, subject, packageId, version, and authorId
- Add subject field to packages table and registry
- Implement dashboard (index.php) with:
  - latest version per package
  - search functionality
  - pagination (10 per page)
  - total package count
- Add version history modal via AJAX (get_versions.php)
- Refactor upload flow to support structured contributors input
- Replace comma-based contributors with dynamic form inputs
- Add studio UI fields for title, subject, and contributors

This completes the core content lifecycle: create → version → track → view.

## v0.6.0
feat: add authentication, author identity, contributors, and pipeline attribution

- Implement Google OAuth login with session handling
- Add persistent author_id system using MySQL
- Create core auth module (getUser, requireAuth, logout)
- Protect upload and index pages with authentication
- Extend pipeline to accept author_id and contributors
- Add contributor parsing (authors, editors, artists)
- Inject structured contributors into manifest.xml
- Ensure owner is always included as primary author
- Refactor upload flow to pass structured data into pipeline

This establishes identity + attribution layer for MORE Studio.

## v0.5.0
feat: add authentication, author identity, and contributor metadata system

- integrated Google Sign-In (OAuth)
- added MySQL persistence for users
- implemented author_id generation (secure random)
- created auth core (login, session, logout, route protection)
- protected upload and index routes
- passed author_id into pipeline
- added structured contributors system (authors, editors, artists)
- updated manifest to include contributor metadata
- extended upload UI for contributor input
- connected identity layer with packaging pipeline

Result: MORE Studio now supports authored content with persistent identity

## v0.4.0
refactor(core): restructure project into core and public modules with pipeline entry point

- moved processing logic into /core (chapter_parser, quiz_parser, image processor, etc.)
- introduced pipeline.php to centralize DOCX → .more workflow
- separated user-facing files into /public directory
- prepared structure for upload → process → download flow
- standardized path handling using __DIR__
- improved project organization for scalability and security

## v0.3.0
feat: implement MORE build pipeline with multi-build support, packaging, and media processing

- Added chapter parsing with structured HTML output
- Implemented quiz extraction (MCQ, TF, SHORT) into JSON
- Introduced multi-build system with unique build directories
- Generated manifest.xml with embedded metadata and format attribution
- Implemented .more packaging (zip-based format)
- Integrated Pandoc media extraction for DOCX images
- Added image processing pipeline with ImageMagick (compression + asset relocation)
- Fixed relative asset path resolution for chapter rendering
- Cleaned HTML by removing quiz markers from output

Result: end-to-end DOCX → .more package pipeline now functional

## v0.2.0
feat(core): establish MORE content pipeline (docx → html → .more package)

- clarified architecture: Pandoc-based conversion from DOCX to HTML
- separated content (HTML) and quizzes (JSON)
- defined initial .more package structure (content, assets, quizzes, manifest)
- aligned container direction to render HTML via WebView
- removed XML parsing approach in favor of direct HTML rendering
- prepared groundwork for mobile-ready parsing and packaging
