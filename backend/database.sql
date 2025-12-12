CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  valid BOOLEAN DEFAULT FALSE,
  remember_token VARCHAR(100) NULL
);

CREATE TABLE IF NOT EXISTS validations (
  id SERIAL PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  code VARCHAR(6) NOT NULL,
  time VARCHAR(20),
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS social_projects (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
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
  verified BOOLEAN DEFAULT FALSE,
  verified_at TIMESTAMP NULL,
  badge VARCHAR(50),
  status VARCHAR(50),
  activity_area VARCHAR(255),
  target_audiences TEXT,
  image_path VARCHAR(255),
  needs TEXT,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_social_projects_user ON social_projects(user_id);

CREATE TABLE IF NOT EXISTS personalized_pages (
  id SERIAL PRIMARY KEY,
  social_project_id INT NOT NULL REFERENCES social_projects(id) ON DELETE CASCADE,
  url VARCHAR(255) NOT NULL,
  caption VARCHAR(255),
  sort_order INT NOT NULL DEFAULT 0,
  template INT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_personalized_pages_project ON personalized_pages(social_project_id);
CREATE UNIQUE INDEX IF NOT EXISTS uq_personalized_pages_url ON personalized_pages(url);

CREATE TABLE IF NOT EXISTS reports (
  id SERIAL PRIMARY KEY,
  social_project_id INT NOT NULL REFERENCES social_projects(id) ON DELETE CASCADE,
  reporter_user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  category VARCHAR(50) NOT NULL,
  reason TEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  reviewed_by INT NULL REFERENCES users(id) ON DELETE SET NULL,
  reviewed_at TIMESTAMP NULL,
  resolution_notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_reports_project_status ON reports (social_project_id, status);
CREATE INDEX IF NOT EXISTS idx_reports_reporter ON reports (reporter_user_id);
CREATE INDEX IF NOT EXISTS idx_reports_status ON reports (status);

CREATE UNIQUE INDEX IF NOT EXISTS uq_reports_open_by_user_project
ON reports (reporter_user_id, social_project_id)
WHERE status IN ('pending','under_review');

