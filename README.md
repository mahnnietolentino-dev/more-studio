# MoRe Studio v0.9

MoRe Studio is the authoring and packaging server for MoRe Learning Packages (.more).

## Purpose
To allow teachers to convert a full DOCX book into a structured, interactive learning package.

## Features (v0.1)
- Upload DOCX
- Detect chapters (Heading 1)
- Parse quizzes ([MCQ], [SHORT], [TF])
- Convert to HTML (Pandoc)
- Generate .more package

## Status
Initial setup phase

## Author
Mahnnie Tolentino

# Version Updates
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
