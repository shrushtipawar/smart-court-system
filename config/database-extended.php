<?php
// database-extended.php - Extended Database class for admin features

require_once 'config/database.php';

class DatabaseExtended extends Database {
    
    // Site Settings Methods
    public function getSiteSetting($key, $default = '') {
        try {
            $stmt = $this->conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['setting_value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
    
    public function updateSiteSetting($key, $value) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO site_settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            return $stmt->execute([$key, $value, $value]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllSiteSettings($group = null) {
        try {
            if ($group) {
                $stmt = $this->conn->prepare("SELECT * FROM site_settings WHERE setting_group = ? ORDER BY setting_key");
                $stmt->execute([$group]);
            } else {
                $stmt = $this->conn->query("SELECT * FROM site_settings ORDER BY setting_group, setting_key");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Pages Content Methods
    public function getPageContent($page_name, $section_name = null) {
        try {
            if ($section_name) {
                $stmt = $this->conn->prepare("
                    SELECT * FROM pages_content 
                    WHERE page_name = ? AND section_name = ? AND is_active = 1 
                    ORDER BY display_order LIMIT 1
                ");
                $stmt->execute([$page_name, $section_name]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $this->conn->prepare("
                    SELECT * FROM pages_content 
                    WHERE page_name = ? AND is_active = 1 
                    ORDER BY display_order
                ");
                $stmt->execute([$page_name]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $section_name ? null : [];
        }
    }
    
    public function savePageContent($page_name, $section_name, $data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO pages_content (page_name, section_name, title, content, image_url, display_order, meta_data) 
                VALUES (?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                title = VALUES(title), 
                content = VALUES(content), 
                image_url = VALUES(image_url), 
                display_order = VALUES(display_order), 
                meta_data = VALUES(meta_data),
                updated_at = CURRENT_TIMESTAMP
            ");
            
            $meta_data = isset($data['meta_data']) ? json_encode($data['meta_data']) : null;
            
            return $stmt->execute([
                $page_name, 
                $section_name, 
                $data['title'] ?? null,
                $data['content'] ?? null,
                $data['image_url'] ?? null,
                $data['display_order'] ?? 0,
                $meta_data
            ]);
        } catch (Exception $e) {
            error_log("Save page content error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePageContent($page_name, $section_name) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM pages_content WHERE page_name = ? AND section_name = ?");
            return $stmt->execute([$page_name, $section_name]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Team Members Methods
    public function getAllTeamMembers($active_only = false) {
        try {
            if ($active_only) {
                $stmt = $this->conn->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order, name");
            } else {
                $stmt = $this->conn->query("SELECT * FROM team_members ORDER BY display_order, name");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getTeamMember($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function saveTeamMember($data, $id = null) {
        try {
            if ($id) {
                // Update existing
                $stmt = $this->conn->prepare("
                    UPDATE team_members SET 
                    name = ?, position = ?, qualification = ?, experience = ?, 
                    bio = ?, image_url = ?, email = ?, phone = ?, linkedin_url = ?,
                    display_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $data['name'],
                    $data['position'] ?? null,
                    $data['qualification'] ?? null,
                    $data['experience'] ?? null,
                    $data['bio'] ?? null,
                    $data['image_url'] ?? null,
                    $data['email'] ?? null,
                    $data['phone'] ?? null,
                    $data['linkedin_url'] ?? null,
                    $data['display_order'] ?? 0,
                    $data['is_active'] ?? 1,
                    $id
                ]);
            } else {
                // Insert new
                $stmt = $this->conn->prepare("
                    INSERT INTO team_members 
                    (name, position, qualification, experience, bio, image_url, email, phone, linkedin_url, display_order, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                return $stmt->execute([
                    $data['name'],
                    $data['position'] ?? null,
                    $data['qualification'] ?? null,
                    $data['experience'] ?? null,
                    $data['bio'] ?? null,
                    $data['image_url'] ?? null,
                    $data['email'] ?? null,
                    $data['phone'] ?? null,
                    $data['linkedin_url'] ?? null,
                    $data['display_order'] ?? 0,
                    $data['is_active'] ?? 1
                ]);
            }
        } catch (Exception $e) {
            error_log("Save team member error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteTeamMember($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM team_members WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Services Methods
    public function getAllServices($active_only = false) {
        try {
            if ($active_only) {
                $stmt = $this->conn->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order, title");
            } else {
                $stmt = $this->conn->query("SELECT * FROM services ORDER BY display_order, title");
            }
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON features
            foreach ($services as &$service) {
                if ($service['features']) {
                    $service['features'] = json_decode($service['features'], true);
                }
            }
            
            return $services;
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getService($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($service && $service['features']) {
                $service['features'] = json_decode($service['features'], true);
            }
            
            return $service;
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function saveService($data, $id = null) {
        try {
            $features = isset($data['features']) && is_array($data['features']) ? 
                        json_encode($data['features']) : null;
            
            if ($id) {
                $stmt = $this->conn->prepare("
                    UPDATE services SET 
                    title = ?, description = ?, detailed_description = ?, icon = ?, 
                    image_url = ?, features = ?, display_order = ?, is_active = ?, 
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $data['title'],
                    $data['description'] ?? null,
                    $data['detailed_description'] ?? null,
                    $data['icon'] ?? null,
                    $data['image_url'] ?? null,
                    $features,
                    $data['display_order'] ?? 0,
                    $data['is_active'] ?? 1,
                    $id
                ]);
            } else {
                $stmt = $this->conn->prepare("
                    INSERT INTO services 
                    (title, description, detailed_description, icon, image_url, features, display_order, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                return $stmt->execute([
                    $data['title'],
                    $data['description'] ?? null,
                    $data['detailed_description'] ?? null,
                    $data['icon'] ?? null,
                    $data['image_url'] ?? null,
                    $features,
                    $data['display_order'] ?? 0,
                    $data['is_active'] ?? 1
                ]);
            }
        } catch (Exception $e) {
            error_log("Save service error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteService($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM services WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Documents Methods
    public function getAllDocuments($category = null, $public_only = true) {
        try {
            $sql = "SELECT d.*, u.username as uploaded_by_name FROM documents d 
                    LEFT JOIN users u ON d.uploaded_by = u.id";
            
            $conditions = [];
            $params = [];
            
            if ($public_only) {
                $conditions[] = "d.is_public = 1";
            }
            
            if ($category) {
                $conditions[] = "d.category = ?";
                $params[] = $category;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY d.uploaded_at DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getDocumentCategories() {
        try {
            $stmt = $this->conn->query("SELECT DISTINCT category FROM documents WHERE category IS NOT NULL ORDER BY category");
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function saveDocument($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO documents 
                (title, description, file_url, file_name, file_size, file_type, category, is_public, uploaded_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $data['file_url'],
                $data['file_name'] ?? null,
                $data['file_size'] ?? 0,
                $data['file_type'] ?? null,
                $data['category'] ?? null,
                $data['is_public'] ?? 1,
                $data['uploaded_by'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Save document error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteDocument($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM documents WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Contact Methods
    public function saveContactSubmission($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO contact_submissions 
                (name, email, phone, subject, message, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['subject'] ?? null,
                $data['message'],
                $data['ip_address'] ?? null,
                $data['user_agent'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Save contact submission error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllContactSubmissions($status = null) {
        try {
            if ($status) {
                $stmt = $this->conn->prepare("SELECT * FROM contact_submissions WHERE status = ? ORDER BY created_at DESC");
                $stmt->execute([$status]);
            } else {
                $stmt = $this->conn->query("SELECT * FROM contact_submissions ORDER BY created_at DESC");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function updateContactStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Statistics Methods
    public function getDashboardStats() {
        try {
            $stats = [
                'total_users' => 0,
                'total_cases' => 0,
                'total_contacts' => 0,
                'total_documents' => 0,
                'new_contacts_today' => 0
            ];
            
            // Total users
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_users'] = $result['total'] ?? 0;
            
            // Total cases
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM cases");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_cases'] = $result['total'] ?? 0;
            
            // Total contacts
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM contact_submissions");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_contacts'] = $result['total'] ?? 0;
            
            // Total documents
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM documents");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_documents'] = $result['total'] ?? 0;
            
            // New contacts today
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM contact_submissions WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['new_contacts_today'] = $result['total'] ?? 0;
            
            return $stats;
        } catch (Exception $e) {
            return [
                'total_users' => 0,
                'total_cases' => 0,
                'total_contacts' => 0,
                'total_documents' => 0,
                'new_contacts_today' => 0
            ];
        }
    }
}
?>