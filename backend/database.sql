\c mirante_social;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    valid BOOLEAN DEFAULT false,
    remember_token VARCHAR(100) NULL
);

CREATE TABLE validations (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    user_id INTEGER REFERENCES users(id),
    code VARCHAR(6) NOT NULL,
    time VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE social_projects (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),

    name VARCHAR(255) NOT NULL,
    description TEXT,

    address VARCHAR(255),
    district VARCHAR(255),
    city VARCHAR(255),
    state CHAR(2),
    zip_code VARCHAR(20),

    phone VARCHAR(30),
    contact_email VARCHAR(255),
    website_url VARCHAR(255),

    visual_color VARCHAR(20),

    verified BOOLEAN DEFAULT false,
    verified_at TIMESTAMP NULL,
    badge VARCHAR(50),

    status VARCHAR(50),
    activity_area VARCHAR(255),
    target_audiences TEXT,
    image_path VARCHAR(255),

    needs TEXT,

    rating_avg NUMERIC DEFAULT 0,
    rating_count INTEGER DEFAULT 0,

    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE personalized_pages (
    id                SERIAL PRIMARY KEY,
    social_project_id INT NOT NULL REFERENCES social_projects(id) ON DELETE CASCADE,
    url               VARCHAR(255) NOT NULL,
    caption           VARCHAR(255),
    sort_order        INTEGER DEFAULT 0,
    template          INT NOT NULL DEFAULT 1
);

CREATE TABLE social_project_ratings (
    id                BIGSERIAL PRIMARY KEY,
    social_project_id BIGINT NOT NULL REFERENCES social_projects(id) ON DELETE CASCADE,
    user_id           BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rating            SMALLINT NOT NULL,
    feedback_text     TEXT,
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP
);

CREATE TABLE reports (
    id                SERIAL PRIMARY KEY,
    social_project_id INTEGER REFERENCES social_projects(id) ON DELETE CASCADE,
    reporter_user_id  INTEGER REFERENCES users(id),
    category          VARCHAR(100),
    reason            TEXT,
    status            VARCHAR(50) DEFAULT 'pending',
    reviewed_by       INTEGER REFERENCES users(id),
    reviewed_at       TIMESTAMP NULL,
    resolution_notes  TEXT,
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);

CREATE TABLE sessions (
    id           VARCHAR(255) PRIMARY KEY,
    user_id      BIGINT NULL,
    ip_address   VARCHAR(45) NULL,
    user_agent   TEXT NULL,
    payload      TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
