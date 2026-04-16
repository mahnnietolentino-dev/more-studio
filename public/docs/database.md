# MoRe Studio v0.1 — Database Design

## 1. Overview

The database supports:

* User management
* Book/project ownership
* Collaborative authorship
* DOCX version tracking
* Structured content (chapters, quizzes)
* Build tracking
* Audit logging

---

## 2. Core Entities

### 2.1 Users

Represents system users (teachers, admins).

**Table: users**

| Field         | Type             | Description        |
| ------------- | ---------------- | ------------------ |
| id            | INT (PK)         | Unique user ID     |
| name          | VARCHAR          | Full name          |
| email         | VARCHAR (UNIQUE) | Login identifier   |
| password_hash | VARCHAR          | Encrypted password |
| role          | ENUM             | admin, teacher     |
| created_at    | TIMESTAMP        | Creation date      |

---

### 2.2 Books

Represents a learning package project.

**Table: books**

| Field       | Type              | Description            |
| ----------- | ----------------- | ---------------------- |
| id          | INT (PK)          | Book ID                |
| title       | VARCHAR           | Book title             |
| description | TEXT              | Description            |
| owner_id    | INT (FK users.id) | Creator                |
| status      | ENUM              | draft, building, built |
| created_at  | TIMESTAMP         | Created                |
| updated_at  | TIMESTAMP         | Updated                |

---

### 2.3 Book Contributors

Supports collaborative authorship.

**Table: book_contributors**

| Field    | Type              | Description           |
| -------- | ----------------- | --------------------- |
| id       | INT (PK)          | Entry ID              |
| book_id  | INT (FK books.id) | Book                  |
| user_id  | INT (FK users.id) | Contributor           |
| role     | ENUM              | owner, editor, viewer |
| added_at | TIMESTAMP         | Added date            |

---

### 2.4 Uploads

Tracks DOCX versions.

**Table: uploads**

| Field       | Type              | Description       |
| ----------- | ----------------- | ----------------- |
| id          | INT (PK)          | Upload ID         |
| book_id     | INT (FK books.id) | Book              |
| filename    | VARCHAR           | Original filename |
| filepath    | VARCHAR           | Stored path       |
| version     | INT               | Version number    |
| uploaded_by | INT (FK users.id) | Uploader          |
| created_at  | TIMESTAMP         | Upload time       |

---

### 2.5 Chapters

Structured content extracted from DOCX.

**Table: chapters**

| Field        | Type              | Description   |
| ------------ | ----------------- | ------------- |
| id           | INT (PK)          | Chapter ID    |
| book_id      | INT (FK books.id) | Book          |
| title        | VARCHAR           | Chapter title |
| order_index  | INT               | Chapter order |
| html_content | LONGTEXT          | Parsed HTML   |
| created_at   | TIMESTAMP         | Created       |

---

### 2.6 Quizzes

Quiz questions extracted from content.

**Table: quizzes**

| Field         | Type                 | Description    |
| ------------- | -------------------- | -------------- |
| id            | INT (PK)             | Quiz ID        |
| chapter_id    | INT (FK chapters.id) | Chapter        |
| type          | ENUM                 | MCQ, SHORT, TF |
| question_text | TEXT                 | Question       |
| metadata      | JSON                 | Extra data     |
| order_index   | INT                  | Position       |

---

### 2.7 Quiz Choices

For multiple-choice questions.

**Table: quiz_choices**

| Field       | Type                | Description    |
| ----------- | ------------------- | -------------- |
| id          | INT (PK)            | Choice ID      |
| quiz_id     | INT (FK quizzes.id) | Quiz           |
| choice_text | TEXT                | Option         |
| is_correct  | BOOLEAN             | Correct answer |

---

### 2.8 Builds

Tracks build processes and outputs.

**Table: builds**

| Field        | Type              | Description                         |
| ------------ | ----------------- | ----------------------------------- |
| id           | INT (PK)          | Build ID                            |
| book_id      | INT (FK books.id) | Book                                |
| status       | ENUM              | queued, processing, success, failed |
| output_path  | VARCHAR           | Temporary .more file                |
| log          | LONGTEXT          | Build logs                          |
| created_by   | INT (FK users.id) | Triggered by                        |
| created_at   | TIMESTAMP         | Start time                          |
| completed_at | TIMESTAMP         | End time                            |

---

### 2.9 Audit Logs

Tracks system activity for accountability.

**Table: audit_logs**

| Field      | Type              | Description     |
| ---------- | ----------------- | --------------- |
| id         | INT (PK)          | Log ID          |
| user_id    | INT (FK users.id) | Actor           |
| book_id    | INT (FK books.id) | Related book    |
| action     | VARCHAR           | Action type     |
| details    | JSON              | Additional info |
| created_at | TIMESTAMP         | Timestamp       |

---

## 3. Relationships Overview

* One **user** → many **books**
* One **book** → many **contributors**
* One **book** → many **uploads**
* One **book** → many **chapters**
* One **chapter** → many **quizzes**
* One **quiz** → many **choices**
* One **book** → many **builds**
* One **user** → many **audit logs**

---

## 4. Design Considerations

### 4.1 Versioning

Uploads are versioned to allow rebuilding from any state.

---

### 4.2 Normalization

Data is normalized to:

* Avoid duplication
* Support scalability
* Enable analytics later

---

### 4.3 JSON Fields

Used for flexible metadata (quiz configs, logs).

---

### 4.4 Auditability

Every major action is logged.

---

## 5. Future Extensions

* Tagging system for books
* Analytics tables
* Student interaction tracking (separate system)
* Version diff tracking

---

## 6. Summary

The database is designed to support:

* Structured content generation
* Collaborative authorship
* Reliable build tracking
* Full audit visibility

It serves as the backbone of MoRe Studio’s processing pipeline.
