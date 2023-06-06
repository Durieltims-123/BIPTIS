<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Auth;
use App\ProjectDocument;
use App\DocumentType;
use App\ProjectPlans;
use App\Contractors;
class SendNotification implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $data = ['asas'];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user-channel');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'UserEvent';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
        // $projectdocument = ProjectDocument::select('document_types.document_type', 'project_plans.project_title', 'contractors.business_name', 'sender.name as sender_name', 'sender.id as sender_id', 'receiver.id as receiver_id', 'receiver.name as receiver_name')
        $projectdocument = ProjectDocument::select('document_types.document_type', 'project_plans.project_title', 'sender.name as sender_name', 'sender.id as sender_id', 'receiver.id as receiver_id', 'receiver.name as receiver_name')
        ->distinct()
            ->join('project_plans', function($join){
                $join->on('project_documents.plan_id', '=', 'project_plans.plan_id');
            })
            // ->join('contractors', function($join){
            //     $join->on('project_documents.contractor_id', '=', 'contractors.contractor_id');
            // })
            ->join('document_types', function($join){
                $join->on('project_documents.document_type_id', '=', 'document_types.id');
            })
            ->join('users as sender', function($join){
                $join->on('project_documents.sender', '=', 'sender.id');
            })
            ->join('users as receiver', function($join){
                $join->on('project_documents.receiver', '=', 'receiver.id');
            });   
        $projectdocument = $projectdocument->where('project_documents.id', '=', $this->document)->first();
        return [
            'title' => 'Document Sent!',
            'content' => '<b>'.$projectdocument->document_type.'</b> has been sent to '.$projectdocument->receiver_name,
            'receiver_id' => $projectdocument->receiver_id,
            'sender_id' => $projectdocument->sender_id,
            'document_type' => $projectdocument->document_type,
            'sender_name' => $projectdocument->sender_name,
            'receiver_name' => $projectdocument->receiver_name
        ];
            
    }
}
