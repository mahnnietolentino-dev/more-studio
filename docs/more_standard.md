# MORE Standard v1.0

## Overview

MORE (Mobile Online Resources for Education) is an open standard for packaging educational content into structured, portable learning modules.

A MORE package is a `.more` file containing content, assessments, and assets organized in a predictable structure.

MORE is designed to work fully offline and requires no external dependencies.

---

## Package Structure

A valid MORE package must follow this structure:

.more/
- manifest.xml
- chapters/
- quizzes/
- assets/

---

## Manifest (manifest.xml)

The manifest defines metadata, contributors, and content structure.

### Required Sections

- metadata
- contributors
- chapters
- quizzes

---

### Example Structure

manifest
  metadata
    title: Sample Title
    subject: Mathematics
    format: MORE
    formatVersion: 1.0
    generator: MORE Studio
    created: YYYY-MM-DD
    packageId: unique_id
  contributors
    person (role=author)
      name: Author Name
  chapters
    chapter
      file: chapters/ch1.html
      title: Chapter 1
  quizzes
    quiz
      file: quizzes/ch1.json

---

## Chapters

- Stored as HTML files
- Located in the chapters/ directory
- Referenced in the manifest
- Must use relative paths only

---

## Quizzes

- Stored as JSON files
- Located in the quizzes/ directory
- Linked to chapters
- Must be readable offline

---

## Assets

- Stored in the assets/ directory
- Includes images and media
- Referenced using relative paths
- Must not depend on external URLs

---

## Core Rules

- All paths must be relative
- Package must function without internet
- No external scripts or dependencies
- manifest.xml must always remain readable

---

## Encryption (Optional Layer)

MORE supports optional encryption for content protection.

### Encrypted Structure

.more/
- manifest.xml
- chapters/*.enc
- quizzes/*.enc
- assets/

---

### Encryption Rules

- manifest.xml must NOT be encrypted
- chapters may be encrypted
- quizzes may be encrypted
- assets remain unencrypted

---

### Manifest Extension

Add:

encryption
  enabled: true
  algorithm: AES-256

---

### Reader Behavior

If encryption is enabled:

1. Read manifest.xml
2. Detect encryption flag
3. Request license or key
4. Decrypt content in memory
5. Render normally

---

### Security Rules

- Encryption keys must NOT be stored inside the package
- Decrypted content must NOT be saved to disk

---

## Application Behavior

A compliant MORE reader must:

- Load manifest.xml
- Render chapters sequentially
- Load quizzes correctly
- Support offline operation
- Handle encrypted and non-encrypted packages

---

## Error Handling

Applications must:

- Reject invalid manifests
- Handle missing files gracefully
- Block access if decryption fails

---

## Governance

MORE is an open standard.

- No single entity owns or controls the standard
- Anyone may implement compatible tools
- Changes must be versioned and documented

---

## Interoperability

Any developer or organization may:

- Build MORE-compatible tools
- Create readers or converters
- Implement encryption systems

No specific platform or service is required.

---

## Intellectual Position

MORE is released as an open standard to support accessible education.

The format, tools, and ecosystem are intended for broad adoption.

Commercial services may exist but are not required to use the standard.

---

## Version

Current Version: 1.0
