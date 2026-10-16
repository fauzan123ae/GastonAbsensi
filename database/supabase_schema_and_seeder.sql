-- ==============================================================================
-- Gaston Absensi - PostgreSQL / Supabase Schema & Seeder
-- ==============================================================================
-- Petunjuk:
-- Salin dan jalankan seluruh script ini di Supabase SQL Editor.
-- Script ini membuat semua tabel yang dibutuhkan aplikasi Laravel Gaston Absensi
-- dan mengisi data awal (seeder) untuk admin, member, cabor, lokasi, sesi, dan absensi.
-- ==============================================================================

-- 0. BERSIHKAN TABEL LAMA JIKA ADA (DROP TABLES)
DROP TABLE IF EXISTS audit_logs CASCADE;
DROP TABLE IF EXISTS permission_requests CASCADE;
DROP TABLE IF EXISTS attendances CASCADE;
DROP TABLE IF EXISTS session_participants CASCADE;
DROP TABLE IF EXISTS sport_sessions CASCADE;
DROP TABLE IF EXISTS locations CASCADE;
DROP TABLE IF EXISTS sports CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;

-- ==============================================================================
-- 1. TABEL UTAMA APLIKASI
-- ==============================================================================

-- Tabel Users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    member_id VARCHAR(255) UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(255),
    email_verified_at TIMESTAMP WITH TIME ZONE,
    password VARCHAR(255) NOT NULL,
    photo VARCHAR(255),
    qr_token VARCHAR(255) UNIQUE,
    role VARCHAR(50) DEFAULT 'member' NOT NULL CHECK (role IN ('master_admin', 'admin', 'member')),
    status VARCHAR(50) DEFAULT 'active' NOT NULL CHECK (status IN ('active', 'inactive')),
    remember_token VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Tabel Sports (Cabang Olahraga)
