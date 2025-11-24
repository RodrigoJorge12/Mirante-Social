create TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at timestamp ,
    updated_at timestamp,
    valid boolean default false,
    remember_token VARCHAR(100) NULL;
);

create TABLE validations (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    user_id INTEGER REFERENCES users(id),
    code VARCHAR(6) NOT NULL,
    time VARCHAR(20),
    created_at timestamp,
    updated_at timestamp
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

    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE personalized_pages (
    id                  SERIAL PRIMARY KEY,
    social_project_id   INT NOT NULL REFERENCES social_projects(id) ON DELETE CASCADE,
    url                 VARCHAR(255) NOT NULL,
    caption             VARCHAR(255),
    sort_order          INT NOT NULL DEFAULT 0
);