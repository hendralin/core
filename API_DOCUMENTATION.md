# API Documentation - Broadcast Messages

## Base URL
```
http://your-domain.com/api
```

## Authentication

API ini menggunakan API Token (token-based authentication).

- Setiap user otomatis digenerate `api_token` saat register atau dibuat oleh admin.
- Sertakan header: `Authorization: Bearer {api_token}`
- Token hanya ditampilkan sekali setelah dibuat; simpan dengan aman.

## Endpoints

### 1. Send Text Message

Mengirim pesan teks ke nomor WhatsApp atau grup.

**Endpoint:** `POST /api/messages/sendText`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {api_token}
```

**Request Body:**
```json
{
    "session_id": "your_waha_session_id",
    "phone_number": "6281234567890",
    "message": "Hello, this is a test message",
    "scheduled_at": null
}
```

**Atau untuk mengirim ke grup:**
```json
{
    "session_id": "your_waha_session_id",
    "group_wa_id": "120363123456789012@g.us",
    "message": "Hello group, this is a test message",
    "scheduled_at": null
}
```

**Parameters:**
- `session_id` (required, string): `session_id` WAHA yang akan digunakan untuk mengirim pesan
- `phone_number` (required if group_wa_id not provided, string): Nomor WhatsApp tujuan (format: bisa dengan atau tanpa +, spasi, dll)
- `group_wa_id` (required if phone_number not provided, string): ID grup WhatsApp tujuan
- `message` (required, string, max: 1024): Isi pesan yang akan dikirim
- `scheduled_at` (optional, datetime): Waktu penjadwalan pengiriman (minimal 5 menit dari sekarang). Format: `Y-m-d H:i:s` atau ISO 8601

### 2. Send Image Message

Mengirim pesan gambar (URL) dengan caption.

**Endpoint:** `POST /api/messages/sendImage`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {api_token}
```

**Request Body:**
```json
{
    "session_id": "your_waha_session_id",
    "phone_number": "6281234567890",
    "image_url": "https://example.com/image.jpg",
    "caption": "Your caption here",
    "mimetype": "image/jpeg",
    "filename": "photo.jpg",
    "scheduled_at": null
}
```

**Atau untuk grup:**
```json
{
    "session_id": "your_waha_session_id",
    "group_wa_id": "120363123456789012@g.us",
    "image_url": "https://example.com/image.jpg",
    "caption": "Your caption here"
}
```

**Parameters:**
- `session_id` (required, string): `session_id` WAHA yang akan digunakan
- `phone_number` (required if group_wa_id not provided, string)
- `group_wa_id` (required if phone_number not provided, string)
- `image_url` (required, url): URL gambar yang dapat diakses publik
- `caption` (optional, string, max: 1024)
- `mimetype` (optional, string): contoh `image/jpeg`
- `filename` (optional, string): nama file yang akan tampil di WA
- `scheduled_at` (optional, datetime, min +5 menit)

**Response Success (201):**
```json
{
    "success": true,
    "message": "Image message has been queued for sending.",
    "data": {
        "id": 123,
        "status": "pending",
        "scheduled_at": null,
        "created_at": "2024-01-15T10:30:00+00:00"
    }
}
```

**Response Error (400/404/500):**
```json
{
    "success": false,
    "message": "Error message here"
}
```

### 3. Send File Message

Mengirim pesan file (URL) dengan caption.

**Endpoint:** `POST /api/messages/sendFile`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {api_token}
```

**Request Body:**
```json
{
    "session_id": "your_waha_session_id",
    "phone_number": "6281234567890",
    "file_url": "https://example.com/document.pdf",
    "caption": "Here is your document",
    "mimetype": "application/pdf",
    "filename": "document.pdf",
    "scheduled_at": null
}
```

**Atau untuk grup:**
```json
{
    "session_id": "your_waha_session_id",
    "group_wa_id": "120363123456789012@g.us",
    "file_url": "https://example.com/document.pdf",
    "caption": "Here is your document"
}
```

**Parameters:**
- `session_id` (required, string): `session_id` WAHA yang akan digunakan
- `phone_number` (required if group_wa_id not provided, string)
- `group_wa_id` (required if phone_number not provided, string)
- `file_url` (required, url): URL file yang dapat diakses publik
- `caption` (optional, string, max: 1024)
- `mimetype` (optional, string): contoh `application/pdf`
- `filename` (optional, string): nama file yang akan tampil di WA
- `scheduled_at` (optional, datetime, min +5 menit)

**Response Success (201):**
```json
{
    "success": true,
    "message": "File message has been queued for sending.",
    "data": {
        "id": 123,
        "status": "pending",
        "scheduled_at": null,
        "created_at": "2024-01-15T10:30:00+00:00"
    }
}
```

**Response Error (400/404/500):**
```json
{
    "success": false,
    "message": "Error message here"
}
```

### 4. Send Custom Link Message

Mengirim pesan teks dengan preview link kustom (link preview).

**Endpoint:** `POST /api/messages/link-custom-preview`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {api_token}
```

**Request Body:**
```json
{
    "session_id": "your_waha_session_id",
    "phone_number": "6281234567890",
    "text": "Check this out: https://example.com",
    "preview_url": "https://example.com",
    "preview_title": "Example Title",
    "preview_description": "Example description",
    "preview_image_url": "https://example.com/image.jpg",
    "scheduled_at": null
}
```

