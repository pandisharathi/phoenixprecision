-- Database Setup for Phoenix Precision Products

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    bg_image VARCHAR(255),
    btn_text VARCHAR(50),
    btn_link VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    icon VARCHAR(50),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    company VARCHAR(100),
    address TEXT,
    description TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    date_label VARCHAR(100),
    image VARCHAR(255),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100),
    content TEXT,
    image VARCHAR(255),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    summary TEXT,
    content LONGTEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS about_content (
    id INT PRIMARY KEY,
    title VARCHAR(255),
    lead_text TEXT,
    main_text TEXT,
    image VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS top_header_info (
    id INT PRIMARY KEY,
    address VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(100),
    secondary_email VARCHAR(100),
    facebook_url VARCHAR(255),
    instagram_url VARCHAR(255),
    twitter_url VARCHAR(255),
    whatsapp_url VARCHAR(255),
    telegram_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS navbar_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INT PRIMARY KEY,
    site_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT
);

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    logo VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial Admin User (admin / admin123)
INSERT IGNORE INTO admin_users (id, username, password) VALUES (1, 'admin', '$2y$10$U9flCw6NWeHH7UQIAKzLz.TEJNaLM1qiBBOwuVIw78bthFe4p6hD.');

-- Seed Hero Slides
INSERT IGNORE INTO hero_slides (id, title, description, bg_image, btn_text, btn_link) VALUES
(1, 'Innovation for Your Business', 'We provide state-of-the-art solutions to help your business grow and thrive in the modern market.', 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1350&q=80', 'Explore More', '#quoteModal'),
(2, 'Leading Industry Experts', 'Our team of professionals is dedicated to delivering excellence and high-quality results for every project.', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1350&q=80', 'Our Services', '#services'),
(3, 'Future-Proof Solutions', 'Embrace the next generation of business technology with our comprehensive and modern services.', 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1350&q=80', 'Contact Us', '#contact');

-- Seed Services
INSERT IGNORE INTO services (id, title, description, image) VALUES
(1, 'Skilled Professionals', 'Our team consists of industry veterans with years of experience in diverse business sectors.', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=600&q=80'),
(2, 'Modern Equipment', 'We utilize the latest tools and technologies to ensure precision, efficiency, and superior output.', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=600&q=80'),
(3, 'On-Time Delivery', 'Punctuality is our priority. We guarantee timely execution and delivery of all our professional services.', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=600&q=80');

-- Seed Projects
INSERT IGNORE INTO projects (id, title, category, description, date_label, image) VALUES
(1, 'Precision Honing System', 'honing', 'State-of-the-art honing technology for industrial applications.', 'March 2026 | Phoenix Design', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=600&q=80'),
(2, 'Automated CNC Unit', 'machine', 'Reducing overhead with smart machine automation.', 'Feb 2026 | Phoenix Design', 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=600&q=80'),
(3, 'Eco-Friendly Lubricants', 'oils', 'Sustainable oil solutions for high-performance machinery.', 'Jan 2026 | Phoenix Design', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80');

-- Seed Testimonials
INSERT IGNORE INTO testimonials (id, name, position, content, image) VALUES
(1, 'Johnathan Smith', 'CEO, TechIndustries', 'Phoenix Precision Products transformed our production line. Their attention to detail and modern approach are second to none.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80'),
(2, 'Sarah Williams', 'Operations Director', 'Outstanding service and technical expertise. The team at Phoenix Precision delivered our CNC project ahead of schedule.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80');

-- Seed About Content
INSERT IGNORE INTO about_content (id, title, lead_text, main_text, image) VALUES
(1, 'Redefining Business Excellence', 'Helping businesses transform through innovation and strategic insight.', 'Phoenix Precision Products has been at the forefront of business consulting and technical implementation for over a decade. We believe in creating sustainable value for our clients through a partnership built on trust and results.', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80');

-- Seed Top Header
INSERT IGNORE INTO top_header_info (id, address, phone, email, secondary_email, facebook_url, instagram_url, twitter_url, whatsapp_url, telegram_url) VALUES
(1, 'Abuthabo, UAE, 000000.', '+91 7358994111', 'info@example.com', '', 'https://www.facebook.com/', 'https://www.instagram.com/?hl=en', 'https://x.com/', 'https://web.whatsapp.com/', 'https://web.telegram.org/k/');

-- Seed Site Settings
INSERT IGNORE INTO site_settings (id, site_title, meta_description, meta_keywords) 
VALUES (1, 'Phoenix Precision Products', 'Premium manufacturing solutions for precision components.', 'honing, precision, machines');

-- Seed Nav Links
INSERT IGNORE INTO navbar_links (id, label, url, sort_order) VALUES
(1, 'Home', 'index.php#home', 1),
(2, 'About', 'index.php#about', 2),
(3, 'Services', 'index.php#services', 3),
(4, 'Projects', 'index.php#projects', 4),
(5, 'Blog', 'index.php#blogs', 5),
(6, 'Gallery', 'gallery.php', 6),
(7, 'Contact', 'index.php#contact', 7);
