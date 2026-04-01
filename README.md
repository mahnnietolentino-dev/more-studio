# 🚀 MoRe Studio — v1.0

MoRe Studio is a web-based authoring tool that converts structured `.docx` learning materials into `.more` packages for use in the MoRe learning container.

---

## 🎯 Overview

MoRe Studio enables educators to:

- Create structured learning content using familiar tools (Microsoft Word)
- Define assessments using simple markup
- Generate portable `.more` packages
- Deploy content to mobile learning environments

---

## 🔁 Workflow

Create (DOCX) → Upload → Process → Download (.more) → Use in MoRe Container

---

## Database

Import the database structure before running the application.

### Option 1 — phpMyAdmin
1. Open phpMyAdmin
2. Create a database (e.g. `more_studio`)
3. Go to **Import**
4. Upload `/database/database.sql`

### Option 2 — MySQL CLI

```bash
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

# Version Updates
## v1.0
feat: release MoRe Studio v1.0 with complete DOCX-to-.more pipeline, guided authoring UI, and integrated package management

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

This release establishes MoRe Studio as a complete, production-ready authoring tool for generating .more learning packages.

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
feat: implement core studio workflow, dashboard system, and UI foundation for MoRe Standard

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

This establishes identity + attribution layer for MoRe Studio.

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

Result: MoRe Studio now supports authored content with persistent identity

## v0.4.0
refactor(core): restructure project into core and public modules with pipeline entry point

- moved processing logic into /core (chapter_parser, quiz_parser, image processor, etc.)
- introduced pipeline.php to centralize DOCX → .more workflow
- separated user-facing files into /public directory
- prepared structure for upload → process → download flow
- standardized path handling using __DIR__
- improved project organization for scalability and security

## v0.3.0
feat: implement MoRe build pipeline with multi-build support, packaging, and media processing

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
feat(core): establish MoRe content pipeline (docx → html → .more package)

- clarified architecture: Pandoc-based conversion from DOCX to HTML
- separated content (HTML) and quizzes (JSON)
- defined initial .more package structure (content, assets, quizzes, manifest)
- aligned container direction to render HTML via WebView
- removed XML parsing approach in favor of direct HTML rendering
- prepared groundwork for mobile-ready parsing and packaging
