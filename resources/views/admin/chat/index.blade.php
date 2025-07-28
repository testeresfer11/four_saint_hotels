@extends('admin.layouts.app')
@section('title', 'Real-Time Chat')

@section('styles')
<style>
  .cursor-pointer { cursor: pointer; }
  .hover-bg-light:hover { background-color: #f8f9fa; }

  .chat-message {
    max-width: 75%;
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 10px;
    display: inline-block;
    clear: both;
  }

  .chat-left {
    background-color: #f1f1f1;
    float: left;
    border-top-left-radius: 0;
  }

  .chat-right {
    background-color: #B46326;
    color: white;
    float: right;
    border-top-right-radius: 0;
    text-align: right;
  }

  .hede {
    background-color: #b46326 !important;
  }

  .text-mutedd {
    color: #2c2e33 !important;
  }

  #attachment-btn {
    font-size: 18px;
    padding: 6px 10px;
  }
</style>
@endsection

@section('content')
<div class="row">
  <!-- Conversation List -->
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-header text-white hede">
        <strong>Conversations</strong>
      </div>
      <div class="card-body p-0" id="conversation-list" style="max-height: 500px; overflow-y: auto;">
        @forelse($conversations as $conversation)
         @php
        $user = $conversation->userTwo;
        $fullName = $user->full_name ?? 'Unknown User';
        $profileImage = $user->user_detail?->profile 
            ? asset('storage/' . $user->user_detail->profile)
            : asset('images/default_profile.jpeg');

        $latestMessage = $conversation->latest_message;
        $messageText = $latestMessage?->message ?? 'No messages yet';
        $messageType = $latestMessage?->type ?? 'text';
        $messageDate = $latestMessage 
            ? \Carbon\Carbon::parse($latestMessage->created_at)->diffForHumans() 
            : '';
    @endphp

          <div class="conversation-item p-3 border-bottom d-flex align-items-start gap-3 cursor-pointer hover-bg-light"
              data-id="{{ $conversation->id }}" 
              data-name="{{ $fullName }}"
              data-receiver-id="{{ $user->id }}">

              <img src="{{ $profileImage }}" alt="User" class="rounded-circle" width="45" height="45">

              <div class="flex-grow-1">
                  <div class="fw-semibold d-flex justify-content-between">
                      <span>{{ $fullName }}</span>
                      @if($conversation->unread_count > 0)
                          <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                      @endif
                  </div>
                  <div class="text-muted small text-truncate">
                    @if($messageType === 'file')
                        @php
                            $fileExtension = pathinfo($messageText, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ $messageText }}" alt="Image" width="40" height="40" class="rounded">
                        @else
                            📎 <a href="{{ $messageText }}" target="_blank">File Attachment</a>
                        @endif
                    @else
                        {{ \Illuminate\Support\Str::limit($messageText, 50) }}
                    @endif
                </div>

                  <div class="text-muted small">{{ $messageDate }}</div>
              </div>
          </div>
        @empty
          <p class="text-center text-muted p-3">No conversations yet.</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Chat Box -->
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-header hede text-white">
        <strong id="conversation-title">Chat</strong>
      </div>
      <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto;">
        <div class="d-flex justify-content-center align-items-center h-100 text-muted" id="empty-chat-msg">
          <p class="text-center">Start a conversation by clicking on a contact from the left.</p>
        </div>
      </div>
      <div class="card-footer">
        <form id="message-form" class="d-flex align-items-center gap-2 mt-2">
            <input type="hidden" id="sender_id" value="{{ auth()->id() }}">
            <input type="hidden" id="receiver_id">
            <input type="hidden" id="conversation_id">

            <input type="file" id="attachment-input" accept="image/*,video/*,application/pdf" style="display: none;">

            <button type="button" id="attach-btn" class="btn btn-outline-secondary">
                📎
            </button>

            <input type="text" id="message-input" class="form-control" placeholder="Type a message..." required>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
moment.relativeTimeThreshold('s', 60);
const socket = io("https://bm-chat-socket.vipankumar.in");