**Atau untuk grup:**
```json
{
    "session_id": "your_waha_session_id",
    "group_wa_id": "120363123456789012@g.us",
    "text": "Check this out: https://example.com",
    "preview_url": "https://example.com"
}
```

**Parameters:**
- `session_id` (required, string): `session_id` WAHA yang akan digunakan
- `phone_number` (required if group_wa_id not provided, string)
- `group_wa_id` (required if phone_number not provided, string)
- `text` (required, string, max: 1024)
- `preview_url` (required, url): URL yang akan dipakai untuk preview
- `preview_title` (optional, string, max: 255)
- `preview_description` (optional, string, max: 500)
- `preview_image_url` (optional, url)
- `scheduled_at` (optional, datetime, min +5 menit)

**Response Success (201):**
```json
{
    "success": true,
    "message": "Custom link message has been queued for sending.",
    "data": {
        "id": 123,
        "status": "pending",
        "scheduled_at": null,
        "created_at": "2024-01-15T10:30:00+00:00"
    }
}
```

**Response Error (400/404/500):**
```json
{
    "success": false,
    "message": "Error message here"
}
```

### 5. Send Template Message

Mengirim pesan menggunakan template dengan pengisian variabel.

**Endpoint:** `POST /api/messages/sendTemplate`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {api_token}
```

**Request Body:**
```json
{
    "session_id": "your_waha_session_id",
    "template_name": "welcome_template",
    "phone_number": "6281234567890",
    "placeholder_headers": [
        "Header 1"
    ],
    "placeholders": [
        "Body 1",
        "Body 2"
    ],
    "scheduled_at": null
}
```

**Atau untuk grup:**
```json
{
    "session_id": "your_waha_session_id",
    "template_name": "event_invitation",
    "group_wa_id": "120363123456789012@g.us",
    "placeholders": [
        "Webinar",
        "10:00"
    ]
}
```

**Parameters:**
- `session_id` (required, string): `session_id` WAHA yang akan digunakan
- `template_name` (required, string): nama template yang dimiliki user
- `phone_number` (required if group_wa_id not provided, string)
- `group_wa_id` (required if phone_number not provided, string)
- `placeholder_headers` (optional, array): nilai berurutan untuk placeholder di header template
- `placeholders` (optional, array): nilai berurutan untuk placeholder di body template
- `scheduled_at` (optional, datetime, min +5 menit)

**Response Success (201):**
```json
{
    "success": true,
    "message": "Template message has been queued for sending.",
    "data": {
        "id": 123,
        "status": "pending",
        "scheduled_at": null,
        "created_at": "2024-01-15T10:30:00+00:00"
    }
}
```

**Response Error (400/404/500):**
```json
{
    "success": false,
    "message": "Error message here"
}
```

**Response Success (201):**
```json
{
    "success": true,
    "message": "Message has been queued for sending.",
    "data": {
        "id": 123,
        "status": "pending",
        "scheduled_at": null,
        "created_at": "2024-01-15T10:30:00+00:00"
    }
}
```

**Response Error (400/404/500):**
```json
{
    "success": false,
    "message": "Error message here"
}
```

**Contoh Request dengan cURL:**
```bash
curl -X POST http://your-domain.com/api/messages/sendText \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -d '{
    "session_id": 1,
    "phone_number": "6281234567890",
    "message": "Hello, this is a test message"
  }'
```

**Contoh Request dengan PHP (Guzzle):**
```php
$client = new \GuzzleHttp\Client();
$response = $client->post('http://your-domain.com/api/messages/sendText', [
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer YOUR_API_TOKEN'
    ],
    'json' => [
        'session_id' => 1,
        'phone_number' => '6281234567890',
        'message' => 'Hello, this is a test message'
    ]
]);
```

**Contoh Request dengan JavaScript (Fetch):**
```javascript
fetch('http://your-domain.com/api/messages/sendText', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    credentials: 'include', // Include cookies
    body: JSON.stringify({
        session_id: 1,
        phone_number: '6281234567890',
        message: 'Hello, this is a test message'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Status Codes

- `201` - Message successfully queued
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (not authenticated)
- `403` - Forbidden (no access to session)
- `404` - Session not found
- `500` - Internal Server Error

## Notes

1. **Message Status:** Pesan akan dibuat dengan status `pending` dan akan diproses secara asynchronous melalui queue system.
2. **Scheduled Messages:** Jika `scheduled_at` disediakan, pesan akan dikirim pada waktu yang ditentukan (minimal 5 menit dari sekarang).
3. **Session Access:** User hanya bisa menggunakan session yang mereka buat sendiri (`created_by` = user_id).
4. **Phone Number Format:** Nomor telepon akan otomatis dibersihkan dan diformat ke format WAHA (`number@s.whatsapp.net`).
5. **Group Messages:** Untuk mengirim ke grup, gunakan `group_wa_id` dan jangan sertakan `phone_number`.

## Next Steps

Endpoint berikutnya yang akan ditambahkan:
- Send Image Message
- Send File Message
- Send Template Message
- Get Message Status
- List Messages

