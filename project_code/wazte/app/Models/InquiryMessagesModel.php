<?php
namespace App\Models;

use CodeIgniter\Model;

class InquiryMessagesModel extends Model
{
    protected $table = 'inquirymessages';
    protected $primaryKey = 'message_ID';
    protected $allowedFields = ['inquiry_ID', 'sender_ID', 'message', 'timestamp'];
    protected $useTimestamps = false;
}