$(document).ready(function () {
    let currentConversationId = null;

    $('.conversation-item').on('click', function () {
        $('.conversation-item').removeClass('bg-light');
        $(this).find('.rounded-pill').hide();
        $(this).addClass('bg-light');

        const conversationId = $(this).data('id');
        const name = $(this).data('name');
        const receiverId = $(this).data('receiver-id');

        $('#conversation_id').val(conversationId);
        $('#receiver_id').val(receiverId);
        $('#conversation-title').text(name);
        currentConversationId = conversationId;

        $('#message-form').removeClass('d-none');
        $('#empty-chat-msg').remove();

        socket.emit('joinRoom', `conversation_${conversationId}`);

        $.get(`/admin/chat/conversation/${conversationId}/messages`, function (res) {
            let messages = '';
            res.data.forEach(msg => {
                const isMe = msg.sender_id == $('#sender_id').val();
                const messageClass = isMe ? 'chat-message chat-right' : 'chat-message chat-left';
                const timeAgo = moment(msg.created_at).fromNow();
                messages += `
                    <div class="${messageClass}">
                        ${renderMessageContent(msg.message, msg.type)}
                        <div class="text-muted small mt-1">${timeAgo}</div>
                    </div>
                    <div class="clearfix"></div>
                `;
            });

            $('#chat-box').html(messages).scrollTop($('#chat-box')[0].scrollHeight);
        });
    });

    $('#attach-btn').on('click', function () {
        $('#attachment-input').click();
    });

    $('#attachment-input').on('change', function () {
        const file = this.files[0];
        if (file) {
            $('#message-input').val(`📎 ${file.name}`);
        }
    });

    $('#message-form').on('submit', async function (e) {
        e.preventDefault();

        const senderId = $('#sender_id').val();
        const receiverId = $('#receiver_id').val();
        const conversationId = $('#conversation_id').val();
        const messageText = $('#message-input').val().trim();
        const file = $('#attachment-input')[0].files[0];

        let payload = {
            sender_id: senderId,
            receiver_id: receiverId,
            conversation_id: conversationId,
            message: null,
            type: 'text'
        };

        if (file) {
            const formData = new FormData();
            formData.append('file', file);

            const uploadRes = await fetch('/api/upload-chat-file', {
                method: 'POST',
                body: formData
            });

            const uploaded = await uploadRes.json();

            payload.message = uploaded.file_url;
            payload.type = 'file';

            socket.emit('sendMessage', payload);
        } else if (messageText) {
            payload.message = messageText;
            payload.type = 'text';

            socket.emit('sendMessage', payload);
        } else {
            return;
        }

        $('#message-input').val('');
        $('#attachment-input').val('');
    });

    socket.on('receiveMessage', function(data) {

       
        const msg = data.message.message.message;
         const sender_id = data.message.message.sender_id;
        const msgType =data.message.message.type;
        const conversationId = data.message.message.conversation_id;
        const isMe = sender_id == $('#sender_id').val();

        const messageClass = isMe ? 'chat-message chat-right' : 'chat-message chat-left';
        const timeAgo = moment(msg.created_at).fromNow();

        if (parseInt(conversationId) === parseInt($('#conversation_id').val())) {
            const messageHtml = `
                <div class="${messageClass}">
                    ${renderMessageContent(msg, msgType)}
                    <div class="text-muted small mt-1">${timeAgo}</div>
                </div>
                <div class="clearfix"></div>
            `;

            $('#chat-box').append(messageHtml).scrollTop($('#chat-box')[0].scrollHeight);
        } else {
            $(`.conversation-item[data-id="${conversationId}"] .rounded-pill`).show();
        }
    });

    function renderMessageContent(message, type) {
        if (type === 'file') {
            let fileUrl = message;
            let filename = 'Download File';

            if (typeof message === 'object') {
                fileUrl = message.content || message.file_url;
                filename = message.filename || 'Download File';
            }

            const isImage = /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(fileUrl);
            if (isImage) {
                return `<a href="${fileUrl}" target="_blank">
                            <img src="${fileUrl}" alt="Image" style="max-width: 200px; border-radius: 10px;" />
                        </a>`;
            } else {
                return `<a href="${fileUrl}" download target="_blank">${filename}</a>`;
            }
        } else {
            return `<div>${message}</div>`;
        }
    }
});
</script>
@endsection
