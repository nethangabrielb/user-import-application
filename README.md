# User Import Application

A PHP and React application for parsing, validating, normalizing, and importing user records from CSV files into PostgreSQL, with both CLI and web interface.

---

## Overview

The application processes user CSV files (containing `name`, `surname`, `email`) through an import pipeline:

1. **Validation**: Checks row structure, ensures non-empty names/surnames, and validates email formats.
2. **Normalization**: Trims whitespace, capitalizes names and surnames (e.g. `john` $\to$ `John`), and lowercases emails (e.g. `JOHN@EXAMPLE.COM` $\to$ `john@example.com`).
3. **Deduplication**: Detects duplicate emails within the CSV file as well as records that already exist in PostgreSQL.
4. **Persistence / Dry Run**: Inserts valid records into PostgreSQL with prepared statements, or reports simulated results in dry-run mode without modifying the database.

---

## Requirements

- **PHP**: 8.3 or higher with `pdo_pgsql` extension enabled
- **PostgreSQL**: 14 or higher
- **Composer**: 2.x
- **Node.js**: 18+ & **npm**

---

## Installation / Setup

1. **Clone the repository**:

   ```bash
   git clone <repository-url>
   cd user-import-application
   ```

2. **Install PHP backend dependencies**:

   ```bash
   composer install
   ```

3. **Install React frontend dependencies**:
   ```bash
   cd frontend
   npm install
   cd ..
   ```

---

## Database Configuration

1. Copy the example environment file or edit `.env` in the project root:

   ```ini
   DB_DRIVER=pgsql
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=moodle
   DB_USER=postgres
   DB_PASS=your_password
   ```

2. Ensure your PostgreSQL server is running and the database exists:

   ```sql
   CREATE DATABASE moodle;
   ```

3. Initialize/rebuild the `users` table using the CLI tool:
   ```bash
   php bin/user_upload.php --create-table
   ```

---

## Starting the Application

To run the Web UI and API simultaneously:

1. **Start the PHP Backend API** (Terminal 1):

   ```bash
   php -S localhost:8000 -t public
   ```

2. **Start the React Frontend** (Terminal 2):
   ```bash
   cd frontend
   npm run dev
   ```
   Open your browser at `http://localhost:5173`.

---

## Using the Web UI

1. **Upload**: Drag and drop your `.csv` file onto the dropzone or click to select a file.
2. **Preview & Validate**: The application calls `POST /api/preview` to parse and validate the file in dry-run mode. A summary of valid, duplicate, and invalid records is displayed alongside an interactive preview table with color-coded status badges and detailed error reasons.
3. **Import**: Click **"Import Valid Users"** to call `POST /api/import` and save the validated records to PostgreSQL.
4. **Results**: Review the final import confirmation and click **"Import Another File"** to reset.

---

## Using the CLI

The CLI runner is located at `bin/user_upload.php`.

### Options:

- `--file [filename]`: The path to the CSV file to be parsed and imported.
- `--create-table`: Drops and rebuilds the PostgreSQL `users` table.
- `--dry-run`: Runs the validation and deduplication pipeline without inserting any data into the database.
- `--help`: Displays help and usage instructions.

---

## Examples of CLI Commands

- **Display help instructions**:

  ```bash
  php bin/user_upload.php --help
  ```

- **Create / rebuild the database table**:

  ```bash
  php bin/user_upload.php --create-table
  ```

- **Run a dry run on a CSV file (no DB modifications)**:

  ```bash
  php bin/user_upload.php --file samples/users.csv --dry-run
  ```

- **Import records into the database**:
  ```bash
  php bin/user_upload.php --file samples/users.csv
  ```

---

## Running Automated Tests

Run the PHPUnit test suite covering validators, normalizers, deduplicators, and the import pipeline:

```bash
./vendor/bin/phpunit
```

---

## Assumptions & Design Decisions

1. **Single Source of Truth Core Engine**:
   Both the CLI script (`bin/user_upload.php`) and the Web API (`public/index.php`) share the exact same underlying business logic classes (`CsvReader`, `RowValidator`, `RowNormalizer`, `Deduplicator`, `ImportService`), which guarantees a consistent behavior across both interfaces.

2. **Rebuilding Table on `--create-table`**:
   The `--create-table` directive executes `DROP TABLE IF EXISTS users; CREATE TABLE users (...)` to fulfill the specification requirement to create/rebuild the table cleanly.

3. **Name & Surname Normalization**:
   Names and surnames are trimmed and formatted with title capitalization (`ucfirst(strtolower(trim($value)))`), converting inputs like `"JOHN"` or `"   smith "` to `"John"` and `"Smith"`.

4. **Email Normalization & Case Insensitivity**:
   Email addresses are trimmed and converted to lowercase (`strtolower(trim($email))`) before validation and storage, preventing duplicate accounts due to casing differences.

5. **Deduplication Strategy**:
   - **In-batch deduplication**: An in-memory set in `Deduplicator` tracks emails within the same CSV batch to catch multiple entries within a single file.
   - **Database deduplication**: `UserRepository::emailExists()` checks PostgreSQL with a parameterized query before insertion to prevent unique constraint violations.

6. **Validation Rules**:
   - Rows must have exactly 3 columns.
   - Name and surname must not be empty or whitespace-only.
   - Emails are validated using PHP's native `filter_var($email, FILTER_VALIDATE_EMAIL)`.
   - Clear error messages identify the exact CSV line number, raw data, and failure reasons.

7. **Frontend Architecture**:
   Built with Vite, React, Tailwind CSS, shadcn/ui components, TanStack Query for asynchronous state management, `react-dropzone` for file handling, and `sonner` for toast notifications.
