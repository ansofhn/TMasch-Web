
CREATE DATABASE IF NOT EXISTS tmasch_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tmasch_db;

-- ── ROLES ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS roles (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL
);

-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(50)  NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    role_id    INT          NOT NULL,
    department VARCHAR(100),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- ── CATEGORIES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- ── PRIORITIES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS priorities (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL
);

-- ── STATUSES ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS statuses (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL
);

-- ── TICKETS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tickets (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    title           VARCHAR(200) NOT NULL,
    description     TEXT,
    category_id     INT NOT NULL,
    priority_id     INT,
    status_id       INT NOT NULL DEFAULT 1,
    created_by      INT NOT NULL,
    assigned_to     INT,
    resolution_note TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolved_at     DATETIME NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (priority_id) REFERENCES priorities(id),
    FOREIGN KEY (status_id)   REFERENCES statuses(id),
    FOREIGN KEY (created_by)  REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO roles (name) VALUES ('Admin'), ('Guru'), ('Siswa');

INSERT INTO priorities (name) VALUES ('LOW'), ('MEDIUM'), ('HIGH'), ('CRITICAL');

-- Status: 1=Open, 2=In Progress, 3=Closed (sesuai struktur Java sebelumnya)
INSERT INTO statuses (name) VALUES ('Open'), ('In Progress'), ('Closed');

INSERT INTO categories (name) VALUES
('Akademik'),
('Non-Akademik'),
('Legalisir'),
('Permohonan Data/Info'),
('Pengajuan Perubahan Data');

INSERT INTO users (username, password, full_name, role_id, department) VALUES
('admin', 'admin123', 'Administrator',  1, 'Tata Usaha'),
('budi',  'admin123', 'Budi Santoso',   2, 'Matematika'),
('dewi',  'admin123', 'Dewi Rahayu',    2, 'Bahasa Indonesia'),
('ahmad', 'admin123', 'Ahmad Fauzi',    3, 'XII-IPA-1'),
('siti',  'admin123', 'Siti Rahayu',    3, 'XII-IPA-2');

INSERT INTO tickets (title, description, category_id, priority_id, status_id, created_by) VALUES
('Nilai raport tidak sesuai', 'Nilai Matematika di raport berbeda dengan nilai ulangan', 1, 2, 1, 4),
('Permohonan legalisir ijazah', 'Butuh 3 lembar legalisir untuk keperluan beasiswa', 3, 2, 2, 5),
('Koreksi nama di sistem', 'Nama saya tertulis salah, seharusnya Ahmad Fauzi', 5, 3, 2, 4),
('Info biaya SPP semester baru', 'Minta info rincian biaya SPP semester ganjil', 4, 1, 1, 5);
