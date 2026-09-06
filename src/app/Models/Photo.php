<?php

declare(strict_types=1);

class Photo
{
    private const STORAGE_PATH = __DIR__ . '/../../uploads';

    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, original_path, thumbnail_path, original_name, title, alt_text, is_default, width, height
             FROM photo_assets ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM photo_assets WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() !== false;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, original_path, thumbnail_path, original_name, title, alt_text, is_default, mime_type, width, height
             FROM photo_assets WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $photo = $stmt->fetch();

        return $photo === false ? null : $photo;
    }

    public function update(int $id, string $name, string $title, string $altText): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE photo_assets SET original_name = :original_name, title = :title, alt_text = :alt_text
             WHERE id = :id'
        );
        $stmt->execute([
            'original_name' => $name,
            'title' => $title,
            'alt_text' => $altText,
            'id' => $id,
        ]);

        return $stmt->rowCount() === 1;
    }

    public function setDefault(int $id): void
    {
        $this->db->beginTransaction();
        $this->db->exec('UPDATE photo_assets SET is_default = FALSE');
        $stmt = $this->db->prepare('UPDATE photo_assets SET is_default = TRUE WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->db->commit();
    }

    public function store(array $file, string $name, string $title, string $altText): int
    {
        $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($uploadError !== UPLOAD_ERR_OK) {
            $message = match ($uploadError) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The image is too large. Maximum upload size is 32 MB.',
                UPLOAD_ERR_PARTIAL => 'The image upload was interrupted. Please try again.',
                UPLOAD_ERR_NO_FILE => 'Please choose an image to upload.',
                default => 'The image upload failed. Please try again.',
            };
            throw new RuntimeException($message);
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            throw new RuntimeException('Please upload a JPEG, PNG, GIF, or WebP image.');
        }

        $source = $this->createImage($file['tmp_name'], $imageInfo['mime']);
        $extension = match ($imageInfo['mime']) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        };
        $filename = bin2hex(random_bytes(16));
        $originalPath = self::STORAGE_PATH . '/' . $filename . '.' . $extension;
        $thumbnailPath = self::STORAGE_PATH . '/' . $filename . '-thumb.jpg';
        $this->ensureStorage();

        if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
            imagedestroy($source);
            throw new RuntimeException('The photo could not be stored.');
        }

        $thumbnail = $this->thumbnail($source, $imageInfo[0], $imageInfo[1]);
        if (!imagejpeg($thumbnail, $thumbnailPath, 88)) {
            imagedestroy($source);
            imagedestroy($thumbnail);
            @unlink($originalPath);
            throw new RuntimeException('The photo thumbnail could not be created.');
        }
        imagedestroy($source);
        imagedestroy($thumbnail);

        $stmt = $this->db->prepare(
            'INSERT INTO photo_assets (original_path, thumbnail_path, original_name, title, alt_text, is_default, mime_type, width, height)
             VALUES (:original_path, :thumbnail_path, :original_name, :title, :alt_text, FALSE, :mime_type, :width, :height)'
        );
        $stmt->execute([
            'original_path' => '/uploads/' . basename($originalPath),
            'thumbnail_path' => '/uploads/' . basename($thumbnailPath),
            'original_name' => $name,
            'title' => $title,
            'alt_text' => $altText,
            'mime_type' => $imageInfo['mime'],
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function ensureStorage(): void
    {
        if (!is_dir(self::STORAGE_PATH) && !mkdir(self::STORAGE_PATH, 0755, true) && !is_dir(self::STORAGE_PATH)) {
            throw new RuntimeException('The photo storage directory could not be created.');
        }
    }

    private function createImage(string $path, string $mime): GdImage
    {
        return match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
        };
    }

    private function thumbnail(GdImage $source, int $width, int $height): GdImage
    {
        $scale = min(1, 800 / $width);
        $thumbnailWidth = max(1, (int) round($width * $scale));
        $thumbnailHeight = max(1, (int) round($height * $scale));
        $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

        return $thumbnail;
    }
}