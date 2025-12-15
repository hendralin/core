<?php

namespace App\Livewire\Broadcast\Messages;

use App\Models\Group;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Session;
use Livewire\Component;
use App\Models\Template;
use Livewire\WithPagination;
use App\Services\WahaService;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Title('Broadcast Messages')]
class MesssagesIndex extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedSession' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // Form properties
    public $messageType = 'direct'; // 'direct' or 'template'
    public $selectedTemplate = '';
    public $directMessage = '';
    public $recipientType = 'contact'; // 'contact' or 'group'
    public $selectedContactId = '';
    public $contactNumber = ''; // Keep for backward compatibility
    public $selectedGroups = [];
    public $selectedRecipients = [];
    public $recipientsFile;
    public $parsedRecipients = [];
    public $selectedSession = ''; // For filtering messages list
    public $messageSession = ''; // For sending messages in modal
    public $templateParams = [
        'header' => [],
        'body' => []
    ];

    // List properties
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal properties

    // Data properties
    public $templates = [];
    public $sessions = [];
    public $groups = [];
    public $contacts = [];
    public $wahaStatus = [
        'connected' => false,
        'message' => 'Checking connection...',
    ];

    // Using custom validation in sendMessage() method instead of global rules
    // to avoid conflicts with conditional validation

    protected $messages = [
        'messageSession.required' => 'Please select a WAHA session.',
        'directMessage.required' => 'Message content is required when using direct message.',
        'selectedTemplate.required_if' => 'Please select a template when using template message.',
        'selectedTemplate.required' => 'Please select a template.',
        'selectedTemplate.exists' => 'The selected template is invalid.',
        'selectedContactId.required' => 'Please select a contact.',
        'selectedContactId.exists' => 'The selected contact is invalid.',
        'contactNumber.regex' => 'Please enter a valid phone number.',
        'selectedGroups.required_if' => 'Please select at least one group when sending to groups.',
        'selectedGroups.required' => 'Please select at least one group.',
        'selectedGroups.exists' => 'The selected groups are invalid.',
        'recipientsFile' => 'Invalid file format. Please upload Excel (.xlsx, .xls) or CSV file.',
        'recipientsFile.mimes' => 'File must be Excel (.xlsx, .xls) or CSV format.',
        'recipientsFile.max' => 'File size must not exceed 10MB.',
        // 'templateParams.header.*.required' => 'Header parameter ":attribute" is required.',
        // 'templateParams.body.*.required' => 'Body parameter ":attribute" is required.',
        // 'templateParams.header.*.string' => 'Header parameter ":attribute" must be text.',
        // 'templateParams.body.*.string' => 'Body parameter ":attribute" must be text.',
        // 'templateParams.header.*.max' => 'Header parameter ":attribute" must not exceed :max characters.',
        // 'templateParams.body.*.max' => 'Body parameter ":attribute" must not exceed :max characters.',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->templates = Template::where('is_active', true)
            ->with('wahaSession')
            ->orderBy('name')
            ->get();

        $this->sessions = Session::all();

        // Check WAHA connection status
        $this->checkWahaStatus();

        // Filter groups and contacts by message session if one is chosen (for modal)
        // This allows filtering based on the session selected in the send message modal
        if ($this->messageSession) {
            $this->groups = Group::where('waha_session_id', $this->messageSession)
                ->with('wahaSession')
                ->get();
            $this->contacts = Contact::where('waha_session_id', $this->messageSession)
                ->with('wahaSession')
                ->orderBy('name')
                ->get();
        } else {
            $this->groups = Group::with('wahaSession')->get();
            $this->contacts = Contact::with('wahaSession')
                ->orderBy('name')
                ->get();
        }
    }

    private function checkWahaStatus()
    {
        try {
            $wahaService = new WahaService();
            $wahaService->checkConnection();

            if ($wahaService->isConnected) {
                $this->wahaStatus = [
                    'connected' => true,
                    'message' => 'WAHA API Connected',
                ];
            } else {
                $this->wahaStatus = [
                    'connected' => false,
                    'message' => 'WAHA API Disconnected - Check configuration and network',
                ];
            }
        } catch (\Exception $e) {
            $this->wahaStatus = [
                'connected' => false,
                'message' => 'WAHA API Error: ' . $e->getMessage(),
            ];
        }
    }

    public function updatedMessageType()
    {
        $this->selectedTemplate = '';
        $this->directMessage = '';
        $this->templateParams = [
            'header' => [],
            'body' => []
        ];

        $this->resetValidation();
    }

    public function updatedSelectedTemplate()
    {
        $this->templateParams = [
            'header' => [],
            'body' => []
        ];
        $this->loadTemplateParams();
        $this->resetValidation();
    }

    public function updatedRecipientType()
    {
        if ($this->recipientType === 'contact') {
            // Reset group and recipients selection when switching to contact
            $this->selectedGroups = [];
            $this->selectedRecipients = [];
            $this->recipientsFile = null;
            $this->parsedRecipients = [];
        } elseif ($this->recipientType === 'group') {
            // Reset contact and recipients selection when switching to group
            $this->selectedContactId = '';
            $this->contactNumber = '';
            $this->selectedRecipients = [];
            $this->recipientsFile = null;
            $this->parsedRecipients = [];
        } elseif ($this->recipientType === 'recipients') {
            // Reset contact and group selection when switching to recipients
            $this->selectedContactId = '';
            $this->contactNumber = '';
            $this->selectedGroups = [];
        }

        $this->resetValidation();
    }

    public function updatedSelectedSession()
    {
        $this->loadData();
    }

    public function updatedMessageSession()
    {
        $this->loadData(); // Reload groups when message session changes
    }

    public function loadTemplateParams()
    {
        if ($this->selectedTemplate && $this->messageType === 'template') {
            $template = Template::find($this->selectedTemplate);
            if ($template) {
                // Reset template params
                $this->templateParams = [
                    'header' => [],
                    'body' => []
                ];

                // Extract parameters from header
                if ($template->header) {
                    preg_match_all('/\{\{(\w+)\}\}/', $template->header, $headerMatches);
                    $headerParams = array_unique($headerMatches[1]);
                    $this->templateParams['header'] = array_fill_keys($headerParams, '');
                }

                // Extract parameters from body
                preg_match_all('/\{\{(\w+)\}\}/', $template->body, $bodyMatches);
                $bodyParams = array_unique($bodyMatches[1]);
                $this->templateParams['body'] = array_fill_keys($bodyParams, '');
            }
        }
    }

    public function openSendModal()
    {
        $this->resetForm();
        $this->loadData(); // Load groups based on current messageSession
        $this->modal('send-message-modal')->show();
    }

    public function closeSendModal()
    {
        $this->modal('send-message-modal')->close();
        $this->resetForm();
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($this->messageType === 'direct') {
            // Direct message template
            $sheet->setCellValue('A1', 'Phone Number');
            $sheet->setCellValue('B1', 'Message');

            // Set headers
            $sheet->getStyle('A1:B1')->getFont()->setBold(true);
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(50);

            // Sample data
            $sheet->setCellValue('A2', '6281234567890');
            $sheet->setCellValue('B2', 'Hello! This is a test message.');

            $filename = 'direct_message_template.xlsx';
        } else {
            // Template message
            $template = Template::find($this->selectedTemplate);
            $col = 'A';
            $headers = ['Phone Number'];

            // Add header variables if exist
            if ($template && !empty($this->templateParams['header'])) {
                foreach ($this->templateParams['header'] as $param => $value) {
                    $headers[] = "Header: {$param}";
                }
            }

            // Add body variables if exist
            if ($template && !empty($this->templateParams['body'])) {
                foreach ($this->templateParams['body'] as $param => $value) {
                    $headers[] = "Body: {$param}";
                }
            }

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
                $sheet->getColumnDimension(chr(65 + $index))->setWidth(20);
            }
            $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')->getFont()->setBold(true);

            // Sample data
            $row = 2;
            $sheet->setCellValue('A' . $row, '6281234567890');

            $colIndex = 1;
            if ($template && !empty($this->templateParams['header'])) {
                foreach ($this->templateParams['header'] as $param => $value) {
                    $sheet->setCellValue(chr(65 + $colIndex) . $row, "Value for {$param}");
                    $colIndex++;
                }
            }

            if ($template && !empty($this->templateParams['body'])) {
                foreach ($this->templateParams['body'] as $param => $value) {
                    $sheet->setCellValue(chr(65 + $colIndex) . $row, "Value for {$param}");
                    $colIndex++;
                }
            }

            $filename = 'template_message_template.xlsx';
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'template');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }

    public function updatedRecipientsFile()
    {
        $this->parsedRecipients = [];

        if ($this->recipientsFile) {
            try {
                // Load the Excel/CSV file
                $data = Excel::toArray([], $this->recipientsFile->getRealPath())[0];

                if (empty($data)) {
                    $this->addError('recipientsFile', 'File appears to be empty or invalid.');
                    return;
                }

                // Remove header row
                array_shift($data);

                if ($this->messageType === 'direct') {
                    // Direct message format: Phone Number | Message
                    foreach ($data as $row) {
                        if (!empty($row[0]) && !empty($row[1])) {
                            $this->parsedRecipients[] = [
                                'phone' => trim($row[0]),
                                'message' => trim($row[1])
                            ];
                        }
                    }
                } elseif ($this->messageType === 'template') {
                    // Template message format: Phone Number | Header Var 1 | Header Var 2 | Body Var 1 | Body Var 2 | ...
                    $template = Template::find($this->selectedTemplate);
                    if (!$template) {
                        $this->addError('recipientsFile', 'Template not found. Please select a valid template first.');
                        return;
                    }

                    foreach ($data as $row) {
                        if (empty($row[0])) continue; // Skip empty phone numbers

                        $recipient = [
                            'phone' => trim($row[0])
                        ];

                        // Parse header variables
                        $headerVars = [];
                        $bodyVars = [];
                        $colIndex = 1;

                        // Header variables
                        if (!empty($this->templateParams['header'])) {
                            foreach ($this->templateParams['header'] as $param => $value) {
                                if (isset($row[$colIndex])) {
                                    $headerVars[$param] = trim($row[$colIndex]);
                                }
                                $colIndex++;
                            }
                        }

                        // Body variables
                        if (!empty($this->templateParams['body'])) {
                            foreach ($this->templateParams['body'] as $param => $value) {
                                if (isset($row[$colIndex])) {
                                    $bodyVars[$param] = trim($row[$colIndex]);
                                }
                                $colIndex++;
                            }
                        }

                        $recipient['header_vars'] = $headerVars;
                        $recipient['body_vars'] = $bodyVars;

                        $this->parsedRecipients[] = $recipient;
                    }
                }

                if (empty($this->parsedRecipients)) {
                    $this->addError('recipientsFile', 'No valid recipients found in the file. Please check the format.');
                }

            } catch (\Exception $e) {
                $this->addError('recipientsFile', 'Failed to parse file: ' . $e->getMessage());
            }
        }
    }

    public function resetForm()
    {
        $this->messageType = 'direct';
        $this->selectedTemplate = '';
        $this->directMessage = '';
        $this->recipientType = 'contact';
        $this->contactNumber = '';
        $this->messageSession = '';
        $this->selectedTemplate = '';
        $this->selectedContactId = '';
        $this->contactNumber = '';
        $this->selectedGroups = [];
        $this->selectedRecipients = [];
        $this->recipientsFile = null;
        $this->parsedRecipients = [];
        $this->templateParams = [
            'header' => [],
            'body' => []
        ];
        $this->resetValidation();
    }

    public function sendMessage()
    {
        // Custom validation based on recipient type
        $rules = [
            'messageSession' => 'required',
        ];

        if ($this->messageType === 'direct') {
            // Only require directMessage if not using bulk recipients (recipients get messages from file)
            if ($this->recipientType !== 'recipients') {
                $rules['directMessage'] = 'required';
            }
        } elseif ($this->messageType === 'template') {
            $rules['selectedTemplate'] = 'required|exists:templates,id';

            // Validate template parameters (only when not using bulk recipients)
            if ($this->recipientType !== 'recipients' && $this->selectedTemplate) {
                $template = Template::find($this->selectedTemplate);
                if ($template) {
                    // Validate header parameters
                    if (!empty($this->templateParams['header'])) {
                        foreach ($this->templateParams['header'] as $param => $value) {
                            $rules["templateParams.header.{$param}"] = 'required|string|max:255';

                            $messages["templateParams.header.{$param}.required"] = 'Header parameter "'. $param .'" is required.';
                            $messages["templateParams.header.{$param}.string"] = 'Header parameter "'. $param .'" must be text.';
                            $messages["templateParams.header.{$param}.max"] = 'Header parameter "'. $param .'" must not exceed 255 characters.';
                        }
                    }

                    // Validate body parameters
                    if (!empty($this->templateParams['body'])) {
                        foreach ($this->templateParams['body'] as $param => $value) {
                            $rules["templateParams.body.{$param}"] = 'required|string|max:1000';

                            $messages["templateParams.body.{$param}.required"] = 'Body parameter "'. $param .'" is required.';
                            $messages["templateParams.body.{$param}.string"] = 'Body parameter "'. $param .'" must be text.';
                            $messages["templateParams.body.{$param}.max"] = 'Body parameter "'. $param .'" must not exceed 1000 characters.';
                        }
                    }
                }
            }
        }

        // Validate session exists and is valid
        $rules['messageSession'] = 'required|exists:waha_sessions,id';

        if ($this->recipientType === 'contact') {
            $rules['selectedContactId'] = 'required|exists:contacts,id';
        } elseif ($this->recipientType === 'group') {
            $rules['selectedGroups'] = 'required|array|min:1|exists:groups,id';
        } elseif ($this->recipientType === 'recipients') {
            $rules['recipientsFile'] = 'required|file|mimes:xlsx,xls,csv|max:10240';
        }

        $this->validate($rules, $messages ?? []);

        try {
            DB::beginTransaction();

            $messageContent = $this->buildMessageContent();
            $recipients = $this->buildRecipients();

            // Get the WAHA session name from database
            $sessionRecord = Session::find($this->messageSession);
            $wahaSessionName = $sessionRecord ? $sessionRecord->session_id : null;

            if (!$wahaSessionName) {
                throw new \Exception('Invalid session selected. Please select a valid WAHA session.');
            }

            $wahaService = new WahaService();
            $sendResults = [];

            foreach ($recipients as $recipient) {
                try {
                    // Build custom message content for this recipient
                    $recipientMessageContent = $this->buildMessageContentForRecipient($recipient);

                    // Determine the correct chat ID for WAHA API
                    $chatId = isset($recipient['group_wa_id']) ? $recipient['group_wa_id'] : $recipient['wa_id'];

                    // Send message via WAHA API first
                    $sendResult = $wahaService->sendText(
                        $chatId,
                        $recipientMessageContent,
                        $wahaSessionName // Pass the correct WAHA session name
                    );

                    // Create message record only if WAHA send was successful
                    $message = Message::create([
                        'waha_session_id' => $this->messageSession,
                        'template_id' => $this->messageType === 'template' ? $this->selectedTemplate : null,
                        'wa_id' => $recipient['wa_id'] ?? null,
                        'group_wa_id' => $recipient['group_wa_id'] ?? null,
                        'received_number' => $recipient['number'] ?? null,
                        'message' => $recipientMessageContent,
                        'created_by' => Auth::id(),
                    ]);

                    // Log activity for successful message sending
                    activity()
                        ->performedOn($message)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'attributes' => [
                                'recipient' => $chatId,
                                'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                                'message_type' => $this->messageType,
                                'session_id' => $this->messageSession,
                                'template_id' => $this->selectedTemplate,
                            ],
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ])
                        ->log('sent a message');

                    $sendResults[] = [
                        'recipient' => $chatId,
                        'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                        'success' => true,
                        'message_id' => $message->id,
                        'waha_result' => $sendResult,
                    ];

                    Log::info('Message sent successfully via WAHA', [
                        'message_id' => $message->id,
                        'recipient' => $chatId,
                        'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                        'waha_result' => $sendResult,
                    ]);

                } catch (\Exception $e) {
                    // Log activity for failed message sending
                    activity()
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'attributes' => [
                                'recipient' => $chatId,
                                'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                                'message_type' => $this->messageType,
                                'session_id' => $this->messageSession,
                                'error' => $e->getMessage(),
                            ],
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ])
                        ->log('failed to send message');

                    // Log the error but continue with other recipients if bulk
                    $sendResults[] = [
                        'recipient' => $chatId,
                        'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Failed to send message via WAHA', [
                        'recipient' => $chatId,
                        'recipient_type' => isset($recipient['group_wa_id']) ? 'group' : 'contact',
                        'error' => $e->getMessage(),
                    ]);

                    // For single recipient, re-throw the exception
                    if (count($recipients) === 1) {
                        throw $e;
                    }
                }
            }

            // Update template usage count if using template
            if ($this->messageType === 'template' && $this->selectedTemplate) {
                $template = Template::find($this->selectedTemplate);
                if ($template) {
                    $template->incrementUsageCount();
                    $template->update(['last_used_at' => now()]);
                }
            }

            DB::commit();

            // Calculate success/failure stats
            $totalSent = count($sendResults);
            $successful = count(array_filter($sendResults, fn($r) => $r['success']));
            $failed = $totalSent - $successful;

            if ($failed === 0) {
                session()->flash('success', "All {$totalSent} message(s) sent successfully!");
            } elseif ($successful === 0) {
                session()->flash('error', "Failed to send all {$totalSent} message(s). Check logs for details.");
            } else {
                session()->flash('success', "{$successful} of {$totalSent} message(s) sent successfully. {$failed} failed.");
            }

            $this->closeSendModal();
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    private function buildMessageContent()
    {
        if ($this->messageType === 'template') {
            if (!$this->selectedTemplate) {
                return 'Please select a template first';
            }

            $template = Template::find($this->selectedTemplate);
            if (!$template) {
                return 'Template not found';
            }

            $content = '';

            // Add header if exists
            if ($template->header) {
                $header = $template->header;
                // Replace template parameters in header
                foreach ($this->templateParams['header'] as $param => $value) {
                    $header = str_replace("{{" . $param . "}}", $value, $header);
                }
                $content .= $header . "\n\n";
            }

            // Add body
            $body = $template->body;
            // Replace template parameters in body
            foreach ($this->templateParams['body'] as $param => $value) {
                $body = str_replace("{{" . $param . "}}", $value, $body);
            }
            $content .= $body;

            return trim($content);
        }

        return $this->directMessage ?: 'Please enter a message';
    }

    private function buildMessageContentForRecipient($recipient)
    {
        if ($this->recipientType === 'recipients') {
            // For bulk recipients, use custom parameters from file
            if (isset($recipient['custom_params'])) {
                $params = $recipient['custom_params'];

                if ($this->messageType === 'direct') {
                    // Use direct message from file
                    return $params['direct_message'] ?? $this->buildMessageContent();
                } elseif ($this->messageType === 'template') {
                    // Use template with custom parameters from file
                    $template = Template::find($this->selectedTemplate);
                    if (!$template) {
                        return $this->buildMessageContent();
                    }

                    $content = '';

                    // Add header if exists
                    if ($template->header) {
                        $header = $template->header;
                        // Replace with custom header parameters
                        if (!empty($params['header_vars'])) {
                            foreach ($params['header_vars'] as $param => $value) {
                                $header = str_replace("{{" . $param . "}}", $value, $header);
                            }
                        }
                        $content .= $header . "\n\n";
                    }

                    // Add body
                    $body = $template->body;
                    // Replace with custom body parameters
                    if (!empty($params['body_vars'])) {
                        foreach ($params['body_vars'] as $param => $value) {
                            $body = str_replace("{{" . $param . "}}", $value, $body);
                        }
                    }
                    $content .= $body;

                    return trim($content);
                }
            }
        }

        // For other recipient types, use standard message content
        return $this->buildMessageContent();
    }

    private function buildRecipients()
    {
        $recipients = [];

        if ($this->recipientType === 'contact') {
            if ($this->selectedContactId) {
                $contact = Contact::find($this->selectedContactId);
                if ($contact) {
                    // Clean wa_id format for WAHA (remove any existing suffix and add proper one)
                    $cleanNumber = preg_replace('/@.+$/', '', $contact->wa_id);
                    $recipients[] = [
                        'number' => $contact->wa_id,
                        'wa_id' => $cleanNumber . '@s.whatsapp.net', // WAHA format
                    ];
                }
            } elseif ($this->contactNumber) {
                // Fallback for backward compatibility
                // Clean contact number format (remove any non-numeric characters except +)
                $cleanNumber = preg_replace('/[^\d+]/', '', $this->contactNumber);
                $recipients[] = [
                    'number' => $this->contactNumber,
                    'wa_id' => $cleanNumber . '@s.whatsapp.net', // WAHA format
                ];
            }
        } elseif ($this->recipientType === 'group') {
            foreach ($this->selectedGroups as $groupId) {
                $group = Group::find($groupId);
                if ($group) {
                    $recipients[] = [
                        'group_wa_id' => $group->group_wa_id,
                    ];
                }
            }
        } elseif ($this->recipientType === 'recipients') {
            // Handle bulk recipients from uploaded file
            foreach ($this->parsedRecipients as $recipient) {
                if (isset($recipient['phone'])) {
                    $cleanNumber = preg_replace('/[^\d+]/', '', $recipient['phone']);
                    $recipients[] = [
                        'number' => $recipient['phone'] . '@c.us',
                        'wa_id' => $cleanNumber . '@s.whatsapp.net',
                        'custom_params' => [
                            'header_vars' => $recipient['header_vars'] ?? [],
                            'body_vars' => $recipient['body_vars'] ?? [],
                            'direct_message' => $recipient['message'] ?? null
                        ]
                    ];
                }
            }
        }

        return $recipients;
    }

    private function buildRecipientInfo()
    {
        if ($this->recipientType === 'contact') {
            if ($this->selectedContactId) {
                $contact = Contact::find($this->selectedContactId);
                return $contact ? "Contact: " . ($contact->name ?: $contact->wa_id) : "Contact not found";
            } elseif ($this->contactNumber) {
                return "Contact: " . $this->contactNumber;
            }
            return "Please select a contact";
        } elseif ($this->recipientType === 'group') {
            if (empty($this->selectedGroups)) {
                return "Please select at least one group";
            }

            $groupNames = [];
            foreach ($this->selectedGroups as $groupId) {
                $group = Group::find($groupId);
                if ($group) {
                    $groupNames[] = $group->name;
                }
            }
            return "Groups: " . implode(', ', $groupNames);
        } elseif ($this->recipientType === 'recipients') {
            if (empty($this->parsedRecipients)) {
                return "Please upload a valid recipients file";
            }
            return count($this->parsedRecipients) . " recipients from uploaded file";
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedSession = '';
        $this->resetPage();
    }

    public function render()
    {
        $messages = Message::with(['template', 'wahaSession', 'createdBy', 'contact'])
            ->when($this->search, function ($query) {
                $query->where('message', 'like', '%' . $this->search . '%')
                      ->orWhere('received_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedSession, function ($query) {
                $query->where('waha_session_id', $this->selectedSession);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.broadcast.messages.messsages-index', [
            'messages' => $messages,
        ]);
    }
}
