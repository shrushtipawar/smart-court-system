<?php
// includes/dynamic-content.php

/**
 * Get about page content from database
 */
function getAboutContent() {
    global $db;
    
    if (!$db || !method_exists($db, 'getConnection')) {
        return getDefaultAboutContent();
    }
    
    try {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM about_content LIMIT 1");
        $stmt->execute();
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($content) {
            return $content;
        } else {
            // If no data in database, return default and insert
            $defaultContent = getDefaultAboutContent();
            insertDefaultAboutContent($defaultContent);
            return $defaultContent;
        }
        
    } catch (Exception $e) {
        error_log("Error fetching about content: " . $e->getMessage());
        return getDefaultAboutContent();
    }
}

/**
 * Get site settings
 */
function getSiteSetting($key, $default = '') {
    global $db;
    
    if (!$db || !method_exists($db, 'getConnection')) {
        return $default;
    }
    
    try {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['value'] : $default;
        
    } catch (Exception $e) {
        error_log("Error fetching site setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Get default about content
 */
function getDefaultAboutContent() {
    return [
        'title' => 'About JusticeFlow',
        'mission' => 'To revolutionize the legal industry through technology, making justice accessible, efficient, and transparent for everyone.',
        'vision' => 'A world where legal services are as accessible as any other essential service, powered by technology and innovation.',
        'hero' => json_encode([
            'title' => 'About JusticeFlow',
            'description' => 'Revolutionizing legal practice through cutting-edge technology and AI-powered solutions.',
            'icon' => 'fa-balance-scale'
        ]),
        'story' => json_encode([
            'text' => 'JusticeFlow was founded in 2023 by a team of legal experts and technologists who recognized the growing need for digital transformation in the legal sector. What started as a small startup has grown into a comprehensive legal tech platform serving thousands of users across the globe.',
            'image_url' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ]),
        'values' => json_encode([
            [
                'title' => 'Integrity',
                'description' => 'We uphold the highest ethical standards in everything we do.',
                'icon' => 'fa-shield-alt',
                'color' => 'primary'
            ],
            [
                'title' => 'Innovation',
                'description' => 'Continuously pushing boundaries to create better legal solutions.',
                'icon' => 'fa-lightbulb',
                'color' => 'warning'
            ],
            [
                'title' => 'Accessibility',
                'description' => 'Making legal services available to everyone, everywhere.',
                'icon' => 'fa-universal-access',
                'color' => 'success'
            ],
            [
                'title' => 'Excellence',
                'description' => 'Striving for perfection in every aspect of our service.',
                'icon' => 'fa-award',
                'color' => 'danger'
            ],
            [
                'title' => 'Collaboration',
                'description' => 'Working together with legal professionals and clients.',
                'icon' => 'fa-handshake',
                'color' => 'info'
            ],
            [
                'title' => 'Transparency',
                'description' => 'Clear, honest communication in all our dealings.',
                'icon' => 'fa-eye',
                'color' => 'primary'
            ]
        ]),
        'team_members' => json_encode([
            [
                'name' => 'Dr. Rajesh Kumar',
                'position' => 'Founder & CEO',
                'qualification' => 'PhD in Law, Harvard Law School',
                'experience' => '25+ years',
                'bio' => 'Former Supreme Court lawyer with expertise in constitutional law and legal tech innovation.',
                'image' => 'https://ui-avatars.com/api/?name=Dr+Rajesh+Kumar&background=1a365d&color=fff&size=200'
            ],
            [
                'name' => 'Sarah Johnson',
                'position' => 'Chief Technology Officer',
                'qualification' => 'MS in Computer Science, Stanford',
                'experience' => '15+ years',
                'bio' => 'Expert in AI and machine learning with experience at leading tech companies.',
                'image' => 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=2d74da&color=fff&size=200'
            ],
            [
                'name' => 'Michael Chen',
                'position' => 'Head of Legal Operations',
                'qualification' => 'JD, Yale Law School',
                'experience' => '20+ years',
                'bio' => 'Former partner at a top law firm, specializing in corporate law and compliance.',
                'image' => 'https://ui-avatars.com/api/?name=Michael+Chen&background=0d9d6b&color=fff&size=200'
            ],
            [
                'name' => 'Priya Sharma',
                'position' => 'Product Director',
                'qualification' => 'MBA, Wharton School',
                'experience' => '12+ years',
                'bio' => 'Product management expert with experience in legal tech startups.',
                'image' => 'https://ui-avatars.com/api/?name=Priya+Sharma&background=ffc107&color=000&size=200'
            ]
        ]),
        'milestones' => json_encode([
            [
                'year' => '2020',
                'event' => 'Company Founded',
                'description' => 'Started with vision to revolutionize legal tech'
            ],
            [
                'year' => '2021',
                'event' => 'First Prototype',
                'description' => 'Developed initial AI-powered legal assistant'
            ],
            [
                'year' => '2022',
                'event' => 'Series A Funding',
                'description' => 'Raised $5M to expand team and technology'
            ],
            [
                'year' => '2023',
                'event' => 'Platform Launch',
                'description' => 'Launched comprehensive JusticeFlow platform'
            ],
            [
                'year' => '2024',
                'event' => 'Global Expansion',
                'description' => 'Expanded services to 25+ countries'
            ]
        ]),
        'statistics' => json_encode([
            [
                'value' => '1M+',
                'label' => 'Active Users'
            ],
            [
                'value' => '50+',
                'label' => 'Countries'
            ],
            [
                'value' => '25K+',
                'label' => 'Legal Cases'
            ],
            [
                'value' => '95%',
                'label' => 'Client Satisfaction'
            ]
        ])
    ];
}

/**
 * Insert default about content into database
 */
function insertDefaultAboutContent($content) {
    global $db;
    
    if (!$db || !method_exists($db, 'getConnection')) {
        return false;
    }
    
    try {
        $conn = $db->getConnection();
        
        // Create table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS about_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            mission TEXT,
            vision TEXT,
            hero TEXT,
            story TEXT,
            values TEXT,
            team_members TEXT,
            milestones TEXT,
            statistics TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);
        
        // Insert default content
        $sql = "INSERT INTO about_content (title, mission, vision, hero, story, values, team_members, milestones, statistics) 
                VALUES (:title, :mission, :vision, :hero, :story, :values, :team_members, :milestones, :statistics)";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute([
            ':title' => $content['title'],
            ':mission' => $content['mission'],
            ':vision' => $content['vision'],
            ':hero' => $content['hero'],
            ':story' => $content['story'],
            ':values' => $content['values'],
            ':team_members' => $content['team_members'],
            ':milestones' => $content['milestones'],
            ':statistics' => $content['statistics']
        ]);
        
    } catch (Exception $e) {
        error_log("Error inserting default about content: " . $e->getMessage());
        return false;
    }
}

/**
 * Get home page content
 */
function getHomeContent() {
    global $db;
    
    if (!$db || !method_exists($db, 'getConnection')) {
        return getDefaultHomeContent();
    }
    
    try {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM home_content LIMIT 1");
        $stmt->execute();
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $content ?: getDefaultHomeContent();
        
    } catch (Exception $e) {
        error_log("Error fetching home content: " . $e->getMessage());
        return getDefaultHomeContent();
    }
}

/**
 * Get features content
 */
function getFeaturesContent() {
    global $db;
    
    if (!$db || !method_exists($db, 'getConnection')) {
        return getDefaultFeaturesContent();
    }
    
    try {
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM features_content");
        $stmt->execute();
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $features ?: getDefaultFeaturesContent();
        
    } catch (Exception $e) {
        error_log("Error fetching features content: " . $e->getMessage());
        return getDefaultFeaturesContent();
    }
}

// Add more functions as needed...
?>