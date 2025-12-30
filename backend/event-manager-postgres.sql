-- PostgreSQL version of event_manager database schema

-- Drop tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS contacts CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS locations CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- Table structure for table roles
CREATE TABLE roles (
  role_id SERIAL PRIMARY KEY,
  role_name VARCHAR(100) NOT NULL
);

-- Insert default roles
INSERT INTO roles (role_id, role_name) VALUES 
(1, 'Admin'), 
(2, 'Organizer'), 
(3, 'User');

-- Reset sequence
SELECT setval('roles_role_id_seq', 3, true);

-- Table structure for table users
CREATE TABLE users (
  user_id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INTEGER DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles (role_id) ON DELETE SET NULL
);

-- Table structure for table locations
CREATE TABLE locations (
  location_id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  address VARCHAR(255) DEFAULT NULL,
  city VARCHAR(100) DEFAULT NULL,
  capacity INTEGER DEFAULT NULL
);

-- Table structure for table events
CREATE TABLE events (
  event_id SERIAL PRIMARY KEY,
  organizer_id INTEGER DEFAULT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  start_date TIMESTAMP DEFAULT NULL,
  end_date TIMESTAMP DEFAULT NULL,
  location_id INTEGER DEFAULT NULL,
  capacity INTEGER DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_organizer FOREIGN KEY (organizer_id) REFERENCES users (user_id) ON DELETE CASCADE,
  CONSTRAINT fk_events_location FOREIGN KEY (location_id) REFERENCES locations (location_id) ON DELETE SET NULL
);

-- Table structure for table bookings
CREATE TABLE bookings (
  booking_id SERIAL PRIMARY KEY,
  user_id INTEGER DEFAULT NULL,
  event_id INTEGER DEFAULT NULL,
  booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(50) DEFAULT NULL,
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  CONSTRAINT fk_bookings_event FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE
);

-- Table structure for table contacts
CREATE TABLE contacts (
  contact_id SERIAL PRIMARY KEY,
  name VARCHAR(100) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  message TEXT DEFAULT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_events_organizer ON events(organizer_id);
CREATE INDEX idx_events_location ON events(location_id);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_event ON bookings(event_id);
