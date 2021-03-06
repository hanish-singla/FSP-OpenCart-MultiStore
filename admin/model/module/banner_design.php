<?php

class ModelModuleBannerDesign extends Model {

    public function addBanner($data) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int) $data['status'] . "',bannerType = '" . $this->db->escape($data['bannerType']) . "',bannerStyle = '" . $this->db->escape($data['bannerStyle']) . "'");

        $banner_id = $this->db->getLastId();

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $language_id => $banner_image) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int) $banner_id . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape(serialize($banner_image['image'])) . "',bannerHTML='" . $this->db->escape($banner_image['bannerHTML']) . "', language_id = '" . (int) $language_id . "'");

                $banner_image_id = $this->db->getLastId();

                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int) $banner_image_id . "', language_id = '" . (int) $language_id . "', banner_id = '" . (int) $banner_id . "', title = '" . $this->db->escape($banner_image['banner_image_description'][$language_id]['title']) . "'");
            }
        }
    }

    public function editBanner($banner_id, $data) {

        $this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int) $data['status'] . "',bannerType = '" . $this->db->escape($data['bannerType']) . "',bannerStyle = '" . $this->db->escape($data['bannerStyle']) . "' WHERE banner_id = '" . (int) $banner_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int) $banner_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int) $banner_id . "'");

        if (isset($data['banner_image'])) {
            foreach ($data['banner_image'] as $language_id => $banner_image) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int) $banner_id . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape(serialize($banner_image['image'])) . "',bannerHTML='" . $this->db->escape($banner_image['bannerHTML']) . "', language_id = '" . (int) $language_id . "'");

                $banner_image_id = $this->db->getLastId();

                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int) $banner_image_id . "', language_id = '" . (int) $language_id . "', banner_id = '" . (int) $banner_id . "', title = '" . $this->db->escape($banner_image['title']) . "'");
            }
        }
    }

    public function deleteBanner($banner_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int) $banner_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int) $banner_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int) $banner_id . "'");
    }

    public function getBanner($banner_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int) $banner_id . "'");

        return $query->row;
    }

    public function getBanners($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "banner";

        $sort_data = array(
            'name',
            'status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getBannerImages($banner_id) {
        $banner_image_data = array();

        $banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int) $banner_id . "'");

        foreach ($banner_image_query->rows as $banner_image) {

            $banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int) $banner_image['banner_image_id'] . "' AND banner_id = '" . (int) $banner_id . "'");

            foreach ($banner_image_description_query->rows as $banner_image_description) {

                $banner_image_data[$banner_image_description['language_id']] = array(
                    'title' => $banner_image_description['title'],
                    'link' => $banner_image['link'],
                    'bannerHTML' => $banner_image['bannerHTML'],
                    'language_id' => $banner_image['language_id'],
                    'image' => $banner_image['image']
                );
            }
        }

        return $banner_image_data;
    }

    public function getTotalBanners() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

        return $query->row['total'];
    }

    public function install() {
        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner` LIKE 'bannerType'");
        if (!$result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner` ADD `bannerType` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'image'");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner` LIKE 'bannerStyle'");
        if (!$result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner` ADD `bannerStyle` INT( 11 ) NULL DEFAULT  '0' ");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner_image` LIKE 'bannerHTML'");
        if (!$result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner_image` ADD `bannerHTML` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner_image` LIKE 'language_id'");
        if (!$result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner_image` ADD `language_id` INT( 11 ) NOT NULL");
        }
    }

    public function uninstall() {
        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner` LIKE 'bannerType'");
        if ($result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner` DROP `bannerType`");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner` LIKE 'bannerType'");
        if ($result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner` DROP `bannerStyle`");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner_image` LIKE 'bannerHTML'");
        if ($result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner_image` DROP `bannerHTML`");
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "`banner_image` LIKE 'bannerHTML'");
        if ($result->num_rows > 0) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "`banner_image` DROP `language_id`");
        }
    }

}

?>