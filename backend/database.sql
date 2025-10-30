create TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at timestamp ,
    updated_at timestamp,
    valid boolean
);

create TABLE validations (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    user_id INTEGER REFERENCES users(id),
    code VARCHAR(6) NOT NULL,
    time VARCHAR(20),
    created_at timestamp
);