CREATE TABLE sports (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'active' NOT NULL CHECK (status IN ('active', 'inactive')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sports_status ON sports(status);

-- Tabel Locations (Lokasi / GOR)
CREATE TABLE locations (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    description TEXT,
    status VARCHAR(50) DEFAULT 'active' NOT NULL CHECK (status IN ('active', 'inactive')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_locations_status ON locations(status);

-- Tabel Sport Sessions (Sesi Olahraga)
CREATE TABLE sport_sessions (
    id BIGSERIAL PRIMARY KEY,
    sport_id BIGINT NOT NULL REFERENCES sports(id) ON DELETE CASCADE,
    location_id BIGINT NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status VARCHAR(50) DEFAULT 'draft' NOT NULL CHECK (status IN ('draft', 'open', 'ongoing', 'completed', 'cancelled')),
    notes TEXT,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sport_sessions_date ON sport_sessions(date);
CREATE INDEX idx_sport_sessions_status ON sport_sessions(status);

-- Tabel Session Participants (Peserta Terdaftar di Sesi)
CREATE TABLE session_participants (
    id BIGSERIAL PRIMARY KEY,
    session_id BIGINT NOT NULL REFERENCES sport_sessions(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    registration_source VARCHAR(50) DEFAULT 'manual' NOT NULL CHECK (registration_source IN ('whatsapp', 'manual')),
    status VARCHAR(50) DEFAULT 'registered' NOT NULL CHECK (status IN ('registered', 'cancelled')),
    registered_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uniq_session_user_participant UNIQUE (session_id, user_id)
);

CREATE INDEX idx_session_participants_status ON session_participants(status);

-- Tabel Attendances (Presensi/Absensi Kehadiran)
CREATE TABLE attendances (
    id BIGSERIAL PRIMARY KEY,
    session_id BIGINT NOT NULL REFERENCES sport_sessions(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    checked_in_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    checked_in_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    method VARCHAR(50) DEFAULT 'qr' NOT NULL CHECK (method IN ('qr', 'manual')),
    is_walk_in BOOLEAN DEFAULT FALSE NOT NULL,
    status VARCHAR(50) DEFAULT 'present' NOT NULL CHECK (status IN ('present', 'cancelled')),
    notes VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uniq_session_user_attendance UNIQUE (session_id, user_id)
);

CREATE INDEX idx_attendances_checked_in_at ON attendances(checked_in_at);
CREATE INDEX idx_attendances_status ON attendances(status);

-- Tabel Permission Requests (Pengajuan Izin)
CREATE TABLE permission_requests (
    id BIGSERIAL PRIMARY KEY,
    session_id BIGINT NOT NULL REFERENCES sport_sessions(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reason TEXT NOT NULL,
    proof VARCHAR(255),
    review_note TEXT,
    status VARCHAR(50) DEFAULT 'pending' NOT NULL CHECK (status IN ('pending', 'approved', 'rejected')),
    reviewed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    reviewed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uniq_session_user_permission UNIQUE (session_id, user_id)
);

CREATE INDEX idx_permission_requests_status ON permission_requests(status);

-- Tabel Audit Logs
CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(255) NOT NULL,
    target_type VARCHAR(255),
    target_id BIGINT,
    description TEXT,
    old_values JSONB,
    new_values JSONB,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_audit_logs_target ON audit_logs(target_type, target_id);

-- ==============================================================================
-- 2. TABEL BAWAAN LARAVEL (AUTH, SESSIONS, JOBS & CACHE)
-- ==============================================================================

-- Password Reset Tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE
);

-- Sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- Cache
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- Jobs
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL
);


-- ==============================================================================
-- 3. SEEDER DATA (DATA AWAL)
-- Password default semua user: password
-- Catatan Hash: $2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu
-- ==============================================================================

-- 3.1 USERS (Master Admin, Admin, Member)
INSERT INTO users (id, member_id, name, email, phone, password, qr_token, role, status, created_at, updated_at) VALUES
(1, 'GST-MSTR0', 'Master Admin Gaston', 'master@gaston.test', '081299990001', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-QR-MASTER0000000001', 'master_admin', 'active', NOW(), NOW()),
(2, 'GST-00000', 'Admin Gaston', 'admin@gaston.test', '081234567890', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-QR-ADMIN00000000002', 'master_admin', 'active', NOW(), NOW()),
(3, 'GST-ADM01', 'Admin Staff (Operasional)', 'staff@gaston.test', '081234567891', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-QR-STAFF00000000003', 'admin', 'active', NOW(), NOW()),
(4, 'GST-00001', 'Fauzan', 'fauzan@gaston.test', '08121111001', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-FAUZAN0001', 'member', 'active', NOW(), NOW()),
(5, 'GST-00002', 'Andi', 'andi@gaston.test', '08121111002', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-ANDI000002', 'member', 'active', NOW(), NOW()),
(6, 'GST-00003', 'Budi', 'budi@gaston.test', '08121111003', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-BUDI000003', 'member', 'active', NOW(), NOW()),
(7, 'GST-00004', 'Rizky', 'rizky@gaston.test', '08121111004', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-RIZKY00004', 'member', 'active', NOW(), NOW()),
(8, 'GST-00005', 'Dimas', 'dimas@gaston.test', '08121111005', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-DIMAS00005', 'member', 'active', NOW(), NOW()),
(9, 'GST-00006', 'Fajar', 'fajar@gaston.test', '08121111006', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-FAJAR00006', 'member', 'active', NOW(), NOW()),
(10, 'GST-00007', 'Adit', 'adit@gaston.test', '08121111007', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-ADIT000007', 'member', 'active', NOW(), NOW()),
(11, 'GST-00008', 'Rian', 'rian@gaston.test', '08121111008', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-RIAN000008', 'member', 'active', NOW(), NOW()),
(12, 'GST-00009', 'Bayu', 'bayu@gaston.test', '08121111009', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-BAYU000009', 'member', 'active', NOW(), NOW()),
(13, 'GST-00010', 'Ilham', 'ilham@gaston.test', '08121111010', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-ILHAM00010', 'member', 'active', NOW(), NOW()),
(14, 'GST-00011', 'Nonaktif Eko', 'eko@gaston.test', '08121111011', '$2y$10$CjzHjF8DYwMPWmuCngi8LOzR70dJO7Rt2ZMnK1hwkSYIpxSQJ1DCu', 'GST-MBR-EKO0000011', 'member', 'inactive', NOW(), NOW());

-- Sesuaikan urutan auto increment ID users
SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));

-- 3.2 SPORTS (Cabang Olahraga)
INSERT INTO sports (id, name, description, status, created_at, updated_at) VALUES
(1, 'Futsal', 'Olahraga futsal beregu 5 vs 5', 'active', NOW(), NOW()),
(2, 'Basket', 'Olahraga bola basket lapangan', 'active', NOW(), NOW()),
(3, 'Badminton', 'Bulu tangkis ganda & tunggal', 'active', NOW(), NOW()),
(4, 'Voli', 'Bola voli lapangan', 'active', NOW(), NOW()),
(5, 'Tenis', 'Tenis lapangan outdoor/indoor', 'active', NOW(), NOW());

SELECT setval('sports_id_seq', (SELECT MAX(id) FROM sports));

-- 3.3 LOCATIONS (Lokasi / GOR)
INSERT INTO locations (id, name, address, description, status, created_at, updated_at) VALUES
(1, 'GOR Manahan', 'Jl. Adi Sucipto No. 1, Manahan, Solo', 'Kompleks Olahraga Manahan', 'active', NOW(), NOW()),
(2, 'GOR Kartasura', 'Jl. Ahmad Yani, Kartasura, Sukoharjo', 'GOR Bulutangkis & Futsal', 'active', NOW(), NOW()),
(3, 'GOR Klaten', 'Jl. Pemuda, Klaten', 'GOR Serbaguna Klaten', 'active', NOW(), NOW());

SELECT setval('locations_id_seq', (SELECT MAX(id) FROM locations));

-- 3.4 SPORT SESSIONS (Sesi Olahraga)
INSERT INTO sport_sessions (id, sport_id, location_id, date, start_time, end_time, status, notes, created_by, created_at, updated_at) VALUES
(1, 1, 1, CURRENT_DATE, '19:00:00', '21:00:00', 'open', 'Sesi Futsal Rutin Mingguan', 1, NOW(), NOW()),
(2, 3, 2, CURRENT_DATE + INTERVAL '2 day', '16:00:00', '18:00:00', 'draft', 'Latihan bulutangkis ganda', 1, NOW(), NOW());

SELECT setval('sport_sessions_id_seq', (SELECT MAX(id) FROM sport_sessions));

-- 3.5 SESSION PARTICIPANTS (Peserta di Sesi 1)
INSERT INTO session_participants (session_id, user_id, registration_source, status, registered_at, created_at, updated_at) VALUES
(1, 4, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 5, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 6, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 7, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 8, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 9, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 10, 'whatsapp', 'registered', NOW(), NOW(), NOW()),
(1, 11, 'whatsapp', 'registered', NOW(), NOW(), NOW());

-- 3.6 ATTENDANCES (Presensi yang sudah hadir di Sesi 1)
INSERT INTO attendances (session_id, user_id, checked_in_at, checked_in_by, method, is_walk_in, status, created_at, updated_at) VALUES
(1, 4, NOW() - INTERVAL '45 minute', 1, 'qr', false, 'present', NOW(), NOW()),
(1, 5, NOW() - INTERVAL '30 minute', 1, 'manual', false, 'present', NOW(), NOW()),
(1, 6, NOW() - INTERVAL '15 minute', 1, 'manual', false, 'present', NOW(), NOW());

-- 3.7 PERMISSION REQUESTS (Pengajuan Izin)
INSERT INTO permission_requests (session_id, user_id, reason, proof, review_note, status, created_at, updated_at) VALUES
(1, 7, 'Sedang demam dan istirahat dokter', NULL, NULL, 'pending', NOW(), NOW());
