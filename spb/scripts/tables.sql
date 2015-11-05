CREATE TABLE IF NOT EXISTS template_images (image VARCHAR(255) UNIQUE);
CREATE TABLE IF NOT EXISTS source_images (image VARCHAR(255) UNIQUE);
CREATE TABLE IF NOT EXISTS templates (image_id BIGINT, positions TEXT);

