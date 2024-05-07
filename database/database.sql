CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    phone VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    type VARCHAR(50)
);
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    token VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS doctors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(255) NOT NULL,
  patients INT NOT NULL,
  experience TEXT,
  bio_data TEXT,
  status VARCHAR(20) NOT NULL,
  user_id INT,
  time VARCHAR(255),
  display_image VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS appointments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  doctor_id INT,
  date DATE,
  status VARCHAR(255),
  user_id INT,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);


