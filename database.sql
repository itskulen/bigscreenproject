CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255),
    project_status VARCHAR(100),
    project_image VARCHAR(255),
    material_image VARCHAR(255),
    description TEXT
